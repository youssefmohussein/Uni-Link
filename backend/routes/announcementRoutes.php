<?php
require_once __DIR__ . '/../controllers/AnnouncementController.php';

function registerAnnouncementRoutes($request, $method) {
    switch (true) {

        // CREATE
        case $request === '/addAnnouncement' && $method === 'POST':
            AnnouncementController::addAnnouncement();
            break;

        // GET ALL ANNOUNCEMENTS (optional faculty filter)
        case $request === '/getAnnouncements' && $method === 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $facultyId = $data['faculty_id'] ?? null;
            AnnouncementController::getAnnouncements($facultyId);
            break;

        // GET SINGLE ANNOUNCEMENT
        case preg_match('#^/getAnnouncement/(\d+)$#', $request, $matches) && $method === 'GET':
            AnnouncementController::getAnnouncement($matches[1]);
            break;

        // UPDATE
        case $request === '/updateAnnouncement' && $method === 'POST':
            AnnouncementController::updateAnnouncement();
            break;

        // DELETE
        case $request === '/deleteAnnouncement' && $method === 'POST':
            AnnouncementController::deleteAnnouncement();
            break;

        default:
            return false;
    }
    return true;
}
