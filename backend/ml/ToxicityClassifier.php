<?php
/**
 * ToxicityClassifier - ML-based text toxicity detection
 * 
 * Uses a Naive Bayes-inspired approach with pre-trained vocabulary weights
 * to detect toxic, offensive, and negative sentiment content.
 * 
 * @author Uni-Link ML Team
 */

namespace ML;

class ToxicityClassifier {
    
    /**
     * Toxicity vocabulary with weights (0.0 to 1.0)
     * Higher weight = more toxic
     */
    private array $toxicVocabulary = [
        // ===== SEVERE TOXICITY (0.9-1.0) =====
        // Extreme profanity and slurs
        'fuck' => 0.95,
        'fucking' => 0.95,
        'fucker' => 0.98,
        'motherfucker' => 1.0,
        'shit' => 0.85,
        'shitty' => 0.85,
        'bullshit' => 0.88,
        'ass' => 0.6,
        'asshole' => 0.92,
        'bitch' => 0.9,
        'bastard' => 0.85,
        'damn' => 0.5,
        'crap' => 0.55,
        'dick' => 0.85,
        'piss' => 0.7,
        'pissed' => 0.65,
        'whore' => 0.95,
        'slut' => 0.95,
        'cunt' => 1.0,
        
        // Hate speech indicators
        'retard' => 0.95,
        'retarded' => 0.95,
        'faggot' => 1.0,
        'fag' => 0.98,
        'nigger' => 1.0,
        'nigga' => 0.95,
        'spic' => 1.0,
        'chink' => 1.0,
        'kike' => 1.0,
        'wetback' => 1.0,
        
        // Threats and violence
        'kill' => 0.75,
        'murder' => 0.8,
        'die' => 0.6,
        'death' => 0.5,
        'suicide' => 0.7,
        'rape' => 0.95,
        'stab' => 0.8,
        'shoot' => 0.75,
        'bomb' => 0.7,
        'attack' => 0.5,
        'destroy' => 0.55,
        'threat' => 0.6,
        'threaten' => 0.65,
        
        // ===== MODERATE TOXICITY (0.6-0.89) =====
        // Strong insults
        'idiot' => 0.75,
        'idiots' => 0.75,
        'idiotic' => 0.75,
        'moron' => 0.78,
        'morons' => 0.78,
        'moronic' => 0.78,
        'stupid' => 0.7,
        'stupidity' => 0.7,
        'dumb' => 0.65,
        'dumbass' => 0.85,
        'loser' => 0.7,
        'losers' => 0.7,
        'pathetic' => 0.68,
        'worthless' => 0.75,
        'useless' => 0.6,
        'trash' => 0.7,
        'garbage' => 0.65,
        'scum' => 0.8,
        'jerk' => 0.65,
        'creep' => 0.65,
        'creepy' => 0.55,
        'freak' => 0.6,
        'weirdo' => 0.55,
        'lame' => 0.5,
        'suck' => 0.6,
        'sucks' => 0.6,
        'sucker' => 0.65,
        'disgusting' => 0.65,
        'gross' => 0.5,
        'nasty' => 0.55,
        'ugly' => 0.6,
        'fat' => 0.55,
        'fatty' => 0.7,
        'skinny' => 0.4,
        
        // Harassment terms
        'harass' => 0.75,
        'harassment' => 0.75,
        'bully' => 0.7,
        'bullying' => 0.7,
        'troll' => 0.55,
        'trolling' => 0.6,
        'spam' => 0.45,
        'spammer' => 0.5,
        'stalker' => 0.7,
        'stalking' => 0.7,
        
        // ===== MILD TOXICITY (0.4-0.59) =====
        // Dismissive/rude language
        'shut up' => 0.55,
        'shutup' => 0.55,
        'idgaf' => 0.6,
        'stfu' => 0.75,
        'gtfo' => 0.75,
        'wtf' => 0.55,
        'lmao' => 0.2,
        'annoying' => 0.45,
        'boring' => 0.35,
        'ridiculous' => 0.4,
        'absurd' => 0.35,
        'terrible' => 0.5,
        'horrible' => 0.55,
        'awful' => 0.5,
        'worst' => 0.5,
        'bad' => 0.35,
        'poor' => 0.3,
        'weak' => 0.35,
        'fail' => 0.4,
        'failure' => 0.45,
        'failed' => 0.4,
        'incompetent' => 0.6,
        'clueless' => 0.55,
        'ignorant' => 0.55,
        'arrogant' => 0.5,
        'selfish' => 0.45,
        'rude' => 0.45,
        'mean' => 0.4,
        'cruel' => 0.6,
        'evil' => 0.5,
        'toxic' => 0.55,
        
        // ===== NEGATIVE SENTIMENT (0.2-0.39) =====
        // Anger indicators
        'hate' => 0.65,
        'hated' => 0.6,
        'hates' => 0.65,
        'hatred' => 0.7,
        'hating' => 0.65,
        'angry' => 0.35,
        'furious' => 0.45,
        'rage' => 0.5,
        'mad' => 0.35,
        'pissed off' => 0.7,
        'frustrated' => 0.3,
        'annoyed' => 0.35,
        'irritated' => 0.35,
        
        // Negative emotions
        'sad' => 0.2,
        'depressed' => 0.35,
        'depression' => 0.35,
        'miserable' => 0.4,
        'unhappy' => 0.3,
        'hopeless' => 0.4,
        'desperate' => 0.35,
        'lonely' => 0.25,
        'alone' => 0.2,
        'abandoned' => 0.35,
        'rejected' => 0.35,
        'unwanted' => 0.35,
        'worthless' => 0.5,
        
        // Disappointment
        'disappointed' => 0.35,
        'disappointing' => 0.4,
        'letdown' => 0.4,
        'regret' => 0.3,
        'sorry' => 0.15,
        'apologize' => 0.1,
        
        // Contempt
        'despise' => 0.7,
        'loathe' => 0.7,
        'detest' => 0.65,
        'disgust' => 0.6,
        'resent' => 0.55,
        'scorn' => 0.6,
        'mock' => 0.5,
        'mocking' => 0.55,
        'ridicule' => 0.55,
        'shame' => 0.45,
        'shameful' => 0.5,
        'embarrassing' => 0.4,
        'humiliate' => 0.6,
        'humiliating' => 0.6,
        
        // ===== CONTEXTUAL NEGATIVES =====
        // These are negative when used to attack someone
        'nobody' => 0.3,
        'nothing' => 0.2,
        'never' => 0.15,
        'wrong' => 0.25,
        'mistake' => 0.25,
        'error' => 0.2,
        'problem' => 0.2,
        'trouble' => 0.25,
        'issue' => 0.15,
        'fault' => 0.3,
        'blame' => 0.4,
        'guilty' => 0.35,
        'liar' => 0.7,
        'lie' => 0.5,
        'lying' => 0.55,
        'cheat' => 0.6,
        'cheater' => 0.65,
        'cheating' => 0.6,
        'fake' => 0.5,
        'fraud' => 0.6,
        'scam' => 0.6,
        'steal' => 0.55,
        'thief' => 0.6,
        'stealing' => 0.55,
        
        // ===== PERSONAL ATTACKS =====
        'you suck' => 0.75,
        'you are dumb' => 0.8,
        'youre dumb' => 0.8,
        'you are stupid' => 0.8,
        'youre stupid' => 0.8,
        'you are an idiot' => 0.85,
        'youre an idiot' => 0.85,
        'go away' => 0.45,
        'get lost' => 0.5,
        'leave me alone' => 0.35,
        'nobody cares' => 0.55,
        'no one cares' => 0.55,
        'who cares' => 0.4,
        'dont care' => 0.35,
        'i dont care' => 0.3,
    ];
    
