<?php
// ================================
// 📦 User Controller (Admin adds users manually)
// ================================

require_once __DIR__ . '/../utils/DbConnection.php';

class UserController {

    // public static function addUser() {
    //     global $pdo;

    //     $input = json_decode(file_get_contents("php://input"), true);

    //     // ✅ Validate required fields
    //     if (!$input || !isset($input['user_id'], $input['username'], $input['email'], $input['password'], $input['role'])) {
    //         echo json_encode([
    //             "status" => "error",
    //             "message" => "Missing required fields"
    //         ]);
    //         return;
    //     }

    //     $user_id   = (int)$input['user_id'];
    //     $username  = trim($input['username']);
    //     $email     = trim($input['email']);
    //     $password  = password_hash($input['password'], PASSWORD_DEFAULT);
    //     $roleInput = trim($input['role']);
    //     $role      = ucfirst(strtolower($roleInput));
    //     $allowedRoles = ['Student', 'Professor', 'Admin'];

    //     if (!in_array($role, $allowedRoles, true)) {
    //         echo json_encode([
    //             "status" => "error",
    //             "message" => "Invalid role provided"
    //         ]);
    //         return;
    //     }

    //     $phone         = $input['phone'] ?? null;
    //     $profile_image = $input['profile_image'] ?? null;
    //     $bio           = $input['bio'] ?? null;
    //     $job_title     = $input['job_title'] ?? null;
    //     $faculty_id    = $input['faculty_id'] ?? null;
    //     $major_id      = $input['major_id'] ?? null;

    //     // ✅ Resolve faculty/major by name if ID not provided
    //     if (empty($faculty_id) && !empty($input['faculty_name'])) {
    //         $stmt = $pdo->prepare("SELECT faculty_id FROM Faculty WHERE faculty_name = ?");
    //         $stmt->execute([$input['faculty_name']]);
    //         $faculty_id = $stmt->fetchColumn();
    //         if (!$faculty_id) {
    //             echo json_encode([
    //                 "status" => "error",
    //                 "message" => "Invalid faculty name: " . $input['faculty_name']
    //             ]);
    //             return;
    //         }
    //     }

    //     if (empty($major_id) && !empty($input['major_name'])) {
    //         $stmt = $pdo->prepare("SELECT major_id FROM Major WHERE major_name = ?");
    //         $stmt->execute([$input['major_name']]);
    //         $major_id = $stmt->fetchColumn();
    //         if (!$major_id) {
    //             echo json_encode([
    //                 "status" => "error",
    //                 "message" => "Invalid major name: " . $input['major_name']
    //             ]);
    //             return;
    //         }
    //     }

    //     try {
    //         $pdo->beginTransaction();

    //         // 🔍 Check if user_id already exists
    //         $checkStmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
    //         $checkStmt->execute([$user_id]);
    //         if ($checkStmt->fetch()) {
    //             echo json_encode([
    //                 "status" => "error",
    //                 "message" => "User ID already exists"
    //             ]);
    //             $pdo->rollBack();
    //             return;
    //         }

    //         // ✅ Insert into Users
    //         $stmt = $pdo->prepare("
    //             INSERT INTO Users 
    //             (user_id, username, email, password, phone, profile_image, bio, job_title, role, faculty_id, major_id)
    //             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    //         ");
    //         $stmt->execute([
    //             $user_id, $username, $email, $password, $phone,
    //             $profile_image, $bio, $job_title, $role, $faculty_id, $major_id
    //         ]);

    //         // ✅ Insert into role-specific table
    //         if ($role === 'Student') {
    //             $pdo->prepare("INSERT INTO Student (student_id, points) VALUES (?, 0)")->execute([$user_id]);
    //         } elseif ($role === 'Professor') {
    //             $pdo->prepare("INSERT INTO Professor (professor_id) VALUES (?)")->execute([$user_id]);
    //         } elseif ($role === 'Admin') {
    //             $pdo->prepare("INSERT INTO Admin (admin_id, privilege_level) VALUES (?, 'Standard')")->execute([$user_id]);
    //         }

    //         $pdo->commit();

    //         echo json_encode([
    //             "status" => "success",
    //             "message" => "$role added successfully",
    //             "user_id" => $user_id
    //         ]);

    //     } catch (PDOException $e) {
    //         if ($pdo->inTransaction()) $pdo->rollBack();
    //         echo json_encode([
    //             "status" => "error",
    //             "message" => "Database error: " . $e->getMessage()
    //         ]);
    //     }
    // }

public static function addUser() {
    global $pdo;

    $input = json_decode(file_get_contents("php://input"), true);

    // Validate core fields
    if (!$input || !isset($input['user_id'], $input['username'], $input['email'], $input['password'], $input['role'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields"
        ]);
        return;
    }

    $user_id   = (int)$input['user_id'];
    $username  = trim($input['username']);
    $email     = trim($input['email']);
    $password  = password_hash($input['password'], PASSWORD_DEFAULT);
    $role      = ucfirst(strtolower(trim($input['role'])));
    $allowedRoles = ['Student', 'Professor', 'Admin'];

    if (!in_array($role, $allowedRoles, true)) {
        echo json_encode(["status" => "error", "message" => "Invalid role provided"]);
        return;
    }

    // Common fields
    $phone         = $input['phone'] ?? null;
    $profile_image = $input['profile_image'] ?? null;
    $bio           = $input['bio'] ?? null;
    $job_title     = $input['job_title'] ?? null;
    $faculty_id    = $input['faculty_id'] ?? null;
    $major_id      = $input['major_id'] ?? null;

    // Student-specific fields
    $year = $input['year'] ?? null;
    $gpa  = $input['gpa'] ?? null;

    // GPA rules:
    if ($role === 'Student') {
        if (!$year) {
            echo json_encode(["status" => "error", "message" => "Please provide academic year"]);
            return;
        }

        if ($year == 1) {
            $gpa = 0.0; // auto lock to 0 for first year
        } else {
            if ($gpa === null) {
                echo json_encode(["status" => "error", "message" => "Please provide GPA"]);
                return;
            }
        }
    }

    try {
        $pdo->beginTransaction();

        // Check duplicate ID
        $checkStmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
        $checkStmt->execute([$user_id]);
        if ($checkStmt->fetch()) {
            echo json_encode(["status" => "error", "message" => "User ID already exists"]);
            $pdo->rollBack();
            return;
        }

        // Insert into Users
        $stmt = $pdo->prepare("
            INSERT INTO Users 
            (user_id, username, email, password, phone, profile_image, bio, job_title, role, faculty_id, major_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id, $username, $email, $password, $phone,
            $profile_image, $bio, $job_title, $role, $faculty_id, $major_id
        ]);

        // Role-specific inserts
        if ($role === 'Student') {
            $stmt = $pdo->prepare("
                INSERT INTO Student (student_id, year, gpa, points) 
                VALUES (?, ?, ?, 0)
            ");
            $stmt->execute([$user_id, $year, $gpa]);

        } elseif ($role === 'Professor') {
            $pdo->prepare("INSERT INTO Professor (professor_id) VALUES (?)")->execute([$user_id]);

        } elseif ($role === 'Admin') {
            $pdo->prepare("INSERT INTO Admin (admin_id, privilege_level) VALUES (?, 'Standard')")
                ->execute([$user_id]);
        }

        $pdo->commit();

        echo json_encode([
            "status" => "success",
            "message" => "$role added successfully",
            "user_id" => $user_id
        ]);

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
}

    // 🔹 Get all users
    public static function getAllUsers() {
        global $pdo;

        try {
            $stmt = $pdo->query("SELECT * FROM Users ORDER BY user_id ASC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($users as &$user) {
                $user['password'] = '*****';
            }

            echo json_encode([
                "status" => "success",
                "count" => count($users),
                "data" => $users
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }

    // 🔹 Update existing user
//     public static function updateUser() {
//         global $pdo;

//         $input = json_decode(file_get_contents("php://input"), true);
//         if (!$input || !isset($input['user_id'])) {
//         echo json_encode([
//             "status" => "error",
//             "message" => "Missing user_id"
//         ]);
//         return;
//         }

//         $user_id = (int)$input['user_id'];
 
//         // Fetch existing user
//         $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
//         $stmt->execute([$user_id]);
//         $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

//         if (!$existingUser) {
//         echo json_encode([
//             "status" => "error",
//             "message" => "User not found"
//         ]);
//         return;
//         }

//         $oldRole = $existingUser['role'];
//         $newRole = isset($input['role']) ? ucfirst(strtolower($input['role'])) : $oldRole;
//         $allowedRoles = ['Student', 'Professor', 'Admin'];

//         if (!in_array($newRole, $allowedRoles, true)) {
//         echo json_encode([
//             "status" => "error",
//             "message" => "Invalid role provided"
//         ]);
//         return;
//       }

//         // Keep old values if not provided
//         $username      = $input['username'] ?? $existingUser['username'];
//         $email         = $input['email'] ?? $existingUser['email'];
//         $phone         = $input['phone'] ?? $existingUser['phone'];
//         $profile_image = $input['profile_image'] ?? $existingUser['profile_image'];
//         $bio           = $input['bio'] ?? $existingUser['bio'];
//         $job_title     = $input['job_title'] ?? $existingUser['job_title'];
//         $faculty_id    = $input['faculty_id'] ?? $existingUser['faculty_id'];
//         $major_id      = $input['major_id'] ?? $existingUser['major_id'];

//         if (!empty($input['password'])) {
//         $password = password_hash($input['password'], PASSWORD_DEFAULT);
//         } else {
//         $password = $existingUser['password'];
//         }

//         try {
//         $pdo->beginTransaction();

//         // 1️⃣ Update Users table
//         $stmt = $pdo->prepare("
//             UPDATE Users 
//             SET username = ?, email = ?, password = ?, phone = ?, 
//                 profile_image = ?, bio = ?, job_title = ?, role = ?, 
//                 faculty_id = ?, major_id = ?
//             WHERE user_id = ?
//         ");
//         $stmt->execute([
//             $username, $email, $password, $phone, $profile_image,
//             $bio, $job_title, $newRole, $faculty_id, $major_id, $user_id
//         ]);

//         // 2️⃣ Handle role change
//         if ($oldRole !== $newRole) {
//             // Delete old role entry
//             if ($oldRole === 'Student') {
//                 $pdo->prepare("DELETE FROM Student WHERE student_id = ?")->execute([$user_id]);
//             } elseif ($oldRole === 'Professor') {
//                 $pdo->prepare("DELETE FROM Professor WHERE professor_id = ?")->execute([$user_id]);
//             } elseif ($oldRole === 'Admin') {
//                 $pdo->prepare("DELETE FROM Admin WHERE admin_id = ?")->execute([$user_id]);
//             }

//             // Insert new role entry
//             if ($newRole === 'Student') {
//                 $pdo->prepare("INSERT INTO Student (student_id, points) VALUES (?, 0)")->execute([$user_id]);
//             } elseif ($newRole === 'Professor') {
//                 $pdo->prepare("INSERT INTO Professor (professor_id) VALUES (?)")->execute([$user_id]);
//             } elseif ($newRole === 'Admin') {
//                 $pdo->prepare("INSERT INTO Admin (admin_id, privilege_level) VALUES (?, 'Standard')")->execute([$user_id]);
//             }
//         }

//         $pdo->commit();

//         echo json_encode([
//             "status" => "success",
//             "message" => "User updated successfully"
//         ]);

//         } catch (PDOException $e) {
//         if ($pdo->inTransaction()) $pdo->rollBack();
//         echo json_encode([
//             "status" => "error",
//             "message" => "Database error: " . $e->getMessage()
//         ]);
//         }
// }

public static function updateUser() {
    global $pdo;

    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input || !isset($input['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Missing user_id"]);
        return;
    }

    $user_id = (int)$input['user_id'];

    // Fetch existing user
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingUser) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        return;
    }

    $oldRole = $existingUser['role'];
    $newRole = isset($input['role']) ? ucfirst(strtolower($input['role'])) : $oldRole;

    $allowedRoles = ['Student', 'Professor', 'Admin'];
    if (!in_array($newRole, $allowedRoles, true)) {
        echo json_encode(["status" => "error", "message" => "Invalid role"]);
        return;
    }

    // Carry-over values
    $username      = $input['username'] ?? $existingUser['username'];
    $email         = $input['email'] ?? $existingUser['email'];
    $phone         = $input['phone'] ?? $existingUser['phone'];
    $profile_image = $input['profile_image'] ?? $existingUser['profile_image'];
    $bio           = $input['bio'] ?? $existingUser['bio'];
    $job_title     = $input['job_title'] ?? $existingUser['job_title'];
    $faculty_id    = $input['faculty_id'] ?? $existingUser['faculty_id'];
    $major_id      = $input['major_id'] ?? $existingUser['major_id'];

    // password
    $password = !empty($input['password'])
        ? password_hash($input['password'], PASSWORD_DEFAULT)
        : $existingUser['password'];

    // Student fields
    $year = $input['year'] ?? null;
    $gpa  = $input['gpa'] ?? null;

    if ($newRole === 'Student') {
        if (!$year) {
            echo json_encode(["status" => "error", "message" => "Please provide academic year"]);
            return;
        }

        if ($year == 1) {
            $gpa = 0.0;
        } else {
            if ($gpa === null) {
                echo json_encode(["status" => "error", "message" => "Please provide GPA"]);
                return;
            }
        }
    }

    try {
        $pdo->beginTransaction();

        // Update Users table
        $stmt = $pdo->prepare("
            UPDATE Users SET 
                username = ?, email = ?, password = ?, phone = ?, 
                profile_image = ?, bio = ?, job_title = ?, role = ?, 
                faculty_id = ?, major_id = ?
            WHERE user_id = ?
        ");
        $stmt->execute([
            $username, $email, $password, $phone, $profile_image,
            $bio, $job_title, $newRole, $faculty_id, $major_id, $user_id
        ]);

        // Role changed?
        if ($oldRole !== $newRole) {

            // Delete old role entry
            if ($oldRole === 'Student') {
                $pdo->prepare("DELETE FROM Student WHERE student_id = ?")->execute([$user_id]);
            }
            if ($oldRole === 'Professor') {
                $pdo->prepare("DELETE FROM Professor WHERE professor_id = ?")->execute([$user_id]);
            }
            if ($oldRole === 'Admin') {
                $pdo->prepare("DELETE FROM Admin WHERE admin_id = ?")->execute([$user_id]);
            }

            // Insert new role entry
            if ($newRole === 'Student') {
                $pdo->prepare("
                    INSERT INTO Student (student_id, year, gpa, points) 
                    VALUES (?, ?, ?, 0)
                ")->execute([$user_id, $year, $gpa]);

            } elseif ($newRole === 'Professor') {
                $pdo->prepare("INSERT INTO Professor (professor_id) VALUES (?)")->execute([$user_id]);

            } elseif ($newRole === 'Admin') {
                $pdo->prepare("INSERT INTO Admin (admin_id, privilege_level) VALUES (?, 'Standard')")
                    ->execute([$user_id]);
            }

        } else if ($newRole === 'Student') {
            // Update student info if role stayed the same
            $stmt = $pdo->prepare("
                UPDATE Student SET year = ?, gpa = ? WHERE student_id = ?
            ");
            $stmt->execute([$year, $gpa, $user_id]);
        }

        $pdo->commit();

        echo json_encode(["status" => "success", "message" => "User updated successfully"]);

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode([
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
}


    // 🔹 Delete user
    public static function deleteUser() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input || !isset($input['user_id'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing user_id"
            ]);
            return;
        }

        $user_id = (int)$input['user_id'];

        try {
            $pdo->beginTransaction();

            // Delete from role-specific tables first
            $pdo->prepare("DELETE FROM Student WHERE student_id = ?")->execute([$user_id]);
            $pdo->prepare("DELETE FROM Professor WHERE professor_id = ?")->execute([$user_id]);
            $pdo->prepare("DELETE FROM Admin WHERE admin_id = ?")->execute([$user_id]);

            // Then delete from Users
            $stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = ?");
            $stmt->execute([$user_id]);

            $pdo->commit();

            echo json_encode([
                "status" => "success",
                "message" => "User deleted successfully"
            ]);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $e->getMessage()
            ]);
        }
    }
}
?>
