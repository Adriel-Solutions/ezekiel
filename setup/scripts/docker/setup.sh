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
    done
fi

# Generate random port for the web container to prevent collision
random_port=$(jot -r 1 2000 3000)
sed -i '' -E "/ports/{N;s/- [0-9]+\:80/- $random_port:80/;}" docker-compose.dev.yml

docker-compose -f docker-compose.dev.yml up -d
composer install
./setup/scripts/local/create-new-env.sh
./ezekiel docker:reset
./ezekiel docker:migrations