    /**
     * Positive words that reduce toxicity score
     */
    private array $positiveVocabulary = [
        'good' => 0.3,
        'great' => 0.35,
        'excellent' => 0.4,
        'amazing' => 0.4,
        'wonderful' => 0.4,
        'fantastic' => 0.4,
        'awesome' => 0.35,
        'love' => 0.35,
        'loved' => 0.35,
        'loving' => 0.35,
        'like' => 0.2,
        'enjoy' => 0.25,
        'happy' => 0.35,
        'happiness' => 0.35,
        'joy' => 0.35,
        'joyful' => 0.35,
        'pleased' => 0.3,
        'pleasant' => 0.3,
        'nice' => 0.25,
        'kind' => 0.3,
        'kindness' => 0.35,
        'friendly' => 0.3,
        'helpful' => 0.35,
        'help' => 0.25,
        'thanks' => 0.3,
        'thank' => 0.25,
        'thankful' => 0.35,
        'grateful' => 0.35,
        'appreciate' => 0.3,
        'appreciated' => 0.3,
        'welcome' => 0.25,
        'beautiful' => 0.3,
        'pretty' => 0.25,
        'smart' => 0.25,
        'intelligent' => 0.3,
        'brilliant' => 0.35,
        'talented' => 0.3,
        'creative' => 0.25,
        'impressive' => 0.3,
        'inspiring' => 0.35,
        'motivating' => 0.3,
        'supportive' => 0.35,
        'support' => 0.25,
        'encourage' => 0.3,
        'encouraging' => 0.3,
        'positive' => 0.3,
        'optimistic' => 0.3,
        'hopeful' => 0.3,
        'success' => 0.3,
        'successful' => 0.3,
        'achieve' => 0.25,
        'achievement' => 0.3,
        'congratulations' => 0.35,
        'congrats' => 0.3,
        'proud' => 0.3,
        'excited' => 0.3,
        'exciting' => 0.3,
        'fun' => 0.25,
        'funny' => 0.2,
        'interesting' => 0.25,
        'cool' => 0.2,
        'perfect' => 0.3,
        'best' => 0.25,
        'better' => 0.2,
        'improve' => 0.2,
        'improved' => 0.25,
        'progress' => 0.25,
        'learn' => 0.2,
        'learning' => 0.2,
        'grow' => 0.2,
        'growth' => 0.25,
        'peace' => 0.3,
        'peaceful' => 0.3,
        'calm' => 0.25,
        'relax' => 0.2,
        'relaxing' => 0.25,
        'comfortable' => 0.25,
        'safe' => 0.25,
        'trust' => 0.25,
        'honest' => 0.3,
        'genuine' => 0.3,
        'sincere' => 0.3,
        'respect' => 0.3,
        'respectful' => 0.35,
    ];
    
