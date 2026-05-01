#!/bin/sh
set -e

# Note: composer install and config caching should be in Dockerfile, not here
# This script just starts the process manager

echo "=== Starting Supervisor ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf