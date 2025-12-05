<?php

require_once __DIR__ . '/../controllers/ProjectRoomController.php';

function registerProjectRoomRoutes($request, $method)
{
    // DB Setup (Run once)
    if ($request === '/initRoomsDB' && $method === 'GET') {
        ProjectRoomController::initDB();
        return true;
    }

    // Rooms
    if ($request === '/createRoom' && $method === 'POST') {
        ProjectRoomController::createRoom();
        return true;
    }
    if ($request === '/getAllRooms' && $method === 'GET') {
        ProjectRoomController::getAllRooms();
        return true;
    }
    if (preg_match('#^/getRoomById/(\d+)$#', $request, $matches) && $method === 'GET') {
        ProjectRoomController::getRoomById($matches[1]);
        return true;
    }

    if ($request === '/updateRoom' && $method === 'POST') {
        ProjectRoomController::updateRoom();
        return true;
    }
    if ($request === '/deleteRoom' && $method === 'POST') {
        ProjectRoomController::deleteRoom();
        return true;
    }

    // Chat
    if ($request === '/sendMessage' && $method === 'POST') {
        ProjectRoomController::sendMessage();
        return true;
    }
    if (preg_match('#^/getRoomMessages/(\d+)$#', $request, $matches) && $method === 'GET') {
        ProjectRoomController::getRoomMessages($matches[1]);
        return true;
    }

    return false;
}
?>