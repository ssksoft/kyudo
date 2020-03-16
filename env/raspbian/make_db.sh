sudo su - postgres <<EOF
echo "Start procedure about PostgreSQL"
echo "Making role..."
createuser --no-createdb --no-createrole --no-superuser kyudo
echo "ok"

echo "Creating DB..."
createdb -O kyudo -E UTF8 kyudodb
psql -c "ALTER USER kyudo with PASSWORD 'kyudo'";
echo "ok"

echo "Login as kyudo"
echo "Creating TABLE..."
psql -h 127.0.0.1 -U kyudo kyudodb
CREATE TABLE kyudo_tbl(\
id  serial  NOT NULL  PRIMARY KEY,\
datetime  timestamp NOT NULL,\
player_name text,\
hit_record  text\
);
EOF
echo "Finish"