    /**
     * N-gram patterns for multi-word toxic phrases
     */
    private array $toxicPatterns = [
        '/\bgo\s+to\s+hell\b/i' => 0.85,
        '/\bgo\s+die\b/i' => 0.95,
        '/\bkill\s+yourself\b/i' => 1.0,
        '/\bkys\b/i' => 1.0,
        '/\bpiece\s+of\s+(shit|crap)\b/i' => 0.9,
        '/\bson\s+of\s+a\s+bitch\b/i' => 0.9,
        '/\bshut\s+(the\s+)?(f+u+c*k*|hell)\s+up\b/i' => 0.85,
        '/\bf+\s*u+\s*c+\s*k+/i' => 0.9,  // Spaced profanity
        '/\bs+\s*h+\s*i+\s*t+/i' => 0.8,
        '/\bfu+ck+\b/i' => 0.9,
        '/\bwanna\s+fight\b/i' => 0.7,
        '/\bi\s+(will|ll)\s+(kill|hurt|destroy)\b/i' => 0.9,
        '/\byou\s+(will|ll)\s+(die|regret)\b/i' => 0.8,
        '/\bno\s+one\s+likes\s+you\b/i' => 0.75,
        '/\beveryone\s+hates\s+you\b/i' => 0.8,
        '/\byou\s+(are|re)\s+(the\s+)?worst\b/i' => 0.7,
        '/\bget\s+out\s+of\s+(here|my\s+life)\b/i' => 0.6,
        '/\bdon\'?t\s+talk\s+to\s+me\b/i' => 0.45,
        '/\bi\s+hate\s+(you|this|everything)\b/i' => 0.75,
    ];
    
