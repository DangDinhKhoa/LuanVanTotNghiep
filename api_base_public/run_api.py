import subprocess

# Chạy ứng dụng FastAPI
if __name__ == "__main__":
    subprocess.run(["uvicorn", "app.main:app", "--host", "127.0.0.1", "--port", "8000", "--workers", "1"])