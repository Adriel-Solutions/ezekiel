#!/bin/bash
read -p "Initial color : " initial_color
read -p "New color : " new_color

find app/views/layouts -type f -name "*.php" -exec sed -i '' -E "s/$initial_color/$new_color/g" {} \;
find app/views/partials -type f -name "*.php" -exec sed -i '' -E "s/$initial_color/$new_color/g" {} \;
find app/views/components -type f -name "*.php" -exec sed -i '' -E "s/$initial_color/$new_color/g" {} \;
find app/views/emails -type f -name "*.php" -exec sed -i '' -E "s/$initial_color/$new_color/g" {} \;
find app/views/pages -type f -name "*.php" -exec sed -i '' -E "s/$initial_color/$new_color/g" {} \;
find app/views/skeletons -type f -name "*.php" -exec sed -i '' -E "s/$initial_color/$new_color/g" {} \;
