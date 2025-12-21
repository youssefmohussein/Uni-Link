-- Migration: Add VOICE message type to chat_messages
-- Date: 2025-12-21
-- Description: Adds VOICE to the message_type ENUM to support voice messages

ALTER TABLE `chat_messages` 
MODIFY COLUMN `message_type` ENUM('TEXT', 'FILE', 'IMAGE', 'VOICE', 'SYSTEM') DEFAULT 'TEXT';
