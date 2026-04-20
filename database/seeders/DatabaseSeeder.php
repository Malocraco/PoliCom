<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear Roles
        $roleCanales = \App\Models\Role::create([
            'name' => 'Analista de Canales',
            'slug' => 'analista_can'
        ]);

        $roleComunicaciones = \App\Models\Role::create([
            'name' => 'Analista de Comunicaciones',
            'slug' => 'analista_com'
        ]);

        // Analista de Canales
        User::updateOrCreate(
            ['email' => 'canales@poligran.edu.co'],
            [
                'name' => 'Analista Canales',
                'password' => bcrypt('password'),
                'role_id' => $roleCanales->id
            ]
        );

        // Analista de Comunicaciones
        User::updateOrCreate(
            ['email' => 'comunicaciones@poligran.edu.co'],
            [
                'name' => 'Analista Comunicaciones',
                'password' => bcrypt('password'),
                'role_id' => $roleComunicaciones->id
            ]
        );
    }
}
