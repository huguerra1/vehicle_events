#!/bin/sh
set -e

if [ ! -f .env ]; then
    echo "Copiando .env.example para .env..."
    cp .env.example .env
fi

if [ -z "${APP_KEY:-}" ] && ! grep -Eq "^APP_KEY=[^[:space:]]+" .env; then
    echo "Gerando APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

echo "Aguardando banco de dados em ${DB_HOST:-db}:${DB_PORT:-5432}..."
until php -r '
    $host = getenv("DB_HOST") ?: "db";
    $port = getenv("DB_PORT") ?: "5432";
    $db   = getenv("DB_DATABASE") ?: "vehicle_events";
    $user = getenv("DB_USERNAME") ?: "vehicle_events";
    $pass = getenv("DB_PASSWORD") ?: "secret";
    try {
        new PDO("pgsql:host={$host};port={$port};dbname={$db}", $user, $pass);
        exit(0);
    } catch (Throwable $e) {
        exit(1);
    }
' 2>/dev/null; do
    sleep 1
done
echo "Banco de dados disponível!"

echo "Executando migrations..."
php artisan migrate --force --no-interaction

if [ ! -f storage/oauth-private.key ] || [ ! -f storage/oauth-public.key ]; then
    echo "Gerando chaves do Passport..."
    php artisan passport:keys --no-interaction
fi

HAS_PERSONAL_CLIENT=$(php -r '
    require "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    if (!Schema::hasTable("oauth_clients")) {
        echo "0";
        exit(0);
    }
    $exists = Schema::hasColumn("oauth_clients", "grant_types")
        ? DB::table("oauth_clients")->where("grant_types", "like", "%personal_access%")->exists()
        : (Schema::hasColumn("oauth_clients", "personal_access_client")
            ? DB::table("oauth_clients")->where("personal_access_client", true)->exists()
            : DB::table("oauth_clients")->exists());
    echo $exists ? "1" : "0";
')

if [ "$HAS_PERSONAL_CLIENT" = "0" ]; then
    echo "Criando personal access client do Passport..."
    php artisan passport:client --personal --no-interaction
fi

USER_COUNT=$(php -r '
    require "vendor/autoload.php";
    $app = require_once "bootstrap/app.php";
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo \App\Models\User::count();
')

if [ "$USER_COUNT" = "0" ]; then
    echo "Banco de dados vazio. Executando seeders..."
    php artisan db:seed --force --no-interaction || echo "Aviso: não foi possível executar os seeders."
fi

echo "Iniciando servidor..."
if [ "$#" -gt 0 ]; then
    exec "$@"
else
    exec php artisan serve --host=0.0.0.0 --port=8000
fi
