<?php

namespace App\Controllers;

use App\Repositories\MenuRepository;
use App\Repositories\UserRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReportRepository;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\User;
use App\Models\Role;
use App\Models\Promotion;
use App\Models\Inventory;
use App\Middlewares\AdminMiddleware;

class AdminController extends BaseController
{
    private MenuRepository $menuRepository;
    private UserRepository $userRepository;
    private OrderRepository $orderRepository;
    private ReportRepository $reportRepository;

    public function __construct()
    {
        $this->menuRepository = new MenuRepository();
        $this->userRepository = new UserRepository();
        $this->orderRepository = new OrderRepository();
        $this->reportRepository = new ReportRepository();
    }

    /**
     * Show admin dashboard
     */
    public function dashboard(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        // Get today's sales summary
        $salesSummary = $this->reportRepository->getSalesSummary(
            date('Y-m-d'),
            date('Y-m-d')
        );
        
        // Get recent orders
        $recentOrders = $this->orderRepository->getByStatus('New');
        $recentOrders = array_slice($recentOrders, 0, 10);
        
        // Get low stock items
        $lowStockItems = Inventory::getLowStock();
        
        // Get active orders (New or Preparing)
        $activeOrders = $this->orderRepository->getPendingOrders();
        $activeOrdersCount = count($activeOrders);
        
        $this->view('admin/dashboard', [
            'salesSummary' => $salesSummary,
            'recentOrders' => $recentOrders,
            'lowStockItems' => $lowStockItems,
            'activeOrdersCount' => $activeOrdersCount
        ]);
    }

    /**
     * Menu management
     */
    public function menuManagement(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        $menuItems = $this->menuRepository->getAllAvailable();
        $categories = $this->menuRepository->getCategories();
        
        $this->view('admin/menu_management', [
            'menuItems' => $menuItems,
            'categories' => $categories
        ]);
    }

