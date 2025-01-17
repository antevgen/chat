<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\GroupRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
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

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "groups")]
    #[OA\Property(
        description: "Members of the group",
        type: "array",
        items: new OA\Items(ref: "#/components/schemas/User")
    )]
    #[ORM\JoinTable(name: 'group_users')]
    private Collection $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->createdAt = new DateTime();
    }

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

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $user): void
    {
        if (!$this->members->contains($user)) {
            $this->members->add($user);
            $user->addGroup($this);
        }
    }

    public function removeMember(User $user): void
    {
        if ($this->members->contains($user)) {
            $this->members->removeElement($user);
            $user->removeGroup($this);
        }
    }

    /**
     * @param Collection<int, User> $members
     */
    public function setMembers(Collection $members): void
    {
        $this->members = $members;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'members' => array_map(static fn(User $user) => $user->toArray(), $this->members->toArray()),
        ];
    }
}
