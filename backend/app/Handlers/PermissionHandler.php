<?php
namespace App\Handlers;

use App\Repositories\ProjectRoomRepository;

/**
 * PermissionHandler
 * 
 * Chain of Responsibility Pattern Implementation
 * Checks if user has permission to send messages in the room
 * Part of the Chat System (UML Design)
 */
class PermissionHandler extends MessageHandler
{
    private ProjectRoomRepository $roomRepo;

    public function __construct(ProjectRoomRepository $roomRepo)
    {
        $this->roomRepo = $roomRepo;
    }

    public function handle(array $message): array
    {
        $roomId = $message['room_id'];
        $senderId = $message['sender_id'];

        // Check if user is a member of the room
        $isMember = $this->roomRepo->isMember($roomId, $senderId);

        if (!$isMember) {
            return ['error' => 'You are not a member of this room'];
        }

        // Pass to next handler if permission check succeeds
        if ($this->next) {
            return $this->next->handle($message);
        }

        return $message;
    }
}
