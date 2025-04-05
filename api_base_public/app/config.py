# File cấu hình chung cho ứng dụng

import os
from dotenv import load_dotenv

# Load các biến môi trường từ file .env
load_dotenv()


class Settings:
    # SETTING
    DIR_ROOT = os.path.dirname(os.path.abspath(".env"))
    # API KEY
    API_KEY = os.environ["API_KEY"]
    KEY_API_GPT = os.getenv("KEY_API_GPT")
    KEY_API_LEONARDO = os.getenv("KEY_API_LEONARDO")
    NUM_DOC = int(os.getenv("NUM_DOC", "3"))
    LLM_NAME = os.getenv("LLM_NAME")
    OPENAI_LLM = os.getenv("OPENAI_LLM")



settings = Settings()
