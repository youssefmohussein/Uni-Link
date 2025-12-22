<?php
namespace App\Services;

use App\Repositories\PostRepository;
use App\Repositories\CommentRepository;

/**
 * Post Service
 * 
 * Business logic for post management
 */
class PostService extends BaseService
{
    private PostRepository $postRepo;
    private CommentRepository $commentRepo;
    private GamificationService $gamificationService;

    public function __construct(PostRepository $postRepo, CommentRepository $commentRepo, GamificationService $gamificationService)
    {
        $this->postRepo = $postRepo;
        $this->commentRepo = $commentRepo;
        $this->gamificationService = $gamificationService;
    }

    /**
     * Get all posts with author and media information
     * 
     * @param int|null $limit Limit number of posts
     * @param int $offset Offset for pagination
     * @return array
     */
    public function getAllPosts(?int $limit = null, int $offset = 0): array
    {
        return $this->postRepo->getAllWithDetails($limit, $offset);
    }

    /**
     * Create post
     * 
     * @param array $data Post data
     * @return array Created post
     */
    public function createPost(array $data): array
    {
        // Validate
        $errors = $this->validate($data, [
            'author_id' => ['required'],
            'content' => ['required', 'min:1'],
            'category' => ['required']
        ]);

        if (!empty($errors)) {
            throw new \Exception('Validation failed: ' . json_encode($errors), 400);
        }

        // Sanitize
        $data['content'] = $this->sanitize($data['content']);
        $data['created_at'] = date('Y-m-d H:i:s');

        // Filter out fields that are not in the posts table (like faculty_id)
        $validFields = ['author_id', 'content', 'category', 'status', 'visibility', 'created_at', 'updated_at'];
        $insertData = array_intersect_key($data, array_flip($validFields));

        // Create
        $postId = $this->postRepo->create($insertData);

        // Return the created post with post_id
        $post = $this->postRepo->find($postId);
        $post['post_id'] = $postId; // Ensure post_id is set

        // Award points for creating a post
        $this->gamificationService->awardPoints(
            (int)$data['author_id'], 
            GamificationService::POINTS_POST_CREATE, 
            'Created a post'
        );

        return $post;
    }

    /**
     * Update post
     * 
     * @param int $postId Post ID
     * @param int $userId User ID (for ownership check)
     * @param array $data Updated data
     * @return bool Success
     */
    public function updatePost(int $postId, int $userId, array $data): bool
    {
        $post = $this->postRepo->find($postId);

        if (!$post) {
            throw new \Exception('Post not found', 404);
        }

        if ($post['author_id'] != $userId) {
            throw new \Exception('Unauthorized', 403);
        }

        if (isset($data['content'])) {
            $data['content'] = $this->sanitize($data['content']);
        }

        return $this->postRepo->update($postId, $data);
    }

    /**
     * Delete post
     * 
     * @param int $postId Post ID
     * @param int $userId User ID (for ownership check)
     * @return bool Success
     */
    public function deletePost(int $postId, int $userId): bool
    {
        $post = $this->postRepo->find($postId);

        if (!$post) {
            throw new \Exception('Post not found', 404);
        }

        if ($post['author_id'] != $userId) {
            throw new \Exception('Unauthorized', 403);
        }

        return $this->postRepo->delete($postId);
    }

    /**
     * Get posts by user
     * 
     * @param int $userId User ID
     * @return array
     */
    public function getUserPosts(int $userId): array
    {
        return $this->postRepo->findByUser($userId);
    }

    /**
     * Search posts
     * 
     * @param string $query Search query
     * @return array
     */
    public function searchPosts(string $query): array
    {
        return $this->postRepo->search($query);
    }

    /**
     * Get trending posts
     * 
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array
     */
    public function getTrendingPosts(?int $limit = null, int $offset = 0): array
    {
        // Use getAllWithDetails style logic but with trending sort
        // Since we added getTrending to repo, we need to manually fetch media for them 
        // OR update getTrending to include media? 
        // For now, let's get the raw trending posts and then attach media if needed.
        // Actually, getAllWithDetails in Repo does a lot. 
        // Let's stick to getTrending returning the list, and if media is needed, we fetch it?
        // Wait, getAllWithUserInfo only returns base info. 
        // We really want getTrendingWithDetails. 
        // For simplicity, let's just use getTrending and if we need media, 
        // we can fetch it or trust the client to fetch details?
        // No, the feed needs media.
        // Let's iterate and add media.
        
        $posts = $this->postRepo->getTrending($limit, $offset);
        
        // Attach media (N+1 problem but acceptable for small limit)
        foreach ($posts as &$post) {
            $media = $this->postRepo->query("SELECT media_id, type as media_type, path as media_path FROM post_media WHERE post_id = ?", [$post['post_id']]);
            $post['media'] = $media;
        }
        
        return $posts;
    }

    /**
     * Get post repository
     * 
     * @return PostRepository
     */
    public function getPostRepo(): PostRepository
    {
        return $this->postRepo;
    }

    /**
     * Get post counts grouped by category
     * 
     * @return array
     */
    public function getCategoryCounts(): array
    {
        return $this->postRepo->getCategoryCounts();
    }
}
