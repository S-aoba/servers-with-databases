<?php
namespace Database\Seeds;

use Database\AbstractSeeder;
use Faker\Factory;

class ComputerPartsSeeder extends AbstractSeeder {
    protected ?string $tableName = 'computer_parts';
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'name'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'type'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'brand'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'model_number'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'release_date'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'description'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'performance_score'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'market_price'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'rsm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'power_consumptionw'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'lengthm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'widthm'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'heightm'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'lifespan'
        ]
    ];

    public function createRowData(): array {
        $data = [];
        $count = 1;

        for($i = 0; $i < $count; $i++) {
            $row = $this->generateRowData();
            $data[] = $row;
        }
        return $data;
    }

    private function generateRowData(): array {
        $faker = Factory::create();

        $output = [
             $faker->words(3, true), 
             $faker->randomElement(['Type1', 'Type2', 'Type3']), 
             $faker->company, 
             $faker->bothify('Model-###??'), 
             $faker->date(), 
             $faker->paragraph, 
             $faker->numberBetween(50, 100), 
             $faker->randomFloat(2, 100, 10000), 
             $faker->randomFloat(2, 50, 5000), 
             $faker->randomFloat(2, 10, 500), 
             $faker->randomFloat(2, 0.5, 3), 
             $faker->randomFloat(2, 0.5, 2), 
             $faker->randomFloat(2, 0.5, 2), 
             $faker->numberBetween(1, 20), 
        ];

        return $output;
    }
}