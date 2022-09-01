#!/bin/bash

# Variables

# Path - Where to find the configuration folder
dir="configuration"

# File name - Default name for sample dotenv file
input="sample.env"

# File name - Default name for newly created dotenv file
output=".custom.env"

# Temp file - The new dotenv file before it's actually saved on disk in the config folder
tmp=$(mktemp)

# Boolean - Overwrite new existing dotenv file after script if name taken already
should_overwrite=false

# Boolean - Preserve comments in newly created dotenv file is mode is S
should_preserve_comments=true

# Boolean - Update references to the dotenv file after script
should_update_references=false

# S -> Sample file as source | C -> Source code as source
mode="S"

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

# Entrypoint
echo "--"

echo "Let's configure your dotenv configuration"

echo "--"

read -p "Should we determine variables based on code (C) or sample (S) ? (S) : " new_mode

if [[ ! -z $new_mode ]]; then
    mode=$new_mode
fi

if [[ $mode == "S" ]]; then
    echo "--"
    echo "Gathering env sample"

    if [ ! -d "$dir" ]; then
        echo "Missing $dir directory"
        quit
    fi

    if [ ! -f "$dir/$input" ]; then
        echo "Missing $input file"
        quit
    fi
fi


echo "--"

read -p "How to name the new configuration file ? ($output) : " new_output

if [[ ! -z $new_output ]]; then
    output=$new_output
fi

if [ -f "$dir/$output" ]; then
    echo "--"
    has_confirmed=$(confirm "Should we overwrite the existing file ? (Y/N) ")
    if [[ $has_confirmed = true ]]; then
        should_overwrite=true
    else
        quit
    fi
else
    echo "--"
    has_confirmed=$(confirm "Should we update references to the env file across the project? (Y/N) ")
    should_update_references=$has_confirmed
fi

if [[ $mode == "S" ]]; then
    echo "--"
    has_confirmed=$(confirm "Should we preserve comments (if any) ? (Y/N) ")
    should_preserve_comments=$has_confirmed
fi

echo "--"

echo "Alright, let's fill your new configuration"

echo "--"

if [[ $mode == "S" ]]; then
    while IFS= read -r line; do
        # Skip comment lines if ever needed
        if [[ $line =~ ^"#" ]]; then
            if [[ "$should_preserve_comments" = true ]]; then
                echo "" >> $tmp
                echo $line >> $tmp
            fi
            continue
        fi

        # Skip empty lines
        if [[ -z $line ]]; then
            continue
        fi

        # Get key (each line is in the form of KEY="VALUE")
        key=$(echo $line | cut -d "=" -f 1)

        # Get previous value / indication
        indication=$(echo $line | cut -d "=" -f 2 | tr -d '"')

        # Prompt for new value
        read -p "$key ($indication) = " value </dev/tty

        # Set value = indication when nothing's given (default assign)
        if [[ -z $value ]]; then
            value=$indication
        fi

        # Store the value in the new env file
        echo "$key=\"$value\"" >> $tmp
    done < "$dir/$input"
fi

if [[ $mode == "C" ]]; then
    variables=$(find ./app -type f ! -path "*app/dependencies*" -name "*.php" -exec cat {} \; | grep -oE "Options::get\('[A-Z_]+'\)" | sed -E "s/.*\('(.+)'\)/\1/g")
    while IFS= read -r line; do
        # Get key (each line is in the form of ^KEY$)
        key=$line

        # Prompt for new value
        read -p "$key = " value </dev/tty

        # Store the value in the new env file
        echo "$key=\"$value\"" >> $tmp
    done <<< "$variables"
fi

echo "--"
echo "Saving on disk."

rm -f "$dir/$output"
mv $tmp "$dir/$output"
rm -f $tmp

echo "--"
echo "New env file filled and written on disk : $dir/$output"

if [[ $should_update_references = true ]]; then
    echo "--"
    echo "Updating references to the env file"

    sed -i '' -E "s/[a-zA-Z.]+.env/$output/g" native/index.php
    sed -i '' -E "s/[a-zA-Z.]+.env/$output/g" native/bin/scheduler.php
    for f in native/scripts/*.php;
    do
        sed -i '' -E "s/[a-zA-Z.]+.env/$output/g" $f
    done

    echo "--"
    echo "References to the env file updated across the project"
fi

echo "--"
echo "Congratulations!"
echo "--"
echo ""
