sh chmod_www.sh
sudo cp pg_hba.conf /etc/postgresql/11/main/
sh make_db.sh
mkdir /var/www/html/kyudo
sudo reboot