composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

Si un jour tu veux un token qui expire dans 5 minutes (ex: pour un lien temporaire), tu peux faire :
$token = $user->createToken('temp', [], now()->addMinutes(5))->plainTextToken;