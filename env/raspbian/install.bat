for /F "delims== tokens=1,2" %%i in (kyudo.conf) do (
  set %%i=%%j
)
scp -i .vagrant/machines/default/virtualbox/private_key ^
chmod_www.sh ^
install.sh ^
make_db.sh ^
pg_hba.conf ^
u_psql.sh ^
vagrant@%server_ip%:/home/vagrant/
vagrant ssh