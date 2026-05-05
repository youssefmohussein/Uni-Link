<?php

namespace App\Controllers;

use App\Models\MenuItemModel;
use App\Controllers\AuthController;

class MenuController
{
    private $menuModel;

    public function __construct()
    {
        $this->menuModel = new MenuItemModel();
    }

    public function index()
    {
        $menuItems = $this->menuModel->getAll();
        $uniqueMealTypes = $this->menuModel->getUniqueMealTypes();
        
        $data = [
            'menuItems' => $menuItems,
            'uniqueMealTypes' => $uniqueMealTypes
        ];

        $content = __DIR__ . '/../Views/menu/index.php';
        include __DIR__ . '/../Views/shared/layout.php';
    }

    public function save()
    {
        AuthController::checkAccess('Chef Boss');
        $logFile = __DIR__ . '/../../public/debug.log';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            
            // Handle image upload - Upload to folder FIRST, then save path to database
            $imageUrl = $_POST['current_image'] ?? 'images/meals-imgs/default.jpg';
            
            if (isset($_FILES['meal_image']) && $_FILES['meal_image']['error'] === UPLOAD_ERR_OK) {
                $uploadedFile = $_FILES['meal_image'];
                $filename = basename($uploadedFile['name']);
                $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                // Validate file type
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($fileExtension, $allowedExtensions)) {
                    \App\Helpers\Session::setFlash('error', 'Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed.');
                    header("Location: index.php?page=menu");
                    exit();
                }
                
                // Validate file size (max 5MB)
                $maxFileSize = 5 * 1024 * 1024;
                if ($uploadedFile['size'] > $maxFileSize) {
                    \App\Helpers\Session::setFlash('error', 'File size too large. Maximum size is 5MB.');
                    header("Location: index.php?page=menu");
                    exit();
                }
                
                // Create unique filename to avoid conflicts
                $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
                $uniqueName = time() . '_' . $safeName;
                
                // Define target directory (go up 3 levels from Controllers to TerraFusion root)
                $targetDir = realpath(__DIR__ . '/../../../images/meals-imgs/');
                
                if (!$targetDir || !is_dir($targetDir)) {
                    \App\Helpers\Session::setFlash('error', 'Upload directory not found. Please contact administrator.');
                    header("Location: index.php?page=menu");
                    exit();
                }
                
                $targetPath = $targetDir . DIRECTORY_SEPARATOR . $uniqueName;
                
                // STEP 1: Upload file to images/meals-imgs/ folder FIRST
                if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                    // STEP 2: Only after successful upload, set the path for database
                    $imageUrl = 'images/meals-imgs/' . $uniqueName;
                } else {
                    \App\Helpers\Session::setFlash('error', 'Failed to upload image. Please try again.');
                    header("Location: index.php?page=menu");
                    exit();
                }
            }

            $quantity = $_POST['quantity'] ?? 0;
            
            // Strict dynamic availability logic: > 0 is Available, otherwise Out of Stock
            if ($quantity > 0) {
                $availability = 'Available';
            } else {
                $availability = 'Out of Stock';
            }

            $data = [
                'meal_name' => $_POST['name'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'meal_type' => $_POST['meal_type'],
                'image' => $imageUrl,
                'availability' => $availability,
                'quantity' => $quantity
            ];

            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] SAVE MEAL: Data array = " . print_r($data, true) . "\n", FILE_APPEND);

            try {
                if ($id) {
                    $result = $this->menuModel->update($id, $data);
                    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] SAVE MEAL: UPDATE result = " . ($result ? 'TRUE' : 'FALSE') . "\n", FILE_APPEND);
                    if ($result) {
                        \App\Helpers\Session::setFlash('success', 'Meal updated successfully!');
                    } else {
                         throw new \Exception("Database update failed.");
                    }
                } else {
                    $result = $this->menuModel->create($data);
                    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] SAVE MEAL: CREATE result = " . ($result ? 'TRUE' : 'FALSE') . "\n", FILE_APPEND);
                    if ($result) {
                        \App\Helpers\Session::setFlash('success', 'Meal added successfully!');
                    } else {
                        throw new \Exception("Database insertion failed.");
                    }
                }
            } catch (\Exception $e) {
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] SAVE ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
                \App\Helpers\Session::setFlash('error', 'Error saving meal: ' . $e->getMessage());
            }

            header("Location: index.php?page=menu");
            exit();
        }
    }

    public function delete()
    {
        AuthController::checkAccess('Chef Boss');
        
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->menuModel->delete($id);
                \App\Helpers\Session::setFlash('success', 'Meal deleted successfully.');
            } catch (\PDOException $e) {
                if ($e->getCode() == 23000) {
                    \App\Helpers\Session::setFlash('error', 'Cannot delete this meal because it is linked to past or current orders. Try setting quantity to 0 to mark it as Out of Stock.');
                } else {
                    \App\Helpers\Session::setFlash('error', 'Cannot delete meal due to database error.');
                }
            } catch (\Exception $e) {
                \App\Helpers\Session::setFlash('error', 'An error occurred while deleting the meal.');
            }
        }
        header("Location: index.php?page=menu");
        exit();
    }
}
