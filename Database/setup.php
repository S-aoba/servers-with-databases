<?php

use Database\MySQLWrapper;

$mysqli = new MySQLWrapper();

// carsテーブルの作成
$result = $mysqli->query("
    CREATE TABLE IF NOT EXISTS cars (
      id INT PRIMARY KEY AUTO_INCREMENT,
      make VARCHAR(50),
      model VARCHAR(50),
      year INT,
      color VARCHAR(20),
      price FLOAT,
      mileage FLOAT,
      transmission VARCHAR(20),
      engine VARCHAR(20),
      status VARCHAR(10)
    );
");

if ($result === false) throw new Exception('Could not execute query for cars table.');
else print("Successfully created 'cars' table.".PHP_EOL);

// car_partsテーブルの作成
$result = $mysqli->query("
    CREATE TABLE IF NOT EXISTS car_parts (
      id INT PRIMARY KEY AUTO_INCREMENT,
      car_id INT,
      name VARCHAR(50),
      description VARCHAR(100),
      price FLOAT,
      quantity_in_stock INT,
      FOREIGN KEY (car_id) REFERENCES cars(id)
    );
");

if ($result === false) throw new Exception('Could not execute query for car_parts table.');
else print("Successfully created 'car_parts' table.".PHP_EOL);

function insertCarQuery(
  string $make,
  string $model,
  int $year,
  string $color,
  float $price,
  float $mileage,
  string $transmission,
  string $engine,
  string $status
): string {
  return sprintf(
      "INSERT INTO cars (make, model, year, color, price, mileage, transmission, engine, status)
      VALUES ('%s', '%s', %d, '%s', %f, %f, '%s', '%s', '%s');",
      $make, $model, $year, $color, $price, $mileage, $transmission, $engine, $status
  );
}

function insertPartQuery(
  string $name,
  string $description,
  float $price,
  int $quantity_in_stock
): string {
  return sprintf(
      "INSERT INTO car_parts (name, description, price, quantity_in_stock)
      VALUES ('%s', '%s', %f, %d);",
      $name, $description, $price, $quantity_in_stock
  );
}

function runQuery(mysqli $mysqli, string $query): void {
  $result = $mysqli->query($query);
  if ($result === false) {
      throw new Exception('Could not execute query.');
  } else {
      echo "Query executed successfully.\n";
  }
}

runQuery($mysqli, insertCarQuery(
  make: 'Toyota',
  model: 'Corolla',
  year: 2020,
  color: 'Blue',
  price: 20000,
  mileage: 1500,
  transmission: 'Automatic',
  engine: 'Gasoline',
  status: 'Available'
));

runQuery($mysqli, insertPartQuery(
  name: 'Brake Pad',
  description: 'High Quality Brake Pad',
  price: 45.99,
  quantity_in_stock: 100
));

$mysqli->close();