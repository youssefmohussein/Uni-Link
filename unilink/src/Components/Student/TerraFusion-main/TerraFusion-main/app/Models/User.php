<?php

namespace App\Models;

use PDO;
use PDOStatement;
use RuntimeException;

class User extends Model
{
    protected string $primaryKey = 'user_id';
    protected array $fillable = [
        'email',
        'password_hash',
        'full_name',
        'phone',
        'role',
        'profile_pic',
        'is_active'
    ];

    // Hash password before saving
    public function setPassword(string $password): void
    {
        $this->attributes['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    }

    // Verify password
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    // Find user by email
    public static function findByEmail(string $email): ?self
    {
        $instance = new static();
        $table = $instance->getTable();
        
        $stmt = $instance->prepare("SELECT * FROM `{$table}` WHERE `email` = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $instance->fill($result);
        $instance->exists = true;
        return $instance;
    }

    // Check if user has a specific role
    public function hasRole(string $roleName): bool
    {
        return $this->role === $roleName;
    }

    // Get user's orders
    public function orders(): array
    {
        return Order::where('customer_name', $this->full_name);
    }

    // Get user's reviews
    public function reviews(): array
    {
        return Review::where('user_id', $this->user_id);
    }

    // Check if user is admin
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    // Check if user is staff
    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    // Check if user is customer
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }
}
