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

CREATE TABLE player_tbl(\
player_id serial NOT NULL PRIMARY KEY UNIQUE,\
player_name text,
team_name text,
dan text,
rank text);

CREATE TABLE kyudo_tbl(\
id  serial  NOT NULL  PRIMARY KEY,\
datetime  timestamp NOT NULL,\
hit_record  text,\
player_id serial NOT NULL REFERENCES player_tbl (player_id)\
);

CREATE TABLE competition_tbl(\
competition_id serial NOT NULL PRIMARY KEY UNIQUE,\
competition_name text,
competition_type text);

EOF
echo "Finish"