<?php
namespace App\Models;

/**
 * Professor Model
 * 
 * Extends User - demonstrates inheritance
 * Professor-specific attributes: academic_rank, office_location
 */
class Professor extends User {
    private ?string $academicRank;
    private ?string $officeLocation;
    
    /**
     * Constructor
     * 
     * @param array $data Professor data including user data
     */
    public function __construct(array $data) {
        // Call parent constructor for base user data
        parent::__construct($data);
        
        // Set role
        $this->role = 'Professor';
        
        // Professor-specific properties
        $this->academicRank = $data['academic_rank'] ?? null;
        $this->officeLocation = $data['office_location'] ?? null;
    }
    
    // Getters
    public function getAcademicRank(): ?string {
        return $this->academicRank;
    }
    
    public function getOfficeLocation(): ?string {
        return $this->officeLocation;
    }
    
    // Setters
    public function setAcademicRank(string $rank): void {
        // Validation: only allow specific ranks
        $allowedRanks = [
            'Teaching Assistant',
            'Assistant Lecturer',
            'Lecturer',
            'Assistant Professor',
            'Associate Professor',
            'Professor'
        ];
        
        if (in_array($rank, $allowedRanks)) {
            $this->academicRank = $rank;
        }
    }
    
    public function setOfficeLocation(string $location): void {
        $this->officeLocation = $location;
    }
    
    /**
     * Implementation of abstract method from User
     * Returns professor-specific data
     * 
     * @return array Professor-specific data
     */
    public function getSpecificData(): array {
        return [
            'academic_rank' => $this->academicRank,
            'office_location' => $this->officeLocation
        ];
    }
    
    /**
     * Check if professor can grade projects
     * All professors can grade, but this method allows for future business rules
     * 
     * @return bool
     */
    public function canGradeProjects(): bool {
        return true;
    }
    
    /**
     * Check if professor is senior (Associate Professor or above)
     * 
     * @return bool
     */
    public function isSenior(): bool {
        return in_array($this->academicRank, ['Associate Professor', 'Professor']);
    }
    
    /**
     * Get rank level (for sorting/comparison)
     * 
     * @return int Rank level (higher is more senior)
     */
    public function getRankLevel(): int {
        return match($this->academicRank) {
            'Professor' => 6,
            'Associate Professor' => 5,
            'Assistant Professor' => 4,
            'Lecturer' => 3,
            'Assistant Lecturer' => 2,
            'Teaching Assistant' => 1,
            default => 0
        };
    }
}
