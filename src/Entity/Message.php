<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'messages')]
#[OA\Schema(
    schema: "Message",
    title: "Message",
    description: "Message entity representing a chat message"
)]
final class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[OA\Property(description: "ID of the message", example: 1)]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[OA\Property(description: "Subject of the message", example: "Greetings")]
    private string $subject;

    #[ORM\Column(type: 'text')]
    #[OA\Property(description: "Content of the message", example: "Hello, world!")]
    private string $content;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    #[OA\Property(
        ref: "#/components/schemas/User",
        description: "Author of the message"
    )]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    public function __construct()
    {
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

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'content' => $this->content,
            'user' => $this->user->toArray(),
        ];
    }
}
