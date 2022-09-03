# Shutdown the containers
docker-compose -f docker-compose.dev.yml down

# Generate new names for the containers
random_prefix="$(echo $RANDOM | base64 | head -c 20 | tr -d '=')"
containers=""
containers="${containers} db"
containers="${containers} web"
containers="${containers} fpm"

for container in ${containers}; do
    # container_name: "xxx-db"
    sed -i '' -E "s/\".+-$container\"/\"$random_prefix-$container\"/g" docker-compose.dev.yml

    # depends_on:\n- xxx-db
    # sed -i '' -E "s/- .+-$container/- $random_prefix-$container/g" docker-compose.dev.yml
done

# Restart everything brand new
docker-compose -f docker-compose.dev.yml up -d

# Set up database and dependencies
./setup/scripts/install-dependencies.sh
./setup/scripts/reset-db.sh
./setup/scripts/run-db-migrations.sh

