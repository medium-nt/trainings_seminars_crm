#!/bin/bash

set -e

RED="\e[31m"
GREEN="\e[32m"
YELLOW="\e[33m"
BLUE="\e[34m"
MAGENTA="\e[35m"
CYAN="\e[36m"
RESET="\e[0m"

block() {
    echo -e "${MAGENTA}\n===== $1 =====${RESET}"
}

echo -e "${CYAN}=== НАЧАЛО ДЕПЛОЯ ===${RESET}"
echo -e "${BLUE}Текущая директория:${RESET} $(pwd)"
echo -e "${BLUE}Время:${RESET} $(date)"

block "ОБНОВЛЕНИЕ КОДА (git pull)"
git pull origin main

block "УСТАНОВКА ЗАВИСИМОСТЕЙ (composer)"
/opt/php/8.4/bin/php composer.phar install --no-interaction --prefer-dist --optimize-autoloader --no-dev

block "ОЧИСТКА КЕШЕЙ LARAVEL"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

block "ПЕРЕСБОРКА КЕШЕЙ LARAVEL"
php artisan config:cache
php artisan route:cache
php artisan view:cache

block "ОПТИМИЗАЦИЯ"
php artisan optimize

echo -e "${GREEN}\n=== ДЕПЛОЙ ЗАВЕРШЁН ===${RESET}"
echo -e "${BLUE}Время:${RESET} $(date)"
