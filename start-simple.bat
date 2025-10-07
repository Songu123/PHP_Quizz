@echo off
cls
echo.
echo ================================================
echo   🐳 KHỞI ĐỘNG ỨNG DỤNG PHP BẰNG DOCKER
echo ================================================
echo.

echo 🔧 Dọn dẹp containers cũ...
docker-compose -f docker-compose.simple.yml down > nul 2>&1

echo 📦 Đang build và khởi động containers...
docker-compose -f docker-compose.simple.yml up -d --build

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ⏳ Chờ services khởi động...
    timeout /t 15 /nobreak > nul
    
    echo.
    echo ✅ ỨNG DỤNG ĐÃ SẴNG SÀNG!
    echo.
    echo 🌐 Truy cập ứng dụng:
    echo    ➜ Website: http://localhost:8080
    echo    ➜ PHPMyAdmin: http://localhost:8081
    echo.
    echo 📊 Database Info:
    echo    ➜ Host: localhost:3307
    echo    ➜ Database: quizz_loq
    echo    ➜ Username: root
    echo    ➜ Password: rootpassword
    echo.
    echo 🔧 Lệnh hữu ích:
    echo    ➜ Xem logs: docker-compose -f docker-compose.simple.yml logs -f
    echo    ➜ Dừng: docker-compose -f docker-compose.simple.yml down
    echo.
    
    choice /c YN /m "Bạn có muốn mở website trong browser không? (Y/N)"
    if !errorlevel!==1 (
        start http://localhost:8080
    )
) else (
    echo.
    echo ❌ CÓ LỖI XẢY RA!
    echo    Vui lòng kiểm tra Docker Desktop đã được cài đặt và khởi động
    echo.
)

pause