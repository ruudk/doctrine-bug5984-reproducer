<?php

declare(strict_types=1);

namespace src;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Ramsey\Uuid\UuidInterface;

#[Table(name: 'child', options: ['charset' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci'])]
#[Entity]
class ChildEntity
{
    #[Column(name: 'id', type: 'uuid')]
    #[Id]
    private UuidInterface $id;

    #[OneToOne(targetEntity: ParentEntity::class)]
    #[JoinColumn(name: 'parent_id', referencedColumnName: 'id', unique: true, nullable: true, onDelete: 'CASCADE')]
    private ?ParentEntity $parent;
}
