@echo off
echo Building Laravel API Docker image...

REM Stop existing containers
docker-compose down

REM Build the image
docker-compose build --no-cache

REM Copy .env for Docker
copy .env .env.docker

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
