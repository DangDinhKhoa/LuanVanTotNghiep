from fastapi import FastAPI, Request
from app.routers import base, file_upload
from fastapi.middleware.cors import CORSMiddleware
from python_rag_llm_layout_banner.demo.llm_generate_question.generate_question_template import GenerateQuestion
from python_rag_llm_layout_banner.demo.llm_generate_question.llm import LLM_GENERATE_QUESTION
from app.config import settings

from openai import OpenAI
from PIL import Image, ImageDraw, ImageFont
from rembg import remove
from io import BytesIO
from requests.exceptions import RequestException
import openai
import requests
from fastapi.responses import RedirectResponse
from fastapi.staticfiles import StaticFiles
from fastapi.responses import JSONResponse
from pathlib import Path
from PIL import Image
import uuid
import asyncio
import aiohttp
import os



# Tạo instance của FastAPI
app = FastAPI()

# Cấu hình CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Cho phép tất cả nguồn (hoặc chỉ định danh sách ["http://example.com"])
    allow_credentials=True,
    allow_methods=["*"],  # Cho phép tất cả phương thức (GET, POST, PUT, DELETE, v.v.)
    allow_headers=["*"],  # Cho phép tất cả headers
)

# Include các router vào ứng dụng chính
# app.include_router(base.router)
# app.include_router(file_upload.router)


# @app.route("/favicon.ico")
# def favicon():
#     return "", 204


# Endpoint URLs 
generation_url = "https://cloud.leonardo.ai/api/rest/v1/generations"
request_url = "https://cloud.leonardo.ai/api/rest/v1/generations/{generation_id}"

# Headers
headers = {
    'accept': 'application/json',
    'authorization': f'Bearer {settings.KEY_API_LEONARDO}',
    'content-type': 'application/json'
}

openai.api_key = settings.KEY_API_GPT

def generate_background_prompt(content, theme):
    """
    Hàm tạo prompt cho Leonardo.ai để vẽ background thuần túy.
    Parameters:
        content (str): Nội dung mô tả nền (ví dụ: màu sắc, cảm giác).
        theme (str): Chủ đề hoặc phong cách của nền.
    
    Returns:
        str: Prompt được tạo để gửi cho Leonardo.ai.
    """
    # Hướng dẫn cụ thể cho GPT-4o-mini
    system_message = (
        "Bạn là một trợ lý tạo prompt cho Leonardo.ai để vẽ background thuần túy. "
        "Dựa trên nội dung và chủ đề mà người dùng cung cấp, hãy tạo một prompt ngắn gọn, chính xác, "
        "mô tả một nền đơn giản, không hoa văn, không văn bản, không vật thể, chỉ có màu sắc hoặc hiệu ứng thuần túy. "
        "Prompt phải phù hợp với phong cách của Leonardo.ai và phản ánh đúng ý người dùng. "
        "Trả về chỉ prompt, không giải thích."
    ) 
    user_message = f"Nội dung: {content}\nChủ đề: {theme}"
    # Gọi API OpenAI
    response = openai.chat.completions.create(
        model="gpt-4o-mini",
        messages=[
            {"role": "system", "content": system_message},
            {"role": "user", "content": user_message}
        ],
        max_tokens=100,
        temperature=0.5
    )
    # Lấy prompt từ phản hồi
    prompt = response.choices[0].message.content.strip()
    return prompt


