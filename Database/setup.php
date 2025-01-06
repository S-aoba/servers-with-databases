<?php

use Database\MySQLWrapper;

$mysqli = new MySQLWrapper();

$result = $mysqli->query(file_get_contents(__DIR__ . '/Examples/cars-setup.sql'));

if($result === false) throw new Exception('Could not execute query.');
else print("Successfully ran all SQL setup queries.".PHP_EOL);