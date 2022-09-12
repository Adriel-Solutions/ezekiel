#!/bin/bash

cd configuration
cp .custom.env sample.env

# Database-related (DB_X=Y -> DB_X=X)
sed -i '' -E 's/^(DB_([a-zA-Z_]+))=".+"$/\1="\2"/g' sample.env

# SMTP-related (SMTP_X=Y -> SMTP_X=X)
sed -i '' -E 's/^(SMTP_([a-zA-Z_]+))=".+"$/\1="\2"/g' sample.env

# Numeric (XX.X.X => "NUMERIC")
sed -i '' -E 's/[[:digit:]]((\.[[:digit:]]+)+)?/NUMERIC/g' sample.env

# Booleans (FALSE|TRUE|ENABLED -> BOOLEAN)
sed -i '' -E 's/TRUE|FALSE/BOOLEAN/g' sample.env

# Keys (XX -> KEY)
sed -i '' -E 's/^([a-zA-Z_]+_KEY)=".+"$/\1="KEY"/g' sample.env
