<?php

namespace App\Controllers;

use App\Repositories\MenuRepository;
use App\Middlewares\AuthMiddleware;

class MenuController extends BaseController
{
    private MenuRepository $menuRepository;

    public function __construct()
    {
        $this->menuRepository = new MenuRepository();
    }

    /**
     * Display menu page
     */
    public function index(): void
    {
        $middleware = new AuthMiddleware();
        $middleware->handle();
        
        $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?? '';
        
        $categories = $this->menuRepository->getCategories();
        
        if (!empty($search)) {
            $menuItems = $this->menuRepository->search($search);
        } elseif ($category) {
            $menuItems = $this->menuRepository->getByCategory($category);
        } else {
            $menuItems = $this->menuRepository->getAllAvailable();
        }
        
        $this->view('customer/menu', [
            'menuItems' => $menuItems,
            'categories' => $categories,
            'selectedCategory' => $category,
            'searchQuery' => $search
        ]);
    }
}

