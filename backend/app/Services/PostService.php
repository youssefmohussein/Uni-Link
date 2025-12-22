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

    public function __construct(PostRepository $postRepo, CommentRepository $commentRepo)
    {
        $this->postRepo = $postRepo;
        $this->commentRepo = $commentRepo;
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
