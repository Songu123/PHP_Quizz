@echo off
echo.
echo ================================================
echo   🛑 DỪNG CONTAINERS DOCKER
echo ================================================
echo.

echo 📦 Đang dừng containers...
docker-compose down

echo.
echo ✅ Đã dừng tất cả containers!
echo.
pause