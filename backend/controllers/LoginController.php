<?php
require_once __DIR__ . '/../utils/DbConnection.php';

class LoginController
{
    public function login()
    {
        global $pdo;
        session_start();

        $input = json_decode(file_get_contents("php://input"), true);

        $identifier = $input["identifier"] ?? null; // email OR username
        $password   = $input["password"] ?? null;

        if (!$identifier || !$password) {
            http_response_code(400);
            echo json_encode(["error" => "Email/username and password required"]);
            exit;
        }

        // search by email OR username
        $query = "SELECT user_id, username, email, password, role 
                  FROM Users 
                  WHERE email = ? OR username = ?
                  LIMIT 1";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(401);
            echo json_encode(["error" => "Account not found"]);
            exit;
        }

        if (!password_verify($password, $user["password"])) {
            http_response_code(401);
            echo json_encode(["error" => "Incorrect password"]);
            exit;
        }

        // Store user data in session
        $_SESSION["user"] = [
            "id" => $user["user_id"],
            "username" => $user["username"],
            "email" => $user["email"],
            "role" => $user["role"]
        ];

        echo json_encode([
            "message" => "Login successful",
            "role" => $user["role"],
            "id" => $user["user_id"], // Explicitly return ID for frontend usage
            "username" => $user["username"],
            "email" => $user["email"],
            "redirect" => $this->redirectUrl($user["role"])
        ]);
    }

    private function redirectUrl($role)
    {
        return match ($role) {
            "Admin" => "/admin/dashboard",
            "Professor" => "/professor/home",
            "Student" => "/student/home",
            default => "/",
        };
    }
}
