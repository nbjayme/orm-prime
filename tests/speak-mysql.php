<?php
require_once(__DIR__ . '/../src/orm-prime-init.php');

$ormFilter = new OrmPrimeFilter();
$mysqlSpeak = new OrmPrimeSpeakMysql();

$ormFilter->Where('gender = :gender')
  ->Tokens([
    'gender' => 'Male'
  ]);

$opts = $ormFilter->toArray();

echo $mysqlSpeak->Select('users',['*'] , $opts) . "\n";
echo $mysqlSpeak->Count('users', $opts) . "\n";

echo $mysqlSpeak->Insert('users', [
  'id' => '10',
  'firstname' => 'Roland',
  'middlename' => 'Bricks',
  'lastname' => 'Trizal'
]) . "\n";

$ormFilter->Clear()->Where('id = :id')
  ->Tokens([
    'id' => 10,
  ]);
$opts = $ormFilter->toArray();

echo $mysqlSpeak->Update('users', [
    'firstname' => 'Zenny',
    'middlename' => 'Santos',
    'lastname' => 'Larugao'
  ],
  $opts) . "\n";

$ormFilter->Clear()->Where('age > :age')
  ->Tokens([
    'age' => 45
  ]);
$opts = $ormFilter->toArray();
echo $mysqlSpeak->Delete('users', $opts) . "\n";
