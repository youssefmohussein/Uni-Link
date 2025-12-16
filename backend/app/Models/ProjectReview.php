<?php
namespace App\Models;

/**
 * ProjectReview Model
 * 
 * Represents a professor's review of a student project
 * Part of the Project Domain (UML Design)
 */
class ProjectReview {
    private int $reviewId;
    private int $projectId;
    private int $professorId;
    private ?string $comment;
    private ?float $score;
    private string $status;
    private ?string $createdAt;
    private ?string $updatedAt;
    
    public function __construct(array $data) {
        $this->reviewId = $data['review_id'] ?? 0;
        $this->projectId = $data['project_id'] ?? 0;
        $this->professorId = $data['professor_id'] ?? 0;
        $this->comment = $data['comment'] ?? null;
        $this->score = isset($data['score']) ? (float)$data['score'] : null;
        $this->status = $data['status'] ?? 'PENDING';
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    // Getters
    public function getReviewId(): int { return $this->reviewId; }
    public function getProjectId(): int { return $this->projectId; }
    public function getProfessorId(): int { return $this->professorId; }
    public function getComment(): ?string { return $this->comment; }
    public function getScore(): ?float { return $this->score; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    
    // Setters
    public function setReviewId(int $reviewId): void { $this->reviewId = $reviewId; }
    public function setComment(?string $comment): void { $this->comment = $comment; }
    public function setScore(?float $score): void { $this->score = $score; }
    public function setStatus(string $status): void { $this->status = $status; }
    
    /**
     * Business Logic: Check if review is positive (approved)
     */
    public function isApproved(): bool {
        return $this->status === 'APPROVED';
    }
    
    /**
     * Business Logic: Check if review is negative (rejected)
     */
    public function isRejected(): bool {
        return $this->status === 'REJECTED';
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array {
        return [
            'review_id' => $this->reviewId,
            'project_id' => $this->projectId,
            'professor_id' => $this->professorId,
            'comment' => $this->comment,
            'score' => $this->score,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
