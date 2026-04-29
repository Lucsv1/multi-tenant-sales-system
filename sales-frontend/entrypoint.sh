#!/bin/sh
set -e

printf "VITE_API_URL=%s\nHMR_HOST=%s\nHMR_PORT=%s\n" \
  "${VITE_API_URL}" "${HMR_HOST:-localhost}" "${HMR_PORT:-9000}" > /app/.env

if [ ! -d "node_modules" ]; then
    echo "node_modules not found, installing dependencies..."
    npm install
fi

exec "$@"
