<?php

use App\Product;
use Faker\Factory as FactoryAlias;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = FactoryAlias::create();

        foreach (range(1,100) as $index) {

            $name = $faker->text($maxNbChars = 20);
            $slug =  Str::slug($name);

            $product = Product::create([
                'sku' => $faker->regexify('[A-Za-z0-9]{5}'),
                'name' => $name,
                'slug' => $slug,
                'description' => $faker->text(),
                'price' => $faker->numberBetween($min = 1000, $max = 9000),
                'main_image' => 'demo.jpg',
                'created_at' => $faker->dateTime('now', $timezone = null),
                'updated_at' => $faker->dateTime('now', $timezone = null)
            ]);

            $stock = $product->stocks()->create([
                'product_id' => $product->id,
                'quantity' => $faker->numberBetween($min = 50, $max = 100),
                'created_at' => $faker->dateTime('now', $timezone = null),
            ]);

            $attribute_product = $product->attributes()->create([
                'product_id' => $product->id,
                'attribute_id' => \App\Attribute::get('id')
                    ->first()
                    ->id
            ]);

            $attribute_product->attribute_product_attribute_values()->create([
                'sku' => $faker->regexify('[A-Za-z0-9]{5}'),
                'price' => $faker->numberBetween($min = 1000, $max = 9000),
                'image' => 'demo.jpg',
                'attribute_value_id' => \App\AttributeValue::inRandomOrder()
                    ->first()
                    ->first()
                    ->id,
                'attribute_product_id' => $attribute_product->id
            ]);
        }
    }
}
