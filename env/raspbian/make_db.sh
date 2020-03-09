sudo su - postgres <<EOF

echo "Making role..."
createuser --no-createdb --no-createrole --no-superuser kyudo

echo "Creating DB..."
createdb -O kyudo -E UTF8 kyudodb
psql -c "ALTER USER kyudo with PASSWORD 'kyudo'";

echo "Creating TABLE..."
psql kyudodb -c "CREATE TABLE kyudo_tbl(\
  id  serial  NOT NULL  PRIMARY KEY,\
  datetime  timestamp NOT NULL,\
  player_name text,\
  hit_record  text\
  );"
EOF