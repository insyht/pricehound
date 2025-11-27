<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product as ProductModel;

class Product extends Seeder
{
    public function run(): void
    {
        ProductModel::factory()->count(10)->create();
    }
}
