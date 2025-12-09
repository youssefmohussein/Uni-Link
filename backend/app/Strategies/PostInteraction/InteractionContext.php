<?php
namespace App\Strategies\PostInteraction;

use App\Strategies\Interfaces\PostInteractionStrategyInterface;

/**
 * Interaction Context
 * 
 * Context class for Strategy Pattern
 * Allows switching between different interaction strategies at runtime
 */
class InteractionContext {
    private PostInteractionStrategyInterface $strategy;
    
    /**
     * Set the interaction strategy
     * 
     * @param PostInteractionStrategyInterface $strategy
     */
    public function setStrategy(PostInteractionStrategyInterface $strategy): void {
        $this->strategy = $strategy;
    }
    
    /**
     * Execute the current strategy
     * 
     * @param int $postId Post ID
     * @param int $userId User ID
     * @return array Result
     */
    public function executeInteraction(int $postId, int $userId): array {
        if (!isset($this->strategy)) {
            throw new \Exception("No interaction strategy set");
        }
        
        if (!$this->strategy->canExecute($postId, $userId)) {
            throw new \Exception("Cannot execute this interaction");
        }
        
        return $this->strategy->execute($postId, $userId);
    }
    
    /**
     * Get current strategy type
     * 
     * @return string
     */
    public function getStrategyType(): string {
        return $this->strategy->getType();
    }
    
    /**
     * Factory method to create strategy by type
     * 
     * @param string $type Interaction type
     * @return PostInteractionStrategyInterface
     */
    public static function createStrategy(string $type): PostInteractionStrategyInterface {
        return match(strtolower($type)) {
            'like' => new LikeStrategy(),
            'love' => new LoveStrategy(),
            'save' => new SaveStrategy(),
            'share' => new ShareStrategy(),
            'celebration', 'celberation' => new CelebrationStrategy(),
            default => throw new \Exception("Unknown interaction type: {$type}")
        };
    }
}
