<?php
namespace Database\Seeds;

use Carbon\Carbon;
use Database\AbstractSeeder;
use Faker\Factory;

class CarsSeeder extends AbstractSeeder {

    // TODO: tableName文字列を割り当ててください。
    protected ?string $tableName = 'cars';

    // TODO: tableColumns配列を割り当ててください。
    protected array $tableColumns = [
        [
            'data_type' => 'string',
            'column_name' => 'make'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'model'
        ],
        [
            'data_type' => 'int',
            'column_name' => 'year'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'color'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'price'
        ],
        [
            'data_type' => 'float',
            'column_name' => 'mileage'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'transmission'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'engine'
        ],
        [
            'data_type' => 'string',
            'column_name' => 'status'
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

        $created_at = $this->dateTime();
        $updated_at = $this->dateTime();

        $output = [
             $faker->company,
             $faker->word,
             $faker->numberBetween(1900, 2025),
             $faker->randomElement(['red', 'blue', 'green', 'white', 'brack', 'gray', 'purple', 'pink']),
             $faker->randomFloat(2, 1000, 50000),
             $faker->randomFloat(1, 0, 200000),
             $faker->randomElement(['Automatic', 'Manual']),
             $faker->randomElement(['V6', 'V8', 'Electric', 'Hybrid']),
             $faker->randomElement(['Available', 'Sold']),
             $created_at,
             $updated_at
        ];

        return $output;
    }

    private function dateTime() : Carbon {
        $start = Carbon::create(2000, 1, 1)->timestamp;
        $end = Carbon::create(2025, 12, 31)->timestamp;

        $randomTimeStamp = mt_rand($start, $end);

        $randomDate = Carbon::createFromTimestamp($randomTimeStamp);

        return $randomDate;
    }
}       