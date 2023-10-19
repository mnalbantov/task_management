#!/bin/bash

PROJECT_PATH=$(pwd)

docker info > /dev/null 2>&1
# Ensure that Docker is running...
if [ $? -ne 0 ]; then
    echo "Docker is not running."

    exit 1
fi
cd "$PROJECT_PATH" || exit
echo "Starting the containers"
docker compose up -d

if [ -f .env ]; then
    echo ".env file already exists."
else
    cp .env.dist .env
    cp .env.dist .env.local
    cp .env.dist .env.test
    echo ".env file created from .env.example."
fi