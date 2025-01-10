<?php
namespace Database\Seeds;

use Database\AbstractSeeder;
use Database\MySQLWrapper;
use Exception;
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
        ]
    ];    

    public function createRowData(): array
    {
        // TODO: createRowData()メソッドを実装してください。
        $data = [];
        $count = 100000;

        for($i = 0; $i < $count; $i++) {
            $row = $this->generateRowData();
            $data[] =  $row;
        }
        return $data;;
    }

    private function generateRowData(): array {
        $faker = Factory::create();

        $output = [
            $faker->word,
            $faker->sentence,
            $faker->randomFloat(2, 1, 1000),
            $faker->numberBetween(0, 100),
            $faker->numberBetween(1, 1000)
        ];

        return $output;
    }
}       