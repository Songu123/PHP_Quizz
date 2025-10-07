@echo off
echo.
echo ================================================
echo   🐳 KHỞI ĐỘNG ỨNG DỤNG BẰNG DOCKER
echo ================================================
echo.

echo 📦 Đang build và khởi động containers...
docker-compose up -d --build

echo.
echo ⏳ Chờ containers khởi động hoàn tất...
timeout /t 10 /nobreak > nul

echo.
echo ✅ Ứng dụng đã sẵn sàng!
echo.
echo 🌐 Truy cập ứng dụng:
echo    ➜ Website: http://localhost:8080
echo    ➜ PHPMyAdmin: http://localhost:8081
echo.
echo 📊 Thông tin database:
echo    ➜ Host: mysql (trong container) / localhost:3307 (từ máy host)
echo    ➜ Database: quizz_loq
echo    ➜ Username: root
echo    ➜ Password: rootpassword
echo.
echo 🔧 Các lệnh hữu ích:
echo    ➜ Xem logs: docker-compose logs -f
echo    ➜ Dừng containers: docker-compose down
echo    ➜ Khởi động lại: docker-compose restart
echo.
pause