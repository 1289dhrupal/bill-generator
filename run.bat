@echo off
echo ========================================
echo Report Generator Setup and Runner
echo ========================================

REM Check if the virtual environment folder exists
if not exist ".venv" (
    echo Creating Python virtual environment...
    python -m venv .venv
    if %errorlevel% neq 0 (
        echo Error: Failed to create virtual environment. Make sure Python is installed and added to PATH.
        pause
        exit /b 1
    )
) ELSE (
    echo Virtual environment already exists. Skipping creation...
)

REM Activate the virtual environment
echo Activating virtual environment...
call .venv\Scripts\activate.bat

REM Install the required Python packages
echo Installing dependencies from requirements.txt...
pip install -r requirements.txt

REM Install Playwright browser dependencies
echo Checking/Installing Chromium for Playwright...
playwright install chromium

REM Run the main Python script
echo.
echo Running generate_reports.py...
echo ========================================
python generate_reports.py
echo ========================================

echo.
echo Process complete.
pause