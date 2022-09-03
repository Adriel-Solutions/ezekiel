#!/bin/bash
read -p "Initial color : " initial_color
read -p "New color : " new_color

DIRS=""
DIRS="${DIRS} app/views/layouts"
DIRS="${DIRS} app/views/partials"
DIRS="${DIRS} app/views/components"
DIRS="${DIRS} app/views/emails"
DIRS="${DIRS} app/views/pages"
DIRS="${DIRS} app/views/skeletons"
DIRS="${DIRS} app/modules/**/views"

for SRC in ${DIRS}; do
    find $SRC -type f -name "*.php" -exec sed -i '' -E "s/-$initial_color-/-$new_color-/g" {} \;
done
