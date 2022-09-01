name=$(cat docker-compose.dev.yml | grep -E "container_name: .*-fpm" | sed -E 's/container_name: "(.+)"$/\1/' | tr -d ' ')
docker exec -w /app/setup/scripts -it $name ../../app/dependencies/bin/psalm --no-progress
