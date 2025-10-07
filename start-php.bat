@echo off
cls
echo.
echo ================================================
echo   🚀 KHỞI ĐỘNG ỨNG DỤNG PHP (BUILT-IN SERVER)
echo ================================================
echo.

echo 🔧 Kiểm tra PHP...
php --version > nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ❌ PHP chưa được cài đặt hoặc chưa có trong PATH
    echo    Vui lòng cài đặt PHP từ: https://www.php.net/downloads
    pause
    exit /b 1
)

echo ✅ PHP đã sẵn sàng
echo.

echo 📍 Thư mục hiện tại: %CD%
echo 📁 Document root: %CD%\public
echo.

echo 🌐 Khởi động web server...
echo    ➜ URL: http://localhost:8000
echo    ➜ Nhấn Ctrl+C để dừng server
echo.

cd public
start http://localhost:8000
php -S localhost:8000