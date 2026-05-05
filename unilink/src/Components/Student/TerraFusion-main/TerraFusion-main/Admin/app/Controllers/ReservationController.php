<?php

namespace App\Controllers;

use App\Models\ReservationModel;

class ReservationController
{
    private $reservationModel;

    public function __construct()
    {
        $this->reservationModel = new ReservationModel();
    }

    public function index()
    {
        $reservations = $this->reservationModel->getAll(); 
        
        $data = [
            'reservations' => $reservations
        ];

        // Standard MVC include
        $content = __DIR__ . '/../Views/reservations/index.php';
        include __DIR__ . '/../Views/shared/layout.php';
    }

    public function save()
    {
        // Authorization check: Manager or Table Manager
        $userRole = $_SESSION['role'] ?? '';
        if ($userRole !== 'Manager' && $userRole !== 'Table Manager') {
            $_SESSION['error_message'] = 'Access Denied: You do not have permission to modify reservations.';
            header("Location: index.php?page=reservations");
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve inputs using CONSISTENT names
            $id = $_POST['reservation_id'] ?? '';
            
            $data = [
                'customer_name'    => $_POST['customer_name'],
                'contact_phone'    => $_POST['contact_phone'],
                'reservation_date' => $_POST['reservation_date'],
                'reservation_time' => $_POST['reservation_time'],
                'party_size'       => $_POST['party_size']
            ];

            if (!empty($id)) {
                // UPDATE
                $this->reservationModel->update($id, $data);
            } else {
                // CREATE
                $this->reservationModel->create($data);
            }
            
            // Redirect
            header("Location: index.php?page=reservations");
            exit();
        }
    }

    public function delete()
    {
        // Authorization check: Manager or Table Manager
        $userRole = $_SESSION['role'] ?? '';
        if ($userRole !== 'Manager' && $userRole !== 'Table Manager') {
            $_SESSION['error_message'] = 'Access Denied: You do not have permission to delete reservations.';
            header("Location: index.php?page=reservations");
            exit();
        }
        
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $this->reservationModel->delete($id);
        }
        header("Location: index.php?page=reservations");
        exit();
    }
}
