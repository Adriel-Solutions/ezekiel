name=$(cat docker-compose.dev.yml | grep -E "container_name: .*-db" | sed -E 's/container_name: "(.+)"$/\1/' | tr -d ' ')
docker exec -it --user postgres $name psql -c 'DROP DATABASE project;'
docker exec -it --user postgres $name psql -f /docker-entrypoint-initdb.d/init.sql
