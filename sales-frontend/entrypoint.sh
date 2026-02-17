#!/bin/sh
set -e

if [ ! -d "node_modules" ]; then
    echo "node_modules not found, installing dependencies..."
    npm install
fi

exec "$@"
