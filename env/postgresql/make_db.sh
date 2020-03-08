sudo su
su - postgres
createuser --no-createdb --no-createrole --no-superuser kyudo
createdb -O kyudo -E UTF8 kyudodb
psql -c "ALTER USER todo with PASSWORD 'kyudo'";

psql kyudodb -c "CREATE TALE kyudo_tbl(
  id  serial  NOT NULL  PRIMARY KEY,
  datetime  timestamp NOT NULL,
  player_name text,
  hit_record  text
  );
  CREATE TABLE"
