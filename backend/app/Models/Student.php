<?php
namespace App\Models;

/**
 * Student Model
 * 
 * Extends User - demonstrates inheritance
 * Student-specific attributes: year, gpa, points
 */
class Student extends User {
    private ?int $year;
    private ?float $gpa;
    private int $points;
    
    /**
     * Constructor
     * 
     * @param array $data Student data including user data
     */
    public function __construct(array $data) {
        // Call parent constructor for base user data
        parent::__construct($data);
        
        // Set role
        $this->role = 'Student';
        
        // Student-specific properties
        $this->year = $data['year'] ?? null;
        $this->gpa = $data['gpa'] ?? null;
        $this->points = $data['points'] ?? 0;
    }
    
    // Getters
    public function getYear(): ?int {
        return $this->year;
    }
    
    public function getGpa(): ?float {
        return $this->gpa;
    }
    
    public function getPoints(): int {
        return $this->points;
    }
    
    // Setters
    public function setYear(int $year): void {
        // Validation: year must be between 1 and 6
        if ($year >= 1 && $year <= 6) {
            $this->year = $year;
            
            // Business rule: First year students have 0 GPA
            if ($year === 1) {
                $this->gpa = 0.0;
            }
        }
    }
    
    public function setGpa(float $gpa): void {
        // Validation: GPA must be between 0 and 4
        if ($gpa >= 0.0 && $gpa <= 4.0) {
            $this->gpa = $gpa;
        }
    }
    
    public function setPoints(int $points): void {
        if ($points >= 0) {
            $this->points = $points;
        }
    }
    
    /**
     * Implementation of abstract method from User
     * Returns student-specific data
     * 
     * @return array Student-specific data
     */
    public function getSpecificData(): array {
        return [
            'year' => $this->year,
            'gpa' => $this->gpa,
            'points' => $this->points
        ];
    }
    
    /**
     * Add points to student
     * 
     * @param int $points Points to add
     */
    public function addPoints(int $points): void {
        if ($points > 0) {
            $this->points += $points;
        }
    }
    
    /**
     * Deduct points from student
     * 
     * @param int $points Points to deduct
     */
    public function deductPoints(int $points): void {
        if ($points > 0 && $this->points >= $points) {
            $this->points -= $points;
        }
    }
    
    /**
     * Check if student is first year
     * 
     * @return bool
     */
    public function isFirstYear(): bool {
        return $this->year === 1;
    }
    
    /**
     * Get academic standing based on GPA
     * 
     * @return string Academic standing
     */
    public function getAcademicStanding(): string {
        if ($this->gpa === null) {
            return 'N/A';
        }
        
        if ($this->gpa >= 3.5) {
            return 'Excellent';
        } elseif ($this->gpa >= 3.0) {
            return 'Very Good';
        } elseif ($this->gpa >= 2.5) {
            return 'Good';
        } elseif ($this->gpa >= 2.0) {
            return 'Pass';
        } else {
            return 'Probation';
        }
    }
}
