name=$(cat docker-compose.dev.yml | grep -E "container_name: .*-fpm" | sed -E 's/container_name: "(.+)"$/\1/' | tr -d ' ')
docker exec -it $name php /app/native/scripts/run-db-migrations.php