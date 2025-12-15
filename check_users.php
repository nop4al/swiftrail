<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = \App\Models\User::all();
echo "Total Users: " . count($users) . "\n\n";
foreach($users as $user) {
  echo "Email: " . $user->email . "\n";
  echo "Role: " . $user->role . "\n";
  echo "Name: " . $user->first_name . " " . $user->last_name . "\n";
  echo "---\n";
}