    /**
     * Toxicity threshold for blocking content
     */
    private float $blockThreshold = 0.6;
    
    /**
     * Analyze text for toxicity
     * 
     * @param string $text Text to analyze
     * @return array Analysis result
     */
    public function analyze(string $text): array {
        $originalText = $text;
        $text = $this->normalizeText($text);
        $words = $this->tokenize($text);
        
        $toxicScore = 0.0;
        $positiveScore = 0.0;
        $flaggedTerms = [];
        $matchCount = 0;
        
        // Check toxic patterns (n-grams) first
        foreach ($this->toxicPatterns as $pattern => $weight) {
            if (preg_match($pattern, $text, $matches)) {
                $toxicScore += $weight;
                $flaggedTerms[] = [
                    'term' => $matches[0],
                    'weight' => $weight,
                    'type' => 'pattern'
                ];
                $matchCount++;
            }
        }
        
        // Check individual words
        foreach ($words as $word) {
            // Check toxic vocabulary
            if (isset($this->toxicVocabulary[$word])) {
                $weight = $this->toxicVocabulary[$word];
                $toxicScore += $weight;
                $flaggedTerms[] = [
                    'term' => $word,
                    'weight' => $weight,
                    'type' => 'word'
                ];
                $matchCount++;
            }
            
            // Check positive vocabulary
            if (isset($this->positiveVocabulary[$word])) {
                $positiveScore += $this->positiveVocabulary[$word];
            }
        }
        
        // Calculate final score (normalize based on text length and matches)
        $wordCount = count($words);
        $normalizedToxicScore = $matchCount > 0 
            ? min(1.0, ($toxicScore / max(1, $matchCount)) * (1 + ($matchCount / max(1, $wordCount))))
            : 0.0;
        
        // Apply positive score reduction (positivity reduces perceived toxicity)
        $finalScore = max(0.0, $normalizedToxicScore - ($positiveScore * 0.3));
        
        // Determine sentiment
        $sentiment = $this->determineSentiment($finalScore, $positiveScore);
        
        return [
            'toxicity_score' => round($finalScore, 3),
            'is_toxic' => $finalScore >= $this->blockThreshold,
            'sentiment' => $sentiment,
            'flagged_terms' => $flaggedTerms,
            'word_count' => $wordCount,
            'raw_toxic_score' => round($toxicScore, 3),
            'positive_score' => round($positiveScore, 3),
            'original_text' => $originalText
        ];
    }
    
    /**
     * Normalize text for analysis
     */
    private function normalizeText(string $text): string {
        // Convert to lowercase
        $text = strtolower($text);
        
        // Replace common leetspeak
        $leetspeak = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '8' => 'b',
            '@' => 'a',
            '$' => 's',
        ];
        $text = strtr($text, $leetspeak);
        
        // Remove excessive punctuation but keep spaces
        $text = preg_replace('/[^\w\s\'-]/u', ' ', $text);
        
        // Normalize whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }
    
    /**
     * Tokenize text into words
     */
    private function tokenize(string $text): array {
        $words = preg_split('/\s+/', $text);
        return array_filter($words, fn($w) => strlen($w) > 1);
    }
    
    /**
     * Determine overall sentiment
     */
    private function determineSentiment(float $toxicScore, float $positiveScore): string {
        if ($toxicScore >= 0.6) {
            return 'negative';
        } elseif ($toxicScore >= 0.3 && $positiveScore < 0.3) {
            return 'negative';
        } elseif ($positiveScore >= 0.5 && $toxicScore < 0.3) {
            return 'positive';
        } else {
            return 'neutral';
        }
    }
    
    /**
     * Get the block threshold
     */
    public function getBlockThreshold(): float {
        return $this->blockThreshold;
    }
    
    /**
     * Set the block threshold
     */
    public function setBlockThreshold(float $threshold): void {
        $this->blockThreshold = max(0.0, min(1.0, $threshold));
    }
    
    /**
     * Check if text should be blocked
     */
    public function shouldBlock(string $text): bool {
        $result = $this->analyze($text);
        return $result['is_toxic'];
    }
}
