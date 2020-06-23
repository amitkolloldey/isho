<?php

use App\Attribute;
use App\AttributeValue;
use Illuminate\Database\Seeder;

class AttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a color attribute
        $attribute = Attribute::create([
            'name' =>  'Color',
        ]);

        $colors = ['white', 'blue', 'red', 'orange', 'yellow', 'wooden'];

        foreach ($colors as $color)
        {
            AttributeValue::create([
                'attribute_id'      =>  $attribute->id,
                'value'             =>  $color
            ]);
        }

    }
}
