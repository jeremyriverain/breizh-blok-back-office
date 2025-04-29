#!/bin/sh

set -e

composer install
composer run fixtures
npm install
npm run build