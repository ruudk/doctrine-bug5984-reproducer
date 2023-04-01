<?php

declare(strict_types=1);

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Doctrine\UuidType;

include __DIR__ . '/vendor/autoload.php';

$host = '127.0.0.1';
$port = 3306;
$username = 'doctrine';
$password = 'doctrine';
$database = 'doctrine';

$dsn = sprintf("mysql://%s:%s@%s:%d/%s?server_version=8.0&charset=utf8mb4&defaultTableOptions[charset]=utf8mb4&defaultTableOptions[collation]=utf8mb4_unicode_ci&defaultTableOptions[engine]=InnoDB", $username, $password, $host, $port, $database);

$defaultTableOptions = [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'engine' => 'InnoDB',
];

Type::addType('uuid', UuidType::class);

$connection = DriverManager::getConnection(['url' => $dsn]);
$connection->executeQuery(
    <<<SQL
DROP DATABASE $database;
CREATE DATABASE $database;
SQL
);

$connection = DriverManager::getConnection(['url' => $dsn]);
$connection->executeQuery(
    <<<SQL
CREATE TABLE `parent` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

CREATE TABLE `child` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:uuid)',
  `parent_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '(DC2Type:uuid)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_896AFCDB2FFECF6A` (`parent_id`),
  CONSTRAINT `FK_896AFCDB2FFECF6A` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
);

$schemaManager = $connection->createSchemaManager();
$schemaConfig = $schemaManager->createSchemaConfig()->setDefaultTableOptions($defaultTableOptions);
$fromSchema = $schemaManager->introspectSchema();

$toSchema = new Schema([], [], $schemaConfig);
$table = $toSchema->createTable('parent')
    ->addOption('charset', 'utf8mb4')
    ->addOption('collation', 'utf8mb4_bin');
$table->addColumn(
    'id',
    'uuid',
    [
        'length' => 36,
        'notnull' => true,
        'customSchemaOptions' => [
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ]
);
$table->setPrimaryKey(['id']);

$table = $toSchema->createTable('child')
    ->addOption('charset', 'utf8mb4')
    ->addOption('collation', 'utf8mb4_unicode_ci');
$table->addColumn(
    'id',
    'uuid',
    [
        'length' => 36,
        'notnull' => true,
    ]
);
$table->addColumn(
    'parent_id',
    'uuid',
    [
        'length' => 36,
        'notnull' => true,
        'customSchemaOptions' => [
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ]
);
$table->setPrimaryKey(['id']);
$table->addForeignKeyConstraint(
    'parent',
    ['parent_id'],
    ['id'],
    ['onDelete' => 'CASCADE'],
    'FK_896AFCDB2FFECF6A'
);
$table->addUniqueIndex(
    ['parent_id'],
    'UNIQ_896AFCDB2FFECF6A'
);
$up = $schemaManager->createComparator()->compareSchemas($fromSchema, $toSchema);
$diffSql = $up->toSql($connection->getDatabasePlatform());
if ($diffSql !== []) {
    echo "The schema should be sync, but the diff keeps on reporting changes:\n";
    var_dump($diffSql);
    exit(1);
}

$down = $schemaManager->createComparator()->compareSchemas($toSchema, $fromSchema);
$diffSql = $down->toSql($connection->getDatabasePlatform());
if ($diffSql !== []) {
    echo "The schema should be sync, but the diff keeps on reporting changes:\n";
    var_dump($diffSql);
    exit(1);
}
