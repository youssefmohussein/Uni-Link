<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    public function create($data)
    {
        // Hash password before saving
        $data['password'] = \App\Helpers\Security::sanitize($data['password']);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO {$this->table} (email, password_hash, full_name, role) VALUES (:email, :password_hash, :full_name, :role)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'email' => $data['email'],
            'password_hash' => $hashedPassword,
            'full_name' => $data['full_name'] ?? $data['email'],
            'role' => $data['role'] ?? 'Waiter'
        ]);
    }

    public function update($id, $data)
    {
        error_log('UserModel::update called for id=' . $id . ' with data: ' . print_r($data, true));
        
        $fields = [];
        $params = ['id' => $id];
        
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }
        if (isset($data['full_name'])) {
            $fields[] = "full_name = :full_name";
            $params['full_name'] = $data['full_name'];
        }
        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params['role'] = $data['role'];
        }
        
        if (empty($fields)) {
            return true; // Nothing to update
        }
        
        $fieldsStr = implode(', ', $fields);
        $sql = "UPDATE {$this->table} SET $fieldsStr WHERE user_id = :id";
        error_log('UserModel::update: SQL = ' . $sql);
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($params);
        error_log('UserModel::update: execute result = ' . var_export($result, true));
        if (!$result) {
            error_log('UserModel::update: error = ' . print_r($stmt->errorInfo(), true));
        }
        return $result;
    }

    public function updatePassword(int $userId, string $hash): bool
    {
        error_log('UserModel::updatePassword called for id=' . $userId);
        
        $sql = "UPDATE users
                SET password_hash = :password
                WHERE user_id = :id";
        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':password' => $hash,
            ':id'       => $userId,
        ]);
        
        error_log('UserModel::updatePassword result = ' . var_export($result, true));
        if (!$result) {
            error_log('UserModel::updatePassword error = ' . print_r($stmt->errorInfo(), true));
        }
        
        return $result;
    }
}
