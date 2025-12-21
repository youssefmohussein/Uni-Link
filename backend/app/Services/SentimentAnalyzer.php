<?php
namespace App\Services;

use ML\ToxicityClassifier;

/**
 * SentimentAnalyzer Service
 * 
 * High-level service for analyzing text sentiment and toxicity.
 * Wraps the ToxicityClassifier ML model.
 */
class SentimentAnalyzer {
    
    private ToxicityClassifier $classifier;
    
    public function __construct() {
        $this->classifier = new ToxicityClassifier();
    }
    
    /**
     * Analyze text for sentiment and toxicity
     * 
     * @param string $text Text to analyze
     * @return array Analysis result with keys:
     *               - is_toxic: boolean
     *               - toxicity_score: float (0.0-1.0)
     *               - sentiment: 'positive' | 'neutral' | 'negative'
     *               - flagged_terms: array of detected problematic words
     */
    public function analyze(string $text): array {
        if (empty(trim($text))) {
            return [
                'is_toxic' => false,
                'toxicity_score' => 0.0,
                'sentiment' => 'neutral',
                'flagged_terms' => [],
                'message' => 'Empty text provided'
            ];
        }
        
        $result = $this->classifier->analyze($text);
        
        return [
            'is_toxic' => $result['is_toxic'],
            'toxicity_score' => $result['toxicity_score'],
            'sentiment' => $result['sentiment'],
            'flagged_terms' => array_map(fn($t) => $t['term'], $result['flagged_terms']),
            'details' => [
                'word_count' => $result['word_count'],
                'raw_toxic_score' => $result['raw_toxic_score'],
                'positive_score' => $result['positive_score']
            ]
        ];
    }
    
    /**
     * Quick check if content should be blocked
     * 
     * @param string $text Text to check
     * @return bool True if content should be blocked
     */
    public function isToxic(string $text): bool {
        return $this->classifier->shouldBlock($text);
    }
    
    /**
     * Get the current toxicity threshold
     * 
     * @return float Threshold value (0.0-1.0)
     */
    public function getThreshold(): float {
        return $this->classifier->getBlockThreshold();
    }
    
    /**
     * Set the toxicity threshold
     * 
     * @param float $threshold New threshold value (0.0-1.0)
     */
    public function setThreshold(float $threshold): void {
        $this->classifier->setBlockThreshold($threshold);
    }
    
    /**
     * Get detailed analysis for debugging/admin purposes
     * 
     * @param string $text Text to analyze
     * @return array Full analysis details
     */
    public function getDetailedAnalysis(string $text): array {
        return $this->classifier->analyze($text);
    }
}
