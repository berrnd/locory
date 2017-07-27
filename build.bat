set projectPath=%~dp0
if %projectPath:~-1%==\ set projectPath=%projectPath:~0,-1%

set releasePath=%projectPath%\.release
mkdir "%releasePath%"

for /f "tokens=*" %%a in ('type version.txt') do set version=%%a

del "%releasePath%\LOCH_%version%.zip"
"build_tools\7za.exe" a -r "%releasePath%\LOCH_%version%.zip" "%projectPath%\*" -xr!.* -xr!build_tools -xr!build.bat -xr!composer.json -xr!composer.lock -xr!composer.phar -xr!LOCH.phpproj -xr!LOCH.phpproj.user -xr!LOCH.sln -xr!bower.json -xr!publication_assets 
"build_tools\7za.exe" a -r "%releasePath%\LOCH_%version%.zip" "%projectPath%\.htaccess"
"build_tools\7za.exe" d "%releasePath%\LOCH_%version%.zip" data\*.* data\sessions
