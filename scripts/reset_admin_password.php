<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'yogs1122@gmail.com';
$password = '112233';

$u = User::where('email', $email)->first();
if ($u) {
    $u->password = Hash::make($password);
    $u->save();
    echo "OK: password updated for {$email}\n";
} else {
    echo "NOTFOUND: {$email}\n";
}
