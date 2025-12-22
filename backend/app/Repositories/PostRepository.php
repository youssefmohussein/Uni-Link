<?php
namespace App\Repositories;

/**
 * Post Repository
 * 
 * Handles database operations for posts
 */
class PostRepository extends BaseRepository
{
    protected string $table = 'posts';
    protected string $primaryKey = 'post_id';

    /**
     * Find posts by user
     * 
     * @param int $userId User ID
     * @return array Array of posts
     */
    /**
     * Find posts by user
     * 
     * @param int $userId User ID
     * @param int|null $limit Limit
     * @return array Array of posts
     */
    public function findByUser(int $userId, ?int $limit = null): array
    {
        $sql = "
            SELECT p.*, u.username, u.profile_picture,
                   COUNT(DISTINCT c.comment_id) as comment_count
            FROM {$this->table} p
            LEFT JOIN users u ON p.author_id = u.user_id
            LEFT JOIN comments c ON p.post_id = c.post_id
            WHERE p.author_id = ?
            GROUP BY p.post_id
            ORDER BY p.created_at DESC
        ";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->query($sql, [$userId]);
    }

    /**
     * Count posts by user
     * 
     * @param int $userId User ID
     * @return int Count
     */
    public function countByUser(int $userId): int
    {
        return $this->count(['author_id' => $userId]);
    }

    /**
     * Find posts by category
     * 
     * @param string $category Category name
     * @return array Array of posts
     */
    public function findByCategory(string $category): array
    {
        return $this->query("
            SELECT p.*, u.username, u.profile_picture,
                   COUNT(DISTINCT c.comment_id) as comment_count
            FROM {$this->table} p
            LEFT JOIN users u ON p.author_id = u.user_id
            LEFT JOIN comments c ON p.post_id = c.post_id
            WHERE p.category = ?
            GROUP BY p.post_id
            ORDER BY p.created_at DESC
        ", [$category]);
    }

    /**
     * Search posts
     * 
     * @param string $query Search query
     * @return array Array of posts
     */
    public function search(string $query): array
    {
        $searchTerm = "%{$query}%";
        return $this->query("
            SELECT p.*, u.username, u.profile_picture
            FROM {$this->table} p
            LEFT JOIN users u ON p.author_id = u.user_id
            WHERE p.content LIKE ? OR p.category LIKE ?
            ORDER BY p.created_at DESC
        ", [$searchTerm, $searchTerm]);
    }

    /**
     * Get post with media
     * 
     * @param int $postId Post ID
     * @return array|null Post with media
     */
    public function getWithMedia(int $postId): ?array
    {
        $post = $this->find($postId);

        if (!$post) {
            return null;
        }

        $media = $this->query("
            SELECT media_id, type as media_type, path as media_path FROM post_media WHERE post_id = ?
        ", [$postId]);

        $post['media'] = $media;

        return $post;
    }

    /**
     * Get all posts with user info
     * 
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array Array of posts
     */
    public function getAllWithUserInfo(?int $limit = null, int $offset = 0): array
    {
        $sql = "
            SELECT p.*, u.username, u.profile_picture,
                   COUNT(DISTINCT c.comment_id) as comment_count
            FROM {$this->table} p
            LEFT JOIN users u ON p.author_id = u.user_id
            LEFT JOIN comments c ON p.post_id = c.post_id
            GROUP BY p.post_id
            ORDER BY p.created_at DESC
        ";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        return $this->query($sql);
    }

    /**
     * Get trending posts (ordered by likes + comments)
     * 
     * @param int|null $limit Limit
     * @param int $offset Offset
     * @return array Array of posts
     */
    public function getTrending(?int $limit = null, int $offset = 0): array
    {
        $sql = "
            SELECT p.*, 
                   u.username as author_name,
                   u.profile_picture,
                   f.name as faculty_name,
                   (SELECT COUNT(*) FROM post_interactions pi WHERE pi.post_id = p.post_id AND pi.type IN ('Like', 'like', 'LIKE', 'Love', 'love', 'LOVE', 'celebration', 'Celebration', 'CELEBRATION', 'Save', 'save', 'SAVE', 'Share', 'share', 'SHARE')) as real_likes_count,
                   (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) as real_comments_count,
                   ((SELECT COUNT(*) FROM post_interactions pi WHERE pi.post_id = p.post_id AND pi.type IN ('Like', 'like', 'LIKE', 'Love', 'love', 'LOVE', 'celebration', 'Celebration', 'CELEBRATION', 'Save', 'save', 'SAVE', 'Share', 'share', 'SHARE')) + 
                    (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id)) as real_total_engagement
            FROM {$this->table} p
            LEFT JOIN users u ON p.author_id = u.user_id
            LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
            ORDER BY real_total_engagement DESC, real_likes_count DESC, real_comments_count DESC, p.created_at DESC
        ";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $posts = $this->query($sql);

        // Get media for each post (consistent with getAllWithDetails)
        foreach ($posts as &$post) {
            $media = $this->query("
                SELECT media_id, type as media_type, path as media_path 
                FROM post_media 
                WHERE post_id = ?
            ", [$post['post_id']]);

            $post['media'] = $media;
        }

        return $posts;
    }

    /**
     * Get all posts with complete details (author, faculty, media)
     * 
     * @param int|null $limit Limit number of posts
     * @param int $offset Offset for pagination
     * @return array Array of posts with details
     */
    public function getAllWithDetails(?int $limit = null, int $offset = 0): array
    {
        $sql = "
            SELECT p.*, 
                   u.username as author_name,
                   u.profile_picture,
                   f.name as faculty_name,
                   COUNT(DISTINCT pi.interaction_id) as likes_count,
                   COUNT(DISTINCT c.comment_id) as comments_count
            FROM {$this->table} p
            LEFT JOIN users u ON p.author_id = u.user_id
            LEFT JOIN faculties f ON u.faculty_id = f.faculty_id
            LEFT JOIN post_interactions pi ON p.post_id = pi.post_id AND pi.type = 'Like'
            LEFT JOIN comments c ON p.post_id = c.post_id
            GROUP BY p.post_id, u.username, u.profile_picture, f.name
            ORDER BY p.created_at DESC
        ";

        if ($limit !== null) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }

        $posts = $this->query($sql);

        // Get media for each post
        foreach ($posts as &$post) {
            $media = $this->query("
                SELECT media_id, type as media_type, path as media_path 
                FROM post_media 
                WHERE post_id = ?
            ", [$post['post_id']]);

            $post['media'] = $media;
        }

        return $posts;
    }

    /**
     * Get count of recent posts (last 7 days)
     * 
     * @return int Count of recent posts
     */
    public function getRecentCount(): int
    {
        $result = $this->queryOne("
            SELECT COUNT(*) as count 
            FROM {$this->table} 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Get database connection
     * 
     * @return \PDO
     */
    public function getDb(): \PDO
    {
        return $this->db;
    }

    /**
     * Get post counts grouped by category
     * 
     * @return array Array of category counts
     */
    public function getCategoryCounts(): array
    {
        return $this->query("
            SELECT category, COUNT(*) as count 
            FROM {$this->table} 
            GROUP BY category
        ");
    }
}