def generate_image_prompt(content, theme):
    """
    Hàm tạo prompt cho Leonardo.ai dựa trên nội dung và chủ đề.
    
    Parameters:
        content (str): Nội dung mà người dùng muốn vẽ.
        theme (str): Chủ đề hoặc phong cách của bức ảnh.
    
    Returns:
        str: Prompt được tạo để gửi cho Leonardo.ai.
    """
    # Tạo hướng dẫn cho GPT-4o-mini
    system_message = (
        "Bạn là một trợ lý chuyên tạo prompt cho Leonardo.ai. "
        "Dựa trên nội dung và chủ đề mà người dùng cung cấp, hãy tạo một prompt ngắn gọn, rõ ràng, "
        "mô tả một hình ảnh sắc nét, chi tiết cực cao, tập trung hoàn toàn vào đối tượng chính (ví dụ: robot, xe hơi), "
        "với độ tương phản mạnh, đường nét rõ ràng, không có chi tiết thừa ở hậu cảnh. "
        "Hình ảnh phải được tối ưu để giữ nguyên chất lượng sau khi xóa nền, tránh bị mờ hoặc mất chi tiết. "
        "Trả về chỉ prompt, không giải thích."
    )
    user_message = f"Nội dung: {content}\nChủ đề: {theme}"
    # Gọi API OpenAI
    response = openai.chat.completions.create(
        model="gpt-4o-mini",
        messages=[
            {"role": "system", "content": system_message},
            {"role": "user", "content": user_message}
        ],
        max_tokens=150,
        temperature=0.7
    ) 
    # Lấy prompt từ phản hồi
    prompt = response.choices[0].message.content.strip()
    return prompt


def url_to_pil_image(image_url):
    """
    Chuyển đổi ảnh từ URL thành đối tượng PIL.Image.
    """
    try:
        response = requests.get(image_url, timeout=10)  # Timeout 10 giây
        response.raise_for_status()  # Ném lỗi nếu status code không phải 200
        return Image.open(BytesIO(response.content))
    except RequestException as e:
        raise ValueError(f"Lỗi tải ảnh từ URL {image_url}: {e}")


def remove_background(input_image: Image.Image) -> Image.Image:
    """
    Xóa nền từ ảnh đầu vào (PIL.Image) và trả về ảnh đã xử lý (PIL.Image).
    :param input_image: Đối tượng PIL.Image của ảnh cần xóa nền.
    :return: Đối tượng PIL.Image của ảnh đã xóa nền.
    """
    # input_image không hợp lệ
    if not isinstance(input_image, Image.Image):
        raise ValueError("Đầu vào phải là đối tượng PIL.Image")
    try:
        # Chuyển đổi ảnh PIL thành dữ liệu nhị phân (byte)
        img_byte_arr = BytesIO()
        # Định dạng PNG hỗ trợ alpha channel, rất phù hợp cho việc xử lý xóa nền
        input_image.save(img_byte_arr, format='PNG')
        img_byte_arr = img_byte_arr.getvalue()
        # Xóa nền từ dữ liệu nhị phân
        output_data = remove(img_byte_arr)
        # Chuyển đổi dữ liệu đã xử lý thành ảnh PIL và trả về
        output_image = Image.open(BytesIO(output_data))
        return output_image
    except Exception as e:
        raise ValueError(f"Lỗi khi xóa nền: {e}")


def hex_to_rgb(hex_color):
    """
    Chuyển đổi chuỗi màu hexadecimal (ví dụ: #FFFFFF) thành giá trị RGB.
    Parameters:
        hex_color (str): Chuỗi màu hexadecimal, ví dụ: "#FFFFFF".
    Returns:
        tuple: Một tuple (R, G, B).
    """
    # Xử lý chuỗi màu Hex (loại bỏ dấu '#')
    hex_color = hex_color.lstrip('#')
    # Chuyển đổi giá trị hex thành các giá trị RGB
    r, g, b = int(hex_color[0:2], 16), int(hex_color[2:4], 16), int(hex_color[4:6], 16) 
    return (r, g, b)


