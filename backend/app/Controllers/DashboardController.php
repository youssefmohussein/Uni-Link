<?php
namespace App\Controllers;

use App\Services\DashboardService;

/**
 * Dashboard Controller
 * 
 * Handles dashboard statistics
 */
class DashboardController extends BaseController {
    private DashboardService $dashboardService;
    
    public function __construct(DashboardService $dashboardService) {
        $this->dashboardService = $dashboardService;
    }
    
    /**
     * Get dashboard statistics
     */
    public function getStats(): void {
        try {
            $this->requireRole('Admin');
            
            $stats = $this->dashboardService->getStats();
            $this->success($stats);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
