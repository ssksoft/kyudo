@echo off
if "%1" == "" goto PARAM_ERROR
scp -r -i C:/Users/sasat/Desktop/kyudo/env/raspbian/.vagrant/machines/default/virtualbox/private_key ../src/* vagrant@%1:/var/www/html/kyudo
exit /B

:PARAM_ERROR
echo "Plese input server ip address"
exit /B