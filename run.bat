@echo off
setlocal enabledelayedexpansion

cd /d "%~dp0"

:agreement
cls
color 0b
echo.
echo.
echo   __     ___     _            _____      _            
echo   \ \   / (_) __^| ^| ___  ___ ^|_   _^|   _^| ^|__   ___ 
echo    \ \ / /^| ^|/ _` ^|/ _ \/ _ \  ^| ^|^| ^| ^| ^| '_ \ / _ \
echo     \ V / ^| ^| (_^| ^|  __/ (_) ^| ^| ^|^| ^|_^| ^| ^|_) ^|  __/
echo      \_/  ^|_^|\__,_^|\___^|\___/  ^|_^| \__,_^|_.__/ \___^|
echo.
echo  =================================================================
echo  ^|^|                                                             ^|^|
echo  ^|^|   [!] SYSTEM LICENCYJNY VIDEO-TUBE                           ^|^|
echo  ^|^|   AUTOR: Dmytro Kyrulenko nr 14 ^| Teb Edukacja Technikum    ^|^|
echo  ^|^|   -------------------------------------------------------   ^|^|
echo  ^|^|                                                             ^|^|
echo  ^|^|   1. PRAWNA OCHRONA AUTORA: Zgodnie z polskim prawem        ^|^|
echo  ^|^|      autorskim oraz Konstytucja RP, autor dostarcza         ^|^|
echo  ^|^|      narzedzie w stanie "as is" (takim, jakie jest).        ^|^|
echo  ^|^|                                                             ^|^|
echo  ^|^|   2. BRAK MODERACJI TRESCI: Autor projektu nie pelni roli   ^|^|
echo  ^|^|      administratora danych ani moderatora. Wszelkie pliki   ^|^|
echo  ^|^|      znajdujace sie w folderze 'uploads' sa tam umieszczone ^|^|
echo  ^|^|      przez uzytkownika koncowego na jego wlasne ryzyko.     ^|^|
echo  ^|^|                                                             ^|^|
echo  ^|^|   3. WYLACZENIE ODPOWIEDZIALNOSCI: Na podstawie art. 415    ^|^|
echo  ^|^|      Kodeksu Cywilnego, autor nie ponosi odpowiedzialnosci  ^|^|
echo  ^|^|      za jakiekolwiek tresci (w tym pornograficzne,          ^|^|
echo  ^|^|      nielegalne) wyswietlane w aplikacji.                   ^|^|
echo  ^|^|                                                             ^|^|
echo  ^|^|   4. ZASADA LOKALNOSCI: System dziala wylacznie w sieci     ^|^|
echo  ^|^|      lokalnej. Kazdy akt naruszenia prawa przez uzytkownika ^|^|
echo  ^|^|      jest jego indywidualnym czynam, za ktory autor projektu^|^|
echo  ^|^|      nie moze zostac pociagniety do odpowiedzialnosci.      ^|^|
echo  ^|^|                                                             ^|^|
echo  ^|^|   5. All rights security (c) 2026. Dmytro Kyrulenko nr 14   ^|^|
echo  ^|^|                                                             ^|^|
echo  =================================================================
echo.
echo.
echo         [1] AKCEPTUJE I KONTYNUUJE      [0] ODRZUCAM I WYCHODZE
echo.
echo  =================================================================
echo.

set /p user_choice=" [SYSTEM] > "

if "%user_choice%"=="0" (
    echo.
    color 0c
    echo  [-] ODRZUCONO. SYSTEM ZOSTANIE ZAMKNIETY...
    timeout /t 2 >nul
    exit /b
)

if not "%user_choice%"=="1" (
    echo.
    color 0e
    echo  [?] BLAD WYBORU. PONAWIANIE...
    timeout /t 1 >nul
    goto :agreement
)

cls
color 0a
echo.
echo  [*] STARTOWANIE MODULOW...
echo  [*] LOKALIZOWANIE PHP...

:: 1. Sprawdzanie w zmiennych systemowych PATH
for %%X in (php.exe) do (set "PHP_PATH=%%~$PATH:X")
if defined PHP_PATH goto :run_server

:: 2. Sprawdzanie popularnych folderow (szybkie)
for %%D in (
    "C:\xampp\php\php.exe"
    "C:\php\php.exe"
    "C:\Program Files\PHP\php.exe"
    "C:\Program Files (x86)\PHP\php.exe"
    "C:\tools\php\php.exe"
) do (
    if exist %%D (
        set "PHP_PATH=%%~D"
        goto :run_server
    )
)

:: Sprawdzanie WAMP / Laragon (wersjonowane foldery)
for /d %%D in ("C:\wamp64\bin\php\php*") do (
    if exist "%%D\php.exe" (
        set "PHP_PATH=%%D\php.exe"
        goto :run_server
    )
)
for /d %%D in ("C:\wamp\bin\php\php*") do (
    if exist "%%D\php.exe" (
        set "PHP_PATH=%%D\php.exe"
        goto :run_server
    )
)
for /d %%D in ("C:\laragon\bin\php\php*") do (
    if exist "%%D\php.exe" (
        set "PHP_PATH=%%D\php.exe"
        goto :run_server
    )
)

:: 3. Glebokie skanowanie dysku (jesli wczesniejsze zawiodlo)
color 0e
echo  [!] PHP NIE ODNALEZIONO W SCIEZKACH DOMYSLNYCH.
echo  [!] ROZPOCZYNAM SKANOWANIE SYSTEMOWE (C:)...
for /f "delims=" %%F in ('dir /s /b /a-d C:\php.exe 2^>nul') do (
    set "PHP_PATH=%%F"
    goto :run_server
)

echo  [!] SZUKAM NA DYSKU D:...
for /f "delims=" %%F in ('dir /s /b /a-d D:\php.exe 2^>nul') do (
    set "PHP_PATH=%%F"
    goto :run_server
)

echo.
color 0c
echo  [X] KRYTYCZNY BLAD: PHP NIE JEST ZAINSTALOWANY!
echo.
pause
exit /b

:run_server
cls
color 0b
echo.
echo  [+] PHP ZLOKALIZOWANO: "!PHP_PATH!"
echo.
echo  =================================================================
echo  ^|^|                                                             ^|^|
echo  ^|^|          VIDEO-TUBE SERVER STATUS: [ ONLINE ]               ^|^|
echo  ^|^|          ACCESS: http://localhost:8000                      ^|^|
echo  ^|^|                                                             ^|^|
echo  =================================================================
echo.
echo  [i] OPTYMALIZACJA PARAMETROW PHP DLA DUZYCH PLIKOW...
echo.
"!PHP_PATH!" -S localhost:8000 -d post_max_size=2048M -d upload_max_filesize=2048M -d memory_limit=256M
pause
