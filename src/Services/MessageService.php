<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Group;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class MessageService
{
    public function __construct(
        private MessageRepository $messageRepository,
    ) {
    }

    public function fetchMessagesByGroupId(int $groupId, int $page = 1, int $limit = 10): array
    {
        $adapter = new QueryAdapter($this->messageRepository->findMessagesByGroupId($groupId));
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        $messages = array_map(
            static fn($message) => $message->toArray(),
            iterator_to_array($pagerfanta->getCurrentPageResults())
        );

        return [
            'data' => $messages,
            'meta' => [
                'current_page' => $pagerfanta->getCurrentPage(),
                'per_page' => $pagerfanta->getMaxPerPage(),
                'total' => $pagerfanta->getNbResults(),
                'total_pages' => $pagerfanta->getNbPages(),
            ]
        ];
    }

    public function sendMessage(
        string $subject,
        string $content,
        Group $group,
        User $user
    ): Message {
        $message = new Message();
        $message->setSubject($subject);
        $message->setContent($content);
        $message->setGroup($group);
        $message->setUser($user);

        $this->messageRepository->save($message);
        return $message;
    }
}
