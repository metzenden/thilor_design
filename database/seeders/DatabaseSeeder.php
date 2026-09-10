<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Peuple la base avec des données de démonstration réalistes (phase 14 du cahier
     * des charges) : rôles/admin, catalogue complet, clients et commandes.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ShopSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
