<?php

namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AvatarSeeder;

/**
 * Running Seeders:
 * php artisan db:seed
 * 
 * --class option to specify a specific seeder class to run individually:
 * php artisan db:seed --class=UserSeeder
 */

// Documentación: https://laravel.com/framework/docs/12.x/seeding#writing-seeders

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     */
    public function run(): void {

        /**
         * Within the DatabaseSeeder class, you may use the call method to 
         * execute additional seed classes. 
         * 
         * call() method allows you to break up your database seeding into 
         * multiple files so that no single seeder class becomes too large. 
         * The call method accepts an array of seeder classes that should be executed
         */
        $this->call([
            AvatarSeeder::class,
        ]);
        
    }
}
