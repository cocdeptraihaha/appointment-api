@echo off
echo Starting Laravel API with Docker...

REM Start containers
docker-compose up -d

echo.
echo Docker containers started!
echo.
echo API: http://localhost:8000
echo phpMyAdmin: http://localhost:8080
echo.
echo To view logs: docker-compose logs -f
echo To stop: docker-compose down
echo.
pause
