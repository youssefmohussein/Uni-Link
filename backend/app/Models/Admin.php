<?php
namespace App\Models;

/**
 * Admin Model
 * 
 * Extends User - demonstrates inheritance
 * Admin-specific attributes: status, created_at
 */
class Admin extends User {
    private string $status;
    private string $createdAt;
    
    /**
     * Constructor
     * 
     * @param array $data Admin data including user data
     */
    public function __construct(array $data) {
        // Call parent constructor for base user data
        parent::__construct($data);
        
        // Set role
        $this->role = 'Admin';
        
        // Admin-specific properties
        $this->status = $data['status'] ?? 'Active';
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }
    
    // Getters
    public function getStatus(): string {
        return $this->status;
    }
    
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
    
    // Setters
    public function setStatus(string $status): void {
        // Validation: only allow specific statuses
        $allowedStatuses = ['Active', 'Disabled'];
        if (in_array($status, $allowedStatuses)) {
            $this->status = $status;
        }
    }
    
    /**
     * Implementation of abstract method from User
     * Returns admin-specific data
     * 
     * @return array Admin-specific data
     */
    public function getSpecificData(): array {
        return [
            'status' => $this->status,
            'created_at' => $this->createdAt
        ];
    }
    
    /**
     * Check if admin is active
     * 
     * @return bool
     */
    public function isActive(): bool {
        return $this->status === 'Active';
    }
    
    /**
     * Disable admin account
     */
    public function disable(): void {
        $this->status = 'Disabled';
    }
    
    /**
     * Enable admin account
     */
    public function enable(): void {
        $this->status = 'Active';
    }
}
