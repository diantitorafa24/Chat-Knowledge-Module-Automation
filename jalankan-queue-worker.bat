@echo off
title CIKA Queue Worker
color 0A
echo ============================================
echo   CIKA Queue Worker - Auto Processing
echo ============================================
echo.
echo [INFO] Menjalankan Queue Worker...
echo [INFO] Tekan CTRL+C untuk menghentikan.
echo.

:loop
"C:\xampp\php\php.exe" "C:\xampp\htdocs\cika\web-admin\artisan" queue:work --timeout=1200 --tries=1 --sleep=3
echo.
echo [WARN] Worker berhenti, restart ulang dalam 5 detik...
timeout /t 5 /nobreak >nul
goto loop
