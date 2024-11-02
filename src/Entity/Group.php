<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity]
#[ORM\Table(name: 'groups')]
#[OA\Schema(
    schema: "Group",
    title: "Group",
    description: "Group entity representing a chat group"
)]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[OA\Property(
        description: "ID of the group",
        example: 1
    )]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[OA\Property(
        description: "Name of the group",
        example: "Developers"
    )]
    private string $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
