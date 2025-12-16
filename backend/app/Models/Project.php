<?php
namespace App\Models;

/**
 * Project Model
 * 
 * Represents a student project submission
 * Part of the Project Domain (UML Design)
 * Works with Command Pattern for professor actions
 */
class Project {
    private int $projectId;
    private int $studentId;
    private string $title;
    private ?string $description;
    private ?string $filePath;
    private ?string $filename;
    private string $status;
    private ?float $grade;
    private ?int $supervisorId;
    private ?string $submittedAt;
    private ?string $updatedAt;
    
    // Related entities
    private array $skills = [];
    private array $reviews = [];
    
    public function __construct(array $data) {
        $this->projectId = $data['project_id'] ?? 0;
        $this->studentId = $data['student_id'] ?? 0;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->filePath = $data['file_path'] ?? null;
        $this->filename = $data['filename'] ?? null;
        $this->status = $data['status'] ?? 'PENDING';
        $this->grade = isset($data['grade']) ? (float)$data['grade'] : null;
        $this->supervisorId = $data['supervisor_id'] ?? null;
        $this->submittedAt = $data['submitted_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    // Getters
    public function getProjectId(): int { return $this->projectId; }
    public function getStudentId(): int { return $this->studentId; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): ?string { return $this->description; }
    public function getFilePath(): ?string { return $this->filePath; }
    public function getFilename(): ?string { return $this->filename; }
    public function getStatus(): string { return $this->status; }
    public function getGrade(): ?float { return $this->grade; }
    public function getSupervisorId(): ?int { return $this->supervisorId; }
    public function getSubmittedAt(): ?string { return $this->submittedAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getSkills(): array { return $this->skills; }
    public function getReviews(): array { return $this->reviews; }
    
    // Setters
    public function setProjectId(int $projectId): void { $this->projectId = $projectId; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setGrade(?float $grade): void { $this->grade = $grade; }
    public function setSupervisorId(?int $supervisorId): void { $this->supervisorId = $supervisorId; }
    public function setSkills(array $skills): void { $this->skills = $skills; }
    public function setReviews(array $reviews): void { $this->reviews = $reviews; }
    
    /**
     * Business Logic: Submit the project
     */
    public function submit(): void {
        $this->status = 'PENDING';
    }
    
    /**
     * Business Logic: Approve the project
     */
    public function approve(): void {
        $this->status = 'APPROVED';
    }
    
    /**
     * Business Logic: Reject the project
     */
    public function reject(): void {
        $this->status = 'REJECTED';
    }
    
    /**
     * Business Logic: Check if project is pending review
     */
    public function isPending(): bool {
        return $this->status === 'PENDING';
    }
    
    /**
     * Business Logic: Check if project is approved
     */
    public function isApproved(): bool {
        return $this->status === 'APPROVED';
    }
    
    /**
     * Business Logic: Check if project is rejected
     */
    public function isRejected(): bool {
        return $this->status === 'REJECTED';
    }
    
    /**
     * Business Logic: Check if project has a file
     */
    public function hasFile(): bool {
        return !empty($this->filePath);
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'project_id' => $this->projectId,
            'student_id' => $this->studentId,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $this->filePath,
            'filename' => $this->filename,
            'status' => $this->status,
            'grade' => $this->grade,
            'supervisor_id' => $this->supervisorId,
            'submitted_at' => $this->submittedAt,
            'updated_at' => $this->updatedAt,
            'skills' => $this->skills,
            'reviews' => $this->reviews
        ];
    }
}
