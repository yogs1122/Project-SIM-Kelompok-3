<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
$users = User::all();
foreach($users as $u){
    echo "id={$u->id} name={$u->name} email={$u->email} created_at={$u->created_at}\n";
}
