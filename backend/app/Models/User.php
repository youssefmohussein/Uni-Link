<?php
namespace App\Models;

use App\Interfaces\ModelInterface;

/**
 * Abstract Base User Model
 * 
 * Implements inheritance for Admin, Student, and Professor
 * Demonstrates OOP principles: Encapsulation, Abstraction, Inheritance, Polymorphism
 */
abstract class User implements ModelInterface {
    // Encapsulation: Protected properties
    protected int $userId;
    protected string $username;
    protected string $email;
    protected string $password;
    protected ?string $phone;
    protected ?string $profileImage;
    protected ?string $bio;
    protected ?string $jobTitle;
    protected string $role;
    protected ?int $facultyId;
    protected ?int $majorId;
    
    /**
     * Constructor
     * 
     * @param array $data User data from database
     */
    public function __construct(array $data) {
        $this->userId = $data['user_id'] ?? 0;
        $this->username = $data['username'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password_hash'] ?? '';
        $this->phone = $data['phone'] ?? null;
        $this->profileImage = $data['profile_image'] ?? null;
        $this->bio = $data['bio'] ?? null;
        $this->jobTitle = $data['job_title'] ?? null;
        $this->role = $data['role'] ?? '';
        $this->facultyId = $data['faculty_id'] ?? null;
        $this->majorId = $data['major_id'] ?? null;
    }
    
    // Getters
    public function getUserId(): int { return $this->userId; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getPhone(): ?string { return $this->phone; }
    public function getProfileImage(): ?string { return $this->profileImage; }
    public function getBio(): ?string { return $this->bio; }
    public function getJobTitle(): ?string { return $this->jobTitle; }
    public function getRole(): string { return $this->role; }
    public function getFacultyId(): ?int { return $this->facultyId; }
    public function getMajorId(): ?int { return $this->majorId; }
    
    // Setters
    public function setUserId(int $userId): void { $this->userId = $userId; }
    public function setUsername(string $username): void { $this->username = $username; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }
    public function setProfileImage(?string $profileImage): void { $this->profileImage = $profileImage; }
    public function setBio(?string $bio): void { $this->bio = $bio; }
    public function setJobTitle(?string $jobTitle): void { $this->jobTitle = $jobTitle; }
    public function setFacultyId(?int $facultyId): void { $this->facultyId = $facultyId; }
    public function setMajorId(?int $majorId): void { $this->majorId = $majorId; }
    
    /**
     * Abstract method for polymorphism
     * Each subclass must implement its own specific data
     * 
     * @return array Role-specific data
     */
    abstract public function getSpecificData(): array;
    
    /**
     * Convert model to array
     * Template method pattern - combines base data with specific data
     * 
     * @return array Complete user data
     */
    public function toArray(): array {
        $baseData = [
            'user_id' => $this->userId,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'profile_image' => $this->profileImage,
            'bio' => $this->bio,
            'job_title' => $this->jobTitle,
            'faculty_id' => $this->facultyId,
            'major_id' => $this->majorId
        ];
        
        // Merge with role-specific data (polymorphism)
        return array_merge($baseData, $this->getSpecificData());
    }
    
    /**
     * Convert model to JSON
     * 
     * @return string JSON representation
     */
    public function toJson(): string {
        return json_encode($this->toArray());
    }
    
    /**
     * Get public data (without password)
     * 
     * @return array Public user data
     */
    public function toPublicArray(): array {
        $data = $this->toArray();
        unset($data['password_hash']);
        return $data;
    }
}
