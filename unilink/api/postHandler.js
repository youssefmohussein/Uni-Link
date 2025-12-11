import { apiRequest } from "./apiClient";

/* ============================================================
   POST HANDLER
   Handles: posts, post interactions, comments
   ============================================================ */

/**
 * Fetch all posts with author and faculty information
 */
export const getAllPosts = async () => {
    const data = await apiRequest("getAllPosts", "GET");
    if (data.status !== "success") throw new Error(data.message || "Failed to fetch posts");
    return data.data ?? [];
};

/**
 * Fetch a single post by ID with media, comments, and likes
 */
export const getPostById = async (post_id) => {
    const data = await apiRequest(`getPostById/${post_id}`, "GET");
    if (data.status !== "success") throw new Error(data.message || "Failed to fetch post");
    return data.data;
};

/**
 * Create a new post
 * @param {Object} postData - { author_id, faculty_id, category, content, status }
 */
export const addPost = async (postData) => {
    const res = await apiRequest("addPost", "POST", postData);
    if (res.status !== "success") throw new Error(res.message || "Failed to add post");
    return res.post_id;
};

/**
 * Update an existing post
 * @param {Object} postData - { post_id, content?, status? }
 */
export const updatePost = async (postData) => {
    if (!postData.post_id) throw new Error("Missing post_id for update");
    const res = await apiRequest("updatePost", "POST", postData);
    if (res.status !== "success") throw new Error(res.message || "Failed to update post");
    return true;
};

/**
 * Delete a post
 * @param {number} post_id
 */
export const deletePost = async (post_id) => {
    const res = await apiRequest("deletePost", "POST", { post_id });
    if (res.status !== "success") throw new Error(res.message || "Delete failed");
    return true;
};

/* ============================================================
   POST INTERACTIONS (Likes, Reactions)
   ============================================================ */

/**
 * Get all interactions for a specific post
 * @param {number} post_id
 */
export const getInteractionsByPost = async (post_id) => {
    const res = await apiRequest("getInteractionsByPost", "POST", { post_id });
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch interactions");
    return res.data ?? [];
};

/**
 * Add an interaction to a post
 * If user already has a different reaction, it will be updated automatically
 * @param {number} post_id
 * @param {number} user_id
 * @param {string} type - 'Like', 'Love', 'celberation', 'Share', 'Save'
 */
export const addInteraction = async (post_id, user_id, type = "Like") => {
    const res = await apiRequest("addInteraction", "POST", {
        post_id,
        user_id,
        type,
    });
    if (res.status !== "success") throw new Error(res.message || "Failed to add interaction");
    return {
        interaction_id: res.interaction_id,
        action: res.action // 'added' or 'updated'
    };
};

/**
 * Get the current user's reaction for a specific post
 * @param {number} post_id
 * @param {number} user_id
 * @returns {Object|null} - { interaction_id, type, created_at } or null if no reaction
 */
export const getUserReaction = async (post_id, user_id) => {
    const res = await apiRequest("getUserReaction", "POST", {
        post_id,
        user_id,
    });
    if (res.status !== "success") throw new Error(res.message || "Failed to get user reaction");
    return res.data; // null if user hasn't reacted
};

/**
 * Get reaction counts breakdown for a post
 * @param {number} post_id
 * @returns {Object} - { Like: number, Love: number, celberation: number, Share: number, Save: number, total: number }
 */
export const getReactionCounts = async (post_id) => {
    const res = await apiRequest("getReactionCounts", "POST", { post_id });
    if (res.status !== "success") throw new Error(res.message || "Failed to get reaction counts");
    return {
        ...res.data,
        total: res.total
    };
};

/**
 * Delete an interaction
 * @param {number} interaction_id
 */
export const deleteInteraction = async (interaction_id) => {
    const res = await apiRequest("deleteInteraction", "POST", { interaction_id });
    if (res.status !== "success") throw new Error(res.message || "Failed to delete interaction");
    return true;
};

/* ============================================================
   COMMENTS
   ============================================================ */

/**
 * Get comments for a post
 * @param {number} post_id
 */
export const getCommentsByPost = async (post_id) => {
    const res = await apiRequest(`getComments/post/${post_id}`, "GET");
    // CommentController returns array directly, not wrapped in status/data
    return Array.isArray(res) ? res : [];
};

