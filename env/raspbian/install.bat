
if "%1" == "" goto PARAM_ERROR
scp -i .vagrant/machines/default/virtualbox/private_key ^
chmod_www.sh ^
install.sh ^
make_db.sh ^
pg_hba.conf ^
vagrant@%1:/home/vagrant/
vagrant ssh

:PARAM_ERROR
echo "Plese input server ip address"