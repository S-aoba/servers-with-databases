<?php
namespace Database\Seeds;

use Carbon\Carbon;
use Database\AbstractSeeder;
use Faker\Factory;

class CarPartsSeeder extends AbstractSeeder {

    // TODO: tableName文字列を割り当ててください。
    protected ?string $tableName = 'car_parts';

    // TODO: tableColumns配列を割り当ててください。
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'price'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'quantity_in_stock'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'car_id'
        ],
        [
            'data_type' => 'Carbon\Carbon',
            'column_name' => 'created_at'
        ],
        [
            'data_type' => 'Carbon\Carbon',
            'column_name' => 'updated_at'
        ]
    ];    

    public function createRowData(): array
    {
        // TODO: createRowData()メソッドを実装してください。
        $data = [];
        $count = 1000;

        for($i = 0; $i < $count; $i++) {
            $row = $this->generateRowData();
            $data[] =  $row;
        }
        return $data;;
    }

    private function generateRowData(): array {
        $faker = Factory::create();

        $created_at = Carbon::now('Asia/Tokyo');
        $updated_at = Carbon::now('Asia/Tokyo');

        $output = [
            $faker->word,
            $faker->sentence,
            $faker->randomFloat(2, 1, 1000),
            $faker->numberBetween(0, 100),
            $faker->numberBetween(1, 1000),
            $created_at,
            $updated_at
        ];

        return $output;
    }
}       