<?php
/**
 * Dependency Injection Container
 * 
 * Simple DI container for managing dependencies
 */

namespace App\Utils;

class Container {
    private static ?Container $instance = null;
    private array $services = [];
    private array $singletons = [];
    
    private function __construct() {}
    
    public static function getInstance(): Container {
        if (self::$instance === null) {
            self::$instance = new Container();
        }
        return self::$instance;
    }
    
    /**
     * Register a service factory
     */
    public function set(string $name, callable $factory): void {
        $this->services[$name] = $factory;
    }
    
    /**
     * Register a singleton service
     */
    public function singleton(string $name, callable $factory): void {
        $this->services[$name] = $factory;
        $this->singletons[$name] = true;
    }
    
    /**
     * Get a service
     */
    public function get(string $name) {
        if (!isset($this->services[$name])) {
            throw new \Exception("Service {$name} not found");
        }
        
        // Return singleton if already created
        if (isset($this->singletons[$name]) && isset($this->instances[$name])) {
            return $this->instances[$name];
        }
        
        // Create instance
        $instance = $this->services[$name]($this);
        
        // Store singleton
        if (isset($this->singletons[$name])) {
            $this->instances[$name] = $instance;
        }
        
        return $instance;
    }
    
    private array $instances = [];
}
