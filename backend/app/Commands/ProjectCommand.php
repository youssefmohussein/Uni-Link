<?php
namespace App\Commands;

/**
 * ProjectCommand Interface
 * 
 * Command Pattern Implementation
 * All project commands must implement this interface
 * Part of the Project Domain (UML Design)
 */
interface ProjectCommand {
    /**
     * Execute the command
     * 
     * @return bool Success status
     */
    public function execute(): bool;
}
