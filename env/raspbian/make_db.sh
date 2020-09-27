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
competition_id serial NOT NULL REFERENCES competition_tbl (competition_id),\
player_name text,\
team_name text,\
dan text,\
rank text\
);

CREATE TABLE kyudo_tbl(\
record_id  serial  NOT NULL  PRIMARY KEY,\
competition_id serial NOT NULL REFERENCES competition_tbl(competition_id),\
match_id serial NOT NULL REFERENCES match_tbl(match_id),\
range   text,\
shoot_order  text,\
player_id serial NOT NULL REFERENCES player_tbl (player_id),\
hit_record  text,\
);

CREATE TABLE match_tbl(\
match_id serial NOT NULL PRIMARY KEY UNIQUE,\
match_name text,\
competition_id serial NOT NULL REFERENCES competition_tbl (competition_id)\
);

CREATE TABLE competition_tbl(\
competition_id serial NOT NULL PRIMARY KEY UNIQUE,\
competition_name text,\
competition_type text);

EOF
echo "Finish"