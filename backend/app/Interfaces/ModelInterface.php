<?php
namespace App\Interfaces;

/**
 * Base Model Interface
 * 
 * Defines standard methods for all models
 */
interface ModelInterface {
    /**
     * Convert model to array representation
     * 
     * @return array Model data as array
     */
    public function toArray(): array;
    
    /**
     * Convert model to JSON string
     * 
     * @return string JSON representation
     */
    public function toJson(): string;
}
