<?php
namespace App\Services;

/**
 * ContentModerationService
 * 
 * Facade service for content moderation.
 * Provides simple interface to validate user-generated content.
 */
class ContentModerationService {
    
    private SentimentAnalyzer $analyzer;
    
    /**
     * Toxicity threshold for blocking content
     * Default: 0.6 (blocks moderately toxic or worse content)
     */
    private const BLOCK_THRESHOLD = 0.6;
    
    /**
     * Warning threshold for flagging content for review
     */
    private const WARNING_THRESHOLD = 0.4;
    
    public function __construct() {
        $this->analyzer = new SentimentAnalyzer();
    }
    
    /**
     * Validate content before saving
     * 
     * @param string $content Content to validate
     * @throws ContentBlockedException If content is toxic
     * @return array Moderation result with details
     */
    public function validateContent(string $content): array {
        $analysis = $this->analyzer->analyze($content);
        
        if ($analysis['is_toxic']) {
            throw new ContentBlockedException(
                'Your content contains inappropriate language or negative sentiment and cannot be posted.',
                $analysis
            );
        }
        
        $result = [
            'allowed' => true,
            'toxicity_score' => $analysis['toxicity_score'],
            'sentiment' => $analysis['sentiment']
        ];
        
        // Add warning if content is borderline
        if ($analysis['toxicity_score'] >= self::WARNING_THRESHOLD) {
            $result['warning'] = 'Your content may be perceived as negative. Consider rephrasing for better engagement.';
        }
        
        return $result;
    }
    
    /**
     * Check if content is safe (returns boolean, no exception)
     * 
     * @param string $content Content to check
     * @return bool True if content is safe
     */
    public function isSafe(string $content): bool {
        return !$this->analyzer->isToxic($content);
    }
    
    /**
     * Get moderation analysis without blocking
     * 
     * @param string $content Content to analyze
     * @return array Analysis result
     */
    public function analyzeContent(string $content): array {
        return $this->analyzer->analyze($content);
    }
    
    /**
     * Batch validate multiple contents
     * 
     * @param array $contents Array of content strings
     * @return array Results for each content
     */
    public function batchValidate(array $contents): array {
        $results = [];
        foreach ($contents as $key => $content) {
            try {
                $results[$key] = $this->validateContent($content);
            } catch (ContentBlockedException $e) {
                $results[$key] = [
                    'allowed' => false,
                    'reason' => $e->getMessage(),
                    'analysis' => $e->getAnalysis()
                ];
            }
        }
        return $results;
    }
}

/**
 * Exception thrown when content is blocked due to toxicity
 */
class ContentBlockedException extends \Exception {
    
    private array $analysis;
    
    public function __construct(string $message, array $analysis) {
        parent::__construct($message, 400);
        $this->analysis = $analysis;
    }
    
    /**
     * Get the toxicity analysis details
     */
    public function getAnalysis(): array {
        return $this->analysis;
    }
    
    /**
     * Get flagged terms
     */
    public function getFlaggedTerms(): array {
        return $this->analysis['flagged_terms'] ?? [];
    }
    
    /**
     * Get toxicity score
     */
    public function getToxicityScore(): float {
        return $this->analysis['toxicity_score'] ?? 0.0;
    }
}
