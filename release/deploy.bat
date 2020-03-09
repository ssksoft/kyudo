for /F "delims== tokens=1,2" %%i in (kyudo.conf) do (
  set %%i=%%j
)

scp -r -i C:/Users/sasat/Desktop/kyudo/env/raspbian/.vagrant/machines/default/virtualbox/private_key ../src/* vagrant@%server_ip%:/var/www/html/kyudo