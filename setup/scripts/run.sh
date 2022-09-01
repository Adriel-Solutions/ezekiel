docker-compose -f docker-compose.dev.yml down
docker-compose -f docker-compose.dev.yml up -d
./setup/scripts/install-dependencies.sh
./setup/scripts/create-new-env.sh
./setup/scripts/reset-db.sh
./setup/scripts/run-db-migrations.sh
