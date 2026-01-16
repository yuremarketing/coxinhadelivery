<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DIAGNÓSTICO DE CREDENCIAIS ---" . PHP_EOL;
echo "ID configurado: " . config('services.google.client_id') . PHP_EOL;
echo "Redirect URI: " . config('services.google.redirect') . PHP_EOL;

$url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
    'client_id' => config('services.google.client_id'),
    'redirect_uri' => config('services.google.redirect'),
    'response_type' => 'code',
    'scope' => 'openid profile email',
]);

echo "--- URL QUE ESTÁ SENDO GERADA ---" . PHP_EOL;
echo $url . PHP_EOL;
echo "---------------------------------" . PHP_EOL;