    /**
     * Create menu item
     */
    public function createMenuItem(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        $data = [
            'category' => filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING),
            'meal_name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
            'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING),
            'price' => filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT),
            'quantity' => filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: 0,
            'availability' => isset($_POST['is_available']) ? 'Available' : 'Out of Stock'
        ];
        
        $errors = $this->validateRequired($data, ['category', 'meal_name', 'price']);
        
        if (empty($errors)) {
            // Handle file upload for image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../assets/images/menu-items/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $data['image'] = 'menu-items/' . $fileName;
                }
            }
            
            $menuItem = $this->menuRepository->create($data);
            flash('success', 'Menu item created successfully');
            $this->redirect(url('admin/menu-management'));
            return;
        }
        
        foreach ($errors as $error) {
            flash('error', $error);
        }
        $this->redirect(url('admin/menu-management'));
    }

    /**
     * Update menu item
     */
    public function updateMenuItem(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            flash('error', 'Invalid menu item');
            $this->redirect(url('admin/menu-management'));
            return;
        }
        
        $data = [
            'category' => filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING),
            'meal_name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
            'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING),
            'price' => filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT),
            'quantity' => filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: 0,
            'availability' => isset($_POST['is_available']) ? 'Available' : 'Out of Stock'
        ];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../assets/images/menu-items/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $data['image'] = 'menu-items/' . $fileName;
            }
        }
        
        if ($this->menuRepository->update($id, $data)) {
            flash('success', 'Menu item updated successfully');
        } else {
            flash('error', 'Failed to update menu item');
        }
        
        $this->redirect(url('admin/menu-management'));
    }

    /**
     * Delete menu item
     */
    public function deleteMenuItem(int $id): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        if ($this->menuRepository->delete($id)) {
            flash('success', 'Menu item deleted successfully');
        } else {
            flash('error', 'Failed to delete menu item');
        }
        
        $this->redirect(url('admin/menu-management'));
    }

    /**
     * User management
     */
    public function users(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        $users = $this->userRepository->all();
        $roles = ['Manager', 'Chef Boss', 'Table Manager', 'Waiter'];
        
        $this->view('admin/users', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    /**
     * Create user
     */
    public function createUser(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        $data = [
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'password' => $_POST['password'] ?? '',
            'full_name' => filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING),
            'phone' => filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING),
            'role' => filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING)
        ];
        
        $errors = $this->validateRequired($data, ['email', 'password', 'full_name', 'role']);
        
        if (empty($errors)) {
            $existingUser = $this->userRepository->findByEmail($data['email']);
            if ($existingUser) {
                flash('error', 'Email already exists');
                $this->redirect(url('admin/users'));
                return;
            }
            
            $user = $this->userRepository->create($data);
            flash('success', 'User created successfully');
        } else {
            foreach ($errors as $error) {
                flash('error', $error);
            }
        }
        
        $this->redirect(url('admin/users'));
    }

    /**
     * Update user
     */
    public function updateUser(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $data = [
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'full_name' => filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING),
            'phone' => filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING),
            'role' => filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING)
        ];
        
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }
        
        if ($this->userRepository->update($id, $data)) {
            flash('success', 'User updated successfully');
        } else {
            flash('error', 'Failed to update user');
        }
        
        $this->redirect(url('admin/users'));
    }

    /**
     * Delete user
     */
    public function deleteUser(int $id): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        if ($this->userRepository->delete($id)) {
            flash('success', 'User deleted successfully');
        } else {
            flash('error', 'Failed to delete user');
        }
        
        $this->redirect(url('admin/users'));
    }

    /**
     * Promotions management
     */
    public function promotions(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        $promotions = Promotion::all();
        
        $this->view('admin/promotions', [
            'promotions' => $promotions
        ]);
    }

    /**
     * Create promotion
     */
    public function createPromotion(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        $data = [
            'code' => strtoupper(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING)),
            'description' => filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING),
            'discount_type' => filter_input(INPUT_POST, 'discount_type', FILTER_SANITIZE_STRING),
            'discount_value' => filter_input(INPUT_POST, 'discount_value', FILTER_VALIDATE_FLOAT),
            'min_order_amount' => filter_input(INPUT_POST, 'min_order_amount', FILTER_VALIDATE_FLOAT) ?: 0,
            'max_discount' => filter_input(INPUT_POST, 'max_discount', FILTER_VALIDATE_FLOAT),
            'start_date' => filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING),
            'end_date' => filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING),
            'max_uses' => filter_input(INPUT_POST, 'max_uses', FILTER_VALIDATE_INT),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        $errors = $this->validateRequired($data, ['code', 'discount_type', 'discount_value', 'start_date', 'end_date']);
        
        if (empty($errors)) {
            $promotion = new Promotion($data);
            if ($promotion->save()) {
                flash('success', 'Promotion created successfully');
            } else {
                flash('error', 'Failed to create promotion');
            }
        } else {
            foreach ($errors as $error) {
                flash('error', $error);
            }
        }
        
        $this->redirect(url('admin/promotions'));
    }

    /**
     * Reports
     */
    public function reports(): void
    {
        $middleware = new AdminMiddleware();
        $middleware->handle();
        
        $startDate = filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_STRING) ?: date('Y-m-d', strtotime('-7 days'));
        $endDate = filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_STRING) ?: date('Y-m-d');
        
        $salesSummary = $this->reportRepository->getSalesSummary($startDate, $endDate);
        $popularItems = $this->reportRepository->getPopularItems(10, $startDate, $endDate);
        $peakTimes = $this->reportRepository->getPeakOrderingTimes($startDate, $endDate);
        $staffPerformance = $this->reportRepository->getStaffPerformance($startDate, $endDate);
        
        $this->view('admin/reports', [
            'salesSummary' => $salesSummary,
            'popularItems' => $popularItems,
            'peakTimes' => $peakTimes,
            'staffPerformance' => $staffPerformance,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}

