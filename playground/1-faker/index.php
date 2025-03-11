<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faker\Factory;

$faker = Factory::create();

$users = [];

for ($i = 0; $i < 10; $i++) {
  $users[] = [
    'name' => $faker->name,
    'email' => $faker->email,
    'phone' => $faker->phoneNumber,
    'address' => $faker->address,
  ];
}

echo json_encode($users, JSON_PRETTY_PRINT);

?>
