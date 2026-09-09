#!/usr/bin/env bash
set -euo pipefail
PORT="${PORT:-80}"
sed -i "s/listen 80/listen ${PORT}/" /etc/nginx/nginx.conf
exec "$@"
