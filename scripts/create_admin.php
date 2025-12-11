<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

$email = 'yogs1122@gmail.com';
$password = '112233';

$role = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator']);

$user = User::where('email', $email)->first();
if (!$user) {
    $user = User::create([
        'name' => 'Admin',
        'email' => $email,
        'phone' => '+628000000001',
        'password' => Hash::make($password),
        'email_verified_at' => now(),
    ]);
    echo "Created user {$email}\n";
} else {
    $user->password = Hash::make($password);
    $user->email_verified_at = now();
    $user->save();
    echo "Updated password for {$email}\n";
}

// attach role if not attached
if (!$user->roles()->where('name','admin')->exists()) {
    $user->roles()->attach($role->id);
    echo "Attached admin role\n";
} else {
    echo "Role already attached\n";
}
