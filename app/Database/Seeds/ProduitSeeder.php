<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class ProduitSeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < 10; $i++) { //to add 10 clients. Change limit as desired
            $this->db->table('produit')->insert($this->generateSeeder());
        }
    }

    private function generateSeeder(): array
    {
        $faker = Factory::create();
        return [
            'name' => $faker->company(),
            'prix' => $faker->buildingNumber(),
        ];
    }
}
