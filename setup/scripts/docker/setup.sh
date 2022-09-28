docker-compose -f docker-compose.dev.yml down

if [[ -z $1 || $1 != "--no-random" ]]; then
    # Generate new names for the containers
    random_prefix="$(echo $RANDOM | base64 | head -c 20 | tr -d '=')"
    containers=""
    containers="${containers} db"
    containers="${containers} web"
    containers="${containers} fpm"

    for container in ${containers}; do
        # container_name: "ezekiel-xxx-db"
        sed -i '' -E "s/\".+-$container\"/\"ezekiel-$random_prefix-$container\"/g" docker-compose.dev.yml

        # depends_on:\n- xxx-db
        # sed -i '' -E "s/- .+-$container/- $random_prefix-$container/g" docker-compose.dev.yml
    done
fi

docker-compose -f docker-compose.dev.yml up -d
./ezekiel install:dependencies
./setup/scripts/local/create-new-env.sh
./setup/scripts/docker/reset-db.sh
./setup/scripts/docker/run-db-migrations.sh
