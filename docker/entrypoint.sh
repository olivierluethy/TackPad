#!/bin/sh
# Generate the .env file the app expects from the container environment, unless
# one was already provided (e.g. bind-mounted for local development). This lets
# `docker compose up` work with zero manual setup while keeping app code intact.
set -e

ENV_FILE=/var/www/html/.env

if [ ! -f "$ENV_FILE" ]; then
    echo "Generating $ENV_FILE from environment variables..."
    cat > "$ENV_FILE" <<EOF
DB_SERVER=${DB_SERVER:-db}
DB_USERNAME=${DB_USERNAME:-tackpad}
DB_PASSWORD=${DB_PASSWORD:-tackpad}
DB_NAME=${DB_NAME:-TackPad}
ENCRYPTION_KEY=${ENCRYPTION_KEY:-0123456789abcdef0123456789abcdef}
EOF
    chown www-data:www-data "$ENV_FILE"
fi

# Apply any pending database migrations (idempotent). Wait briefly for the DB
# to accept connections first — the web container starts once the DB reports
# healthy, but this adds a small safety margin.
echo "Running database migrations..."
i=0
until php /var/www/html/core/migrate.php || [ "$i" -ge 10 ]; do
    i=$((i + 1))
    echo "  migrate: database not ready yet, retry $i/10..."
    sleep 3
done

exec "$@"