def draw_text_on_image(title, image_size=(500, 200), font="Roboto_Condensed-Black", text_color="#FFFFFF"):
    """
    Vẽ tiêu đề lên ảnh với màu sắc văn bản là chuỗi hexadecimal.
    Parameters:
        title (str): Tiêu đề cần vẽ.
        image_size (tuple): Kích thước ảnh (width, height).
        font (str): Tên font chữ cần sử dụng.
        text_color (str): Màu sắc của văn bản dưới dạng hexadecimal (ví dụ: "#FFFFFF").
    Returns:
        Image: Hình ảnh đã vẽ tiêu đề.
    """
    # Tạo ảnh trong suốt (RGBA)
    image = Image.new('RGBA', image_size, (0, 0, 0, 0))
    # Tạo đối tượng ImageDraw để vẽ lên ảnh
    draw = ImageDraw.Draw(image)
    # Chuyển đổi màu sắc từ hexadecimal sang RGB
    color_rgb = hex_to_rgb(text_color)
    # Xây dựng đường dẫn tương đối từ thư mục hiện tại của dự án
    # Lấy thư mục gốc của dự án (api_base_public)
    project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))  
    # Tạo đường dẫn đến thư mục Fonts/static từ thư mục gốc của dự án
    font_folder = os.path.join(project_root, 'Fonts', 'static')
    font_path = os.path.join(font_folder, f"{font}.ttf")  # Kết hợp để tạo đường dẫn đầy đủ tới font
    try:
        font = ImageFont.truetype(font_path, 1)  # Dùng size font nhỏ nhất để thử
    except IOError:
        print(f"Font {font} không tìm thấy, sử dụng font mặc định.")
        font = ImageFont.load_default()
    # Tính toán kích thước của văn bản với kích thước font khác nhau
    max_font_size = 100  # Giới hạn font size tối đa
    for font_size in range(max_font_size, 1, -1):  # Duyệt từ font lớn nhất xuống
        font = ImageFont.truetype(font_path, font_size)
        text_bbox = draw.textbbox((0, 0), title, font=font)
        text_width = text_bbox[2] - text_bbox[0]
        text_height = text_bbox[3] - text_bbox[1] 
        # Kiểm tra nếu văn bản vừa với ảnh
        if text_width <= image_size[0] and text_height <= image_size[1]:
            break
    # Vị trí mặc định vẽ ở giữa ảnh
    position = ((image_size[0] - text_width) // 2, (image_size[1] - text_height) // 2) 
    # Vẽ tiêu đề lên ảnh tại vị trí đã chỉ định
    draw.text(position, title, font=font, fill=color_rgb)
    # Trả về ảnh đã được vẽ
    return image


def get_compatible_size(width, height, maxsize=1536, minsize=512):
    # Chỉ cần là số dương
    if width <= 0 or height <= 0:
        raise ValueError("Chiều dài và chiều rộng phải là số dương.")
    # Tính tỷ lệ khung hình của đầu vào
    input_ratio = width / height
    # Danh sách các tỷ lệ khung hình phổ biến của Leonardo.ai
    supported_ratios = {
        "1:1": 1.0,    # Vuông
        "4:3": 4/3,    # Truyền thống
        "3:2": 3/2,    # Phong cảnh/chân dung
        "16:9": 16/9,  # Màn hình rộng
        "2:1": 2.0     # Cinematic
    }
    # Tìm tỷ lệ gần nhất
    closest_ratio_name = min(supported_ratios, key=lambda x: abs(supported_ratios[x] - input_ratio))
    closest_ratio = supported_ratios[closest_ratio_name]
    # Tính kích thước mới dựa trên tỷ lệ gần nhất
    if closest_ratio >= 1:  # Chiều rộng >= chiều cao
        # Bắt đầu với chiều rộng tối thiểu
        new_width = max(width, minsize)
        new_height = int(new_width / closest_ratio)
        # Nếu chiều cao nhỏ hơn minsize, tăng kích thước lên
        if new_height < minsize:
            new_height = minsize
            new_width = int(new_height * closest_ratio)
        # Nếu vượt quá maxsize, giảm xuống
        if new_width > maxsize:
            new_width = maxsize
            new_height = int(new_width / closest_ratio)
        if new_height > maxsize:
            new_height = maxsize
            new_width = int(new_height * closest_ratio)
    else:  # Chiều cao > chiều rộng
        # Bắt đầu với chiều cao tối thiểu
        new_height = max(height, minsize)
        new_width = int(new_height * closest_ratio)
        # Nếu chiều rộng nhỏ hơn minsize, tăng kích thước lên
        if new_width < minsize:
            new_width = minsize
            new_height = int(new_width / closest_ratio)
        # Nếu vượt quá maxsize, giảm xuống
        if new_height > maxsize:
            new_height = maxsize
            new_width = int(new_height * closest_ratio)
        if new_width > maxsize:
            new_width = maxsize
            new_height = int(new_width / closest_ratio)
    # Đảm bảo kích thước là bội số của 8
    new_width = (new_width // 8) * 8
    new_height = (new_height // 8) * 8
    # Kiểm tra và điều chỉnh lại để nằm trong khoảng minsize-maxsize
    new_width = max(minsize, min(new_width, maxsize))
    new_height = max(minsize, min(new_height, maxsize))
    return new_width, new_height


def resize_image(input_image: Image.Image, width: int, height: int) -> Image.Image:
    """
    Thay đổi kích thước của ảnh đầu vào (PIL.Image) theo chiều rộng và chiều cao mới.
    :param input_image: Đối tượng PIL.Image của ảnh cần thay đổi kích thước.
    :param width: Chiều rộng mới của ảnh.
    :param height: Chiều cao mới của ảnh.
    :return: Đối tượng PIL.Image của ảnh đã thay đổi kích thước.
    """
    try:
        # Thay đổi kích thước ảnh theo chiều rộng và chiều cao mới
        resized_img = input_image.resize((width, height))
        # Trả về ảnh đã được chỉnh kích thước
        return resized_img
    except Exception as e:
        print(f"Đã xảy ra lỗi: {e}")
        return None


def overlay_layer_on_background(background, img_layer, position):
    """
    Chồng một layer ảnh lên nền từ đối tượng PIL.Image, tại vị trí chỉ định, sử dụng alpha channel để giữ phần trong suốt khi chồng ảnh.
    Nếu ảnh layer vượt quá kích thước ảnh nền tại vị trí chỉ định, cắt bỏ phần vượt quá.
    :param background: Đối tượng PIL.Image của ảnh nền.
    :param img_layer: Đối tượng PIL.Image của ảnh layer.
    :param position: Vị trí (x, y) để dán ảnh layer lên nền.
    :return: Hình ảnh đã xử lý (nền + layer ảnh).
    """
    # Kiểm tra xem position có phải là tuple (x, y) hay không
    if not isinstance(position, tuple) or len(position) != 2:
        raise ValueError("Vị trí phải là một tuple (x, y)")
    try:
        # Đảm bảo ảnh nền và ảnh layer đều có alpha channel (nếu chưa có)
        background = background.convert("RGBA")
        img_layer = img_layer.convert("RGBA")
        # Kích thước ảnh nền và ảnh layer
        bg_width, bg_height = background.size
        layer_width, layer_height = img_layer.size
        x, y = position
        # Kiểm tra nếu ảnh layer không vượt quá kích thước ảnh nền tại vị trí chỉ định
        if x + layer_width > bg_width or y + layer_height > bg_height:
            # Tính toán phần ảnh layer sẽ được chồng lên nền
            right = min(x + layer_width, bg_width)   # Không vượt quá bên phải ảnh nền
            bottom = min(y + layer_height, bg_height)  # Không vượt quá dưới ảnh nền
            # Cắt phần ảnh layer mà vượt quá ảnh nền (nếu có)
            img_layer = img_layer.crop((0, 0, right - x, bottom - y))
        # Dán ảnh layer lên ảnh nền tại vị trí chỉ định, sử dụng alpha channel để trong suốt
        background.paste(img_layer, position, img_layer)  # Sử dụng alpha channel để trong suốt
        # Trả về ảnh nền đã chồng layer
        return background
    except Exception as e:
        raise ValueError(f"Đã xảy ra lỗi: {e}")


# Hàm tạo hình ảnh bất đồng bộ
async def generate_images_using_leonardo(prompt, width=1472, height=832, num_images=1):
    # Dữ liệu yêu cầu tạo hình ảnh
    data = {
        "modelId": "b2614463-296c-462a-9586-aafdb8f00e36",
        "contrast": 3.5,
        "prompt": prompt,
        "num_images": num_images,
        "width": width,
        "height": height,
        "styleUUID": "111dc692-d470-4eec-b791-3475abac4c46",
        "enhancePrompt": False
    }
    async with aiohttp.ClientSession() as session:
        async with session.post(generation_url, headers=headers, json=data) as response:
            if response.status == 200:
                data = await response.json()
                generation_id = data.get('sdGenerationJob', {}).get('generationId')
                if not generation_id:
                    raise ValueError("Không tìm thấy generationId trong phản hồi API")
                return generation_id
            else:
                raise ValueError(f"Lỗi gửi yêu cầu tạo ảnh: {response.status} - {await response.text()}")


# Hàm lấy hình ảnh đã tạo bất đồng bộ
async def fetch_generated_images(generation_id):
    async with aiohttp.ClientSession() as session:
        async with session.get(request_url.format(generation_id=generation_id), headers=headers) as status_response:
            if status_response.status == 200:
                try:
                    status_data = await status_response.json()
                except ValueError:
                    raise ValueError("Phản hồi API không phải JSON hợp lệ")
                if status_data.get("generations_by_pk", {}).get("status") == "COMPLETE":
                    generated_images = status_data["generations_by_pk"].get("generated_images", [])
                    if generated_images:
                        return [img["url"] for img in generated_images]
                    return []
                return {"status": "pending"}
            else:
                raise ValueError(f"Lỗi lấy hình ảnh: {status_response.status}")


# Hàm chính bất đồng bộ để lấy URLs hình ảnh
async def get_image_urls(prompt, width=1472, height=832, num_images=1, retries=3, delay=50,):
    try:
        # Bước 1: Gửi yêu cầu tạo hình ảnh
        generation_id = await generate_images_using_leonardo(prompt, width, height, num_images)
        if not generation_id:
            raise ValueError("Không thể khởi tạo yêu cầu tạo ảnh")
        for _ in range(retries):  # Kiểm tra trong 3 lần, mỗi lần cách nhau 60 giây
            await asyncio.sleep(delay)  # Chờ 60 giây (hoặc có thể điều chỉnh theo nhu cầu)
            result = await fetch_generated_images(generation_id)
            if isinstance(result, list):
                return result
            elif result.get("status") != "pending":
                raise ValueError("Trạng thái không xác định từ API")
        raise TimeoutError(f"Hình ảnh không được tạo trong {retries * delay} giây")
    except Exception as e:
        raise ValueError(f"Lỗi khi lấy URL hình ảnh: {str(e)}")
    


async def create_banner(banner_layout, banner_quantity, theme):
    """
    Hàm tạo nhiều banner dựa trên dữ liệu layout đã phân tích từ chatbot.
    Parameters:
        banner_layout (dict): Dữ liệu layout của banner được trả về từ analyze_requirements_using_chatbot.
        banner_quantity (int): Số lượng banner cần tạo.
    Returns:
        List[Image]: Danh sách các banner được tạo từ layout (loại trả về là đối tượng Image của thư viện Pillow).
    """
    # Lấy thông tin từ banner_layout
    background = banner_layout.background
    image_components = banner_layout.image
    title = banner_layout.title
    description = banner_layout.description
    # Tạo background 
    prompt_bg = generate_background_prompt(background.prompt, theme)
    width_bg = background.width
    height_bg = background.height
    width_bg_draw, height_bg_draw = get_compatible_size(width_bg, height_bg)
    background_banner = await get_image_urls(prompt_bg, width_bg_draw, height_bg_draw, banner_quantity)
    # Duyệt qua từng background.
    backgrounds = []
    for bg_banner in background_banner:
        bg_banner = url_to_pil_image(bg_banner)
        backgrounds.append(resize_image(bg_banner, width_bg, height_bg))
    # Tạo ảnh chính
    if image_components:
        # Lưu danh sách image_components.
        images = []
        # Nếu không rỗng, lặp qua các phần tử trong image_components.
        for image in image_components:
            # Lưu danh sách các phân tử con của image_components.
            imgs = []
            width = image.width
            height = image.height
            prompt = generate_image_prompt(image.prompt, theme) 
            width_draw, height_draw = get_compatible_size(width, height)
            image_banner = await get_image_urls(prompt, width_draw, height_draw, banner_quantity)
            for img_banner in image_banner:
                img_banner = url_to_pil_image(img_banner)
                image_banner_transparent = remove_background(img_banner)
                image_banner_transparent = resize_image(image_banner_transparent, width, height)
                imgs.append(image_banner_transparent)
            images.append(imgs)
    # Danh sách banner được tạo 
    banners = []
    # Chồng layer. 
    for bg in range(len(backgrounds)):
        banner = backgrounds[bg]
        # Chồng ảnh.
        col = bg
        for row in range(len(images)): 
            x = image_components[row].x
            y = image_components[row].y                  
            banner = overlay_layer_on_background(banner, images[row][col], (x, y))
        # Tạo title
        if title and title.draw == True:
            x = title.x
            y = title.y
            width = title.width
            height = title.height
            text = title.text
            color = title.color
            font = title.font
            title_banner = draw_text_on_image(text, (width, height), font, color)
            banner = overlay_layer_on_background(banner, title_banner, (x, y))
        # Tạo description
        if description and description.draw == True:
            x = description.x
            y = description.y
            width = description.width
            height = description.height
            text = description.text
            color = description.color
            font = description.font
            description_banner = draw_text_on_image(text, (width, height), font, color)
            banner = overlay_layer_on_background(banner, description_banner, (x, y))
        # Thêm banner hoàn chỉnh.
        banners.append(banner)
    # Trả về danh sách banner hoàn chỉnh. 
    return banners



# Định nghĩa thư mục chứa banner
project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
BASE_DIR = os.path.join(project_root, 'banners')
# Cấu hình FastAPI để phục vụ tệp tĩnh từ thư mục banners
app.mount("/banners", StaticFiles(directory=BASE_DIR), name="banners")

# api
@app.post("/generate/banners")
def upload_banners(request: Request):
    llm_generate = GenerateQuestion(LLM_GENERATE_QUESTION().get_llm()).get_chain()
    banner_layout = llm_generate.invoke({
        "width": 800,
        "height": 350,
        "theme": "Technology",
        "user_request": "Tôi muốn tạo một banner về công nghệ cho sự kiện giới thiệu sản phẩm mới. Nền banner nên có màu sắc hiện đại, chủ yếu là xanh dương và bạc, tạo cảm giác tinh tế và chuyên nghiệp. Ở giữa banner là tên sự kiện lớn 'THẾ GIỚI CÔNG NGHỆ' với phông chữ sắc nét và hiện đại. Thêm vào đó, hãy vẽ hình ảnh của các sản phẩm công nghệ mới như smartphone, laptop và thiết bị thông minh, với các hiệu ứng ánh sáng mạnh mẽ và các biểu tượng công nghệ như đám mây và kết nối mạng. Bên trái là hình một con robot. Đảm bảo rằng phong cách thiết kế phải thể hiện sự đổi mới và tiên tiến, thu hút sự chú ý của người xem."
    })
    theme = "Technology"
    banners = asyncio.run(create_banner(banner_layout, 4, theme))
    # Kiểm tra xem danh sách banners có rỗng không
    if not banners:
        return JSONResponse(
            status_code=400,  # Bad Request
            content={"message": "No banners provided"}
        )
    # Lưu banner vào thư mục và tạo URL
    banner_urls = []
    for banner in banners:
        file_name = f"{uuid.uuid4()}.png"
        file_path = os.path.join(BASE_DIR, file_name)
        banner.save(file_path, "PNG")   
        # Tạo URL động cho banner
        banner_url = str(request.url_for("banners", path=file_name))
        banner_urls.append(banner_url)
    return JSONResponse(content=banner_urls)
