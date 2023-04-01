<?php

declare(strict_types=1);

namespace src;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Ramsey\Uuid\UuidInterface;

#[Table(name: 'categorized_imported_ticket', options: ['charset' => 'utf8mb4', 'collation' => 'utf8mb4_bin'])]
#[Entity]
class ParentEntity
{
    #[Column(name: 'id', type: 'uuid', options: ['collation' => 'utf8mb4_unicode_ci'])]
    #[Id]
    private UuidInterface $id;
}