/**
 * Add a comment to a post
 * @param {number} post_id
 * @param {number} user_id
 * @param {string} content
 * @param {number|null} parent_id - For nested replies
 */
export const addComment = async (post_id, user_id, content, parent_id = null) => {
    const res = await apiRequest("addComment", "POST", {
        entity_type: "post",
        entity_id: post_id,
        user_id,
        content,
        parent_id,
    });
    if (res.status !== "success") throw new Error(res.message || "Failed to add comment");
    return true;
};

/**
 * Update a comment
 * @param {number} comment_id
 * @param {number} user_id
 * @param {string} content
 */
export const updateComment = async (comment_id, user_id, content) => {
    const res = await apiRequest("updateComment", "POST", {
        comment_id,
        user_id,
        content,
    });
    if (res.status !== "success") throw new Error(res.message || "Failed to update comment");
    return true;
};

/**
 * Delete a comment
 * @param {number} comment_id
 * @param {number} user_id
 */
export const deleteComment = async (comment_id, user_id) => {
    const res = await apiRequest("deleteComment", "POST", {
        comment_id,
        user_id,
    });
    if (res.status !== "success") throw new Error(res.message || "Failed to delete comment");
    return true;
};

/* ============================================================
   POST MEDIA UPLOAD
   ============================================================ */

/**
 * Upload media files (images/videos) for a post
 * @param {number} post_id
 * @param {FileList} files - Files from input[type="file"]
 */
export const uploadPostMedia = async (post_id, files) => {
    const formData = new FormData();
    formData.append('post_id', post_id);

    // Append all files
    for (let i = 0; i < files.length; i++) {
        formData.append('media[]', files[i]);
    }

    try {
        const response = await fetch('http://localhost/backend/uploadMedia', {
            method: 'POST',
            credentials: 'include',
            body: formData, // Don't set Content-Type header, browser will set it with boundary
        });

        const data = await response.json();
        if (data.status !== "success") throw new Error(data.message || "Failed to upload media");
        return data.data;
    } catch (error) {
        console.error("Upload error:", error);
        throw error;
    }
};

/**
 * Get media for a specific post
 * @param {number} post_id
 */
export const getMediaByPost = async (post_id) => {
    const res = await apiRequest(`getMediaById?post_id=${post_id}`, "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch media");
    return res.data ?? [];
};

/**
 * Search posts by content or hashtags
 * @param {string} query - Search query
 */
export const searchPosts = async (query) => {
    if (!query || !query.trim()) {
        return [];
    }
    const data = await apiRequest(`searchPosts?q=${encodeURIComponent(query)}`, "GET");
    if (data.status !== "success") throw new Error(data.message || "Failed to search posts");
    return data.data ?? [];
};

/* ============================================================
   SAVED POSTS / COLLECTIONS
   ============================================================ */

/**
 * Save a post to user's collection
 * @param {number} user_id
 * @param {number} post_id
 */
export const savePost = async (user_id, post_id) => {
    const res = await apiRequest("savePost", "POST", { user_id, post_id });
    if (res.status !== "success") throw new Error(res.message || "Failed to save post");
    return {
        saved_id: res.saved_id,
        already_saved: res.already_saved
    };
};

/**
 * Unsave/remove a post from user's collection
 * @param {number} user_id
 * @param {number} post_id
 */
export const unsavePost = async (user_id, post_id) => {
    const res = await apiRequest("unsavePost", "POST", { user_id, post_id });
    if (res.status !== "success") throw new Error(res.message || "Failed to unsave post");
    return {
        removed: res.removed
    };
};

/**
 * Get all saved posts for a user
 * @param {number} user_id
 * @returns {Array} - Array of saved posts with full details
 */
export const getSavedPosts = async (user_id) => {
    const data = await apiRequest(`getSavedPosts?user_id=${user_id}`, "GET");
    if (data.status !== "success") throw new Error(data.message || "Failed to fetch saved posts");
    return data.data ?? [];
};

/**
 * Check if a specific post is saved by user
 * @param {number} user_id
 * @param {number} post_id
 * @returns {Object} - { is_saved: boolean, saved_id: number|null }
 */
export const checkIfPostSaved = async (user_id, post_id) => {
    const res = await apiRequest(`isPostSaved?user_id=${user_id}&post_id=${post_id}`, "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to check save status");
    return {
        is_saved: res.is_saved,
        saved_id: res.saved_id
    };
};

