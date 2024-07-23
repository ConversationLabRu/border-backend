#!/bin/bash

if [ "$1" == "production" ]; then
    cp .env.production .env
    echo "Switched to production environment"
elif [ "$1" == "development" ]; then
    cp .env.dev .env
    echo "Switched to development environment"
else
    echo "Usage: ./switch-env.sh [production|development]"
fi
