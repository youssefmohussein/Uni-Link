<?php
namespace App\Services;

use App\Strategies\PostInteraction\InteractionContext;
use App\Strategies\PostInteraction\LikeStrategy;
use App\Strategies\PostInteraction\LoveStrategy;
use App\Strategies\PostInteraction\CelebrationStrategy;
use App\Strategies\PostInteraction\SaveStrategy;
use App\Strategies\PostInteraction\ShareStrategy;

/**
 * Post Interaction Service
 * 
 * Business logic for post interactions using Strategy pattern
 */
class PostInteractionService extends BaseService {
    private InteractionContext $context;
    private GamificationService $gamificationService;
    private \App\Repositories\PostRepository $postRepo;
    
    public function __construct(GamificationService $gamificationService, \App\Repositories\PostRepository $postRepo) {
        $this->context = new InteractionContext();
        $this->gamificationService = $gamificationService;
        $this->postRepo = $postRepo;
    }
    
    /**
     * Add interaction to post
     * 
     * @param int $postId Post ID
     * @param int $userId User ID
     * @param string $type Interaction type
     * @return bool Success status
     */
    public function addInteraction(int $postId, int $userId, string $type): bool {
        // Set strategy based on type
        switch ($type) {
            case 'like':
                $this->context->setStrategy(new LikeStrategy());
                break;
            case 'love':
                $this->context->setStrategy(new LoveStrategy());
                break;
            case 'celebration':
                $this->context->setStrategy(new CelebrationStrategy());
                break;
            case 'save':
                $this->context->setStrategy(new SaveStrategy());
                break;
            case 'share':
                $this->context->setStrategy(new ShareStrategy());
                break;
            default:
                throw new \Exception('Invalid interaction type', 400);
        }
        
        $this->context->executeInteraction($postId, $userId);
        
        // Award points to the POST AUTHOR (not the liker) when they receive a like
        if ($type === 'like') {
            // Need to find post author first
            $post = $this->postRepo->find($postId);
            if ($post && isset($post['author_id'])) {
                // Don't award points for liking own post
                if ($post['author_id'] != $userId) {
                    $this->gamificationService->awardPoints(
                        (int)$post['author_id'], 
                        GamificationService::POINTS_LIKE_RECEIVED, 
                        'Received a like on post'
                    );
                }
            }
        }

        return true;
    }
    
    /**
     * Remove interaction from post
     * 
     * @param int $postId Post ID
     * @param int $userId User ID
     * @param string $type Interaction type
     * @return bool Success status
     */
    public function removeInteraction(int $postId, int $userId, string $type): bool {
        // Set strategy based on type
        switch ($type) {
            case 'like':
                $this->context->setStrategy(new LikeStrategy());
                break;
            case 'love':
                $this->context->setStrategy(new LoveStrategy());
                break;
            case 'celebration':
                $this->context->setStrategy(new CelebrationStrategy());
                break;
            case 'save':
                $this->context->setStrategy(new SaveStrategy());
                break;
            case 'share':
                $this->context->setStrategy(new ShareStrategy());
                break;
            default:
                throw new \Exception('Invalid interaction type', 400);
        }
        
        $this->context->executeInteraction($postId, $userId);
        return true;
    }
    
    /**
     * Get interactions by post
     * 
     * @param int $postId Post ID
     * @return array Array of interactions
     */
    public function getByPost(int $postId): array {
        // This would use a repository method
        // For now, returning empty array as placeholder
        return [];
    }
    
    /**
     * Get user reaction to post
     * 
     * @param int $postId Post ID
     * @param int $userId User ID
     * @return array|null User's reaction
     */
    public function getUserReaction(int $postId, int $userId): ?array {
        // This would use a repository method
        // For now, returning null as placeholder
        return null;
    }
    
    /**
     * Get reaction counts for post
     * 
     * @param int $postId Post ID
     * @return array Reaction counts
     */
    public function getReactionCounts(int $postId): array {
        // This would use a repository method
        // For now, returning empty counts
        return [
            'like' => 0,
            'love' => 0,
            'celebration' => 0,
            'save' => 0,
            'share' => 0
        ];
    }
}
