<?php
namespace App\Strategies\Interfaces;

/**
 * Post Interaction Strategy Interface
 * 
 * Defines the contract for different post interaction types
 * Implements Strategy Pattern for post interactions (Like, Love, Save, etc.)
 */
interface PostInteractionStrategyInterface {
    /**
     * Execute the interaction
     * 
     * @param int $postId Post ID
     * @param int $userId User ID
     * @return array Result of the interaction
     */
    public function execute(int $postId, int $userId): array;
    
    /**
     * Get interaction type
     * 
     * @return string Interaction type (Like, Love, Save, etc.)
     */
    public function getType(): string;
    
    /**
     * Check if interaction can be performed
     * 
     * @param int $postId Post ID
     * @param int $userId User ID
     * @return bool
     */
    public function canExecute(int $postId, int $userId): bool;
}
