#!/bin/bash

# Variables
dir="app/translations"
tmp=$(mktemp)
should_append_translation=false

# CTRL-C redirect
quit() {
    echo ""
    echo "--"
    echo "Exited."
    echo ""
    rm -f $tmp
    exit
}
trap quit SIGINT
trap quit SIGTERM

# Ask Y/N question
confirm() {
    read -p "$1" response
    case $response in
        [yY][eE][sS]|[yY])
            confirmed=true;;
        *)
            confirmed=false;;
    esac
    echo $confirmed
}

# Array to string
join () {
  local IFS="$1"
  shift
  echo "$*"
}

# Entrypoint
echo "--"

echo "Let's create a new translation"

echo "--"

echo "Gathering translation calls"

echo "--"

translations=$(find ./app/views/ -type f -exec grep -E "__\('.+'\)" -o {} \; | sed -E "s/__\('(.+)'\)/\1/g")
translations_count="$( echo "$translations" | wc -l | tr -d ' ')"

echo "$translations_count translations found"

echo "--"

read -p "What is the code for this new translation ? (xx-YY) " translation_code

if [[ -z $translation_code ]]; then
    exit
fi

output="$translation_code.php"

# Prevent overriding existing translation
if [ -f "$dir/$output" ]; then
    echo "--"
    echo "This translation exists already"
    quit
fi

echo "--"

# Build PHP translation lines (associative array key<->value list)
new_translations=("\"$translation_code\" => \"\"")
while IFS= read -r line; do
    new_translations+=("\"$line\" => \"\"")
done <<< "$translations"

# Build PHP Translation File content
new_translation_file_content=$(cat <<- EOF
    <?php
        I18n::append_translation('$translation_code',
            [
                $(
                    for t in "${new_translations[@]}"; do 
                        printf "$t,\n\t\t\t\t";
                    done
                )
            ]
        );
EOF
)

# Store new translation into a file
echo "$new_translation_file_content" | sed '/^[[:space:]]*$/d' > "$dir/$output"
# has_confirmed=$(confirm "Should we automatically add this translation to the loaded ones ? (Y/N) ")
# should_append_translation=$has_confirmed

# if [[ $should_append_translation = true ]]; then
# fi

# echo "--"

# echo "Alright, your new "

# echo "--"

