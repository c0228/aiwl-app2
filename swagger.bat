@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/vendor/zircote/swagger-php/bin/swagger
SET COMPOSER_RUNTIME_BIN_DIR=%~dp0/controllers
php "%BIN_TARGET%" "%COMPOSER_RUNTIME_BIN_DIR%"
