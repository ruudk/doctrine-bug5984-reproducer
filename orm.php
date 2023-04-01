<?php

declare(strict_types=1);

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Ramsey\Uuid\Doctrine\UuidType;

include __DIR__ . '/vendor/autoload.php';

Type::addType('uuid', UuidType::class);

include __DIR__ . '/vendor/autoload.php';

$conn = [
    'driver' => 'pdo_mysql',
    'server_version' => '8.0',
    'host' => '127.0.0.1',
    'port' => 3306,
    'user' => 'doctrine',
    'password' => 'doctrine',
    'dbname' => 'doctrine',
    'defaultTableOptions' => [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'engine' => 'InnoDB',
    ],
];

$em = EntityManager::create(
    $conn,
    Setup::createAttributeMetadataConfiguration(
        [__DIR__ . "/src"],
        true,
    )
);


$schemaTool = new SchemaTool($em);
$schemaTool->dropDatabase();

$metadatas = $em->getMetadataFactory()->getAllMetadata();
$sqls = $schemaTool->getUpdateSchemaSql($metadatas, true);

echo 'The following SQL statements will be executed:' . PHP_EOL;
foreach ($sqls as $sql) {
    echo sprintf('%s;', str_replace(', ', ", \n  ", $sql)) . PHP_EOL;
}
echo PHP_EOL;

echo 'Updating database schema...' . PHP_EOL;
$schemaTool->updateSchema($metadatas, true);
$pluralization = count($sqls) === 1 ? 'query was' : 'queries were';
echo sprintf('%d %s executed', count($sqls), $pluralization) . PHP_EOL;
echo PHP_EOL;

echo 'Database schema updated successfully!' . PHP_EOL;

echo 'Checking if database schema is sync...' . PHP_EOL;
$sqls = $schemaTool->getUpdateSchemaSql($metadatas, true);
echo PHP_EOL;

if ($sql !== []) {
    echo 'Database schema is not sync!' . PHP_EOL;
    echo PHP_EOL;
    echo 'The following differences were found:' . PHP_EOL;
    foreach ($sqls as $sql) {
        echo sprintf('%s;', str_replace(', ', ", \n  ", $sql)) . PHP_EOL;
    }
    echo PHP_EOL;
    exit(1);
} else {
    echo 'Database schema is sync!' . PHP_EOL;
}
