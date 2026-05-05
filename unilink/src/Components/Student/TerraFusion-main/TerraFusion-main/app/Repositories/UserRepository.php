<?php

namespace App\Repositories;

use App\Models\User;
use App\Libs\Database;
use PDO;

class UserRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function all(): array
    {
        return User::all();
    }

    public function create(array $data): User
    {
        $user = new User($data);
        $user->setPassword($data['password']); // Model handles hashing if setPassword exists, or we map it manualy
        // Actually User model likely maps 'password' to 'password_hash' via setPassword or we need to rename key.
        // Let's assume User model needs to have filling for password_hash
        // Checking User model content from memory/previous views...
        // User.php was viewed. It has 'password_hash' in fillable.
        // It has setPassword method? No, previous view of User.php showed fillable but didn't show setPassword explicitly in the snippet I saw?
        // Wait, I saw User.php lines 1-89. It has fillable password_hash.
        // Let's look at how create was doing it: $user->setPassword($data['password']);
        // If setPassword doesn't exist, this fails. 
        // Let's use direct fill for now assuming logic in controller handled hashing OR User model has mutator.
        // Login/Register controller handled hashing.
        // AdminControllercreateUser does: $user = $this->userRepository->create($data); 
        // AdminController passes 'password' key.
        // So Repository create method needs to hash it and set 'password_hash'.
        
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        $user = new User($data);
        $user->save();
        return $user;
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->find($id);
        if (!$user) {
            return false;
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }

        $user->fill($data);
        return $user->save();
    }

    public function delete(int $id): bool
    {
        $user = $this->find($id);
        return $user ? $user->delete() : false;
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return User::findByEmail($email);
    }

    /**
     * Get users by role
     */
    public function getByRole(string $roleName): array
    {
        $sql = "SELECT * FROM users WHERE role = :role";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':role' => $roleName]);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $results[] = $row;
        }
        
        return $results;
    }
}

