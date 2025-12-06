<?php

require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/RoomsController.php';
require_once __DIR__ . '/../controllers/RoomMembershipController.php';
require_once __DIR__ . '/../controllers/RoomChatController.php';

function registerProjectRoomRoutes($request, $method) {
    // Require authentication for all project room routes
    AuthMiddleware::requireAuth();
    
    switch (true) {

        // 🆕 Create a room
        case $request === '/createRoom' && $method === 'POST':
            ProjectRoomController::createRoom();
            break;

        // 📜 Get all rooms
        case $request === '/getAllRooms' && $method === 'GET':
            ProjectRoomController::getAllRooms();
            break;

        // 🔍 Get room info
        case $request === '/getRoom' && $method === 'GET':
            ProjectRoomController::getRoom();
            break;

        // ➕ Join room
        case $request === '/joinRoom' && $method === 'POST':
            RoomMembershipController::joinRoom();
            break;

        // 👥 Get members
        case $request === '/getRoomMembers' && $method === 'GET':
            RoomMembershipController::getRoomMembers();
            break;

        // 💬 Send message
        case ($request === '/sendMessage' && $method === 'POST'):
            RoomChatController::sendMessage();
            break;

        // 📥 Get messages
        case ($request === '/getMessages' && $method === 'GET'):
            RoomChatController::getMessages();
            break;

        // 👤 Get user's rooms (where user is a member)
        case $request === '/getUserRooms' && $method === 'GET':
            ProjectRoomController::getUserRooms();
            break;

        default:
            return false;
    }

    return true;
}
?>
