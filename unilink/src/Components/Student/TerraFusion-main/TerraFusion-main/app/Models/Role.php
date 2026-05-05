<?php

namespace App\Models;

class Role extends Model
{
    protected static string $table = 'roles';
    protected array $fillable = [
        'name',
        'description'
    ];

    /**
     * Get users with this role
     */
    public function users(): array
    {
        return User::where('role_id', $this->id);
    }
}
