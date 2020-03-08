sudo su - postgres <<EOF

echo "Make role"
createuser --no-createdb --no-createrole --no-superuser kyudo

echo "Create DB"
createdb -O kyudo -E UTF8 kyudodb
psql -c "ALTER USER kyudo with PASSWORD 'kyudo'";

echo "Create TABLE"
psql kyudodb -c "CREATE TABLE kyudo_tbl(\
  id  serial  NOT NULL  PRIMARY KEY,\
  datetime  timestamp NOT NULL,\
  player_name text,\
  hit_record  text\
  );"
EOF