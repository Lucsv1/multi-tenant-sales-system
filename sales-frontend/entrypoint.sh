#!/bin/sh
set -e

echo "VITE_API_URL=${VITE_API_URL}" > /app/.env

if [ ! -d "node_modules" ]; then
    echo "node_modules not found, installing dependencies..."
    npm install
fi

exec "$@"
