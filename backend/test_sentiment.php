<?php
/**
 * Sentiment Analysis Test Script
 * 
 * Tests the ML toxicity classifier with various content types
 */

require_once __DIR__ . '/ml/ToxicityClassifier.php';
require_once __DIR__ . '/app/Services/SentimentAnalyzer.php';
require_once __DIR__ . '/app/Services/ContentModerationService.php';

use ML\ToxicityClassifier;
use App\Services\SentimentAnalyzer;
use App\Services\ContentModerationService;
use App\Services\ContentBlockedException;

echo "==============================================\n";
echo "  ML Sentiment Analysis Test Suite\n";
echo "==============================================\n\n";

$analyzer = new SentimentAnalyzer();
$moderator = new ContentModerationService();

// Test cases
$testCases = [
    // Positive/Neutral cases - should PASS
    [
        'text' => 'Hello everyone! Great to be here!',
        'expected' => 'pass',
        'description' => 'Friendly greeting'
    ],
    [
        'text' => 'I just finished my project and I am really happy with the results!',
        'expected' => 'pass',
        'description' => 'Positive achievement'
    ],
    [
        'text' => 'Can anyone help me with this assignment? Thanks in advance!',
        'expected' => 'pass',
        'description' => 'Polite help request'
    ],
    [
        'text' => 'The lecture was interesting but I found some parts confusing.',
        'expected' => 'pass',
        'description' => 'Constructive feedback'
    ],
    [
        'text' => 'Welcome to the group! Feel free to ask questions.',
        'expected' => 'pass',
        'description' => 'Welcoming message'
    ],
    
    // Toxic cases - should BLOCK
    [
        'text' => 'You are all idiots and I hate everyone here!',
        'expected' => 'block',
        'description' => 'Insults and hate'
    ],
    [
        'text' => 'This is bullshit, what the fuck is this garbage?',
        'expected' => 'block',
        'description' => 'Profanity'
    ],
    [
        'text' => 'Shut up you moron, nobody cares about your stupid opinion',
        'expected' => 'block',
        'description' => 'Personal attack'
    ],
    [
        'text' => 'Go kill yourself, you worthless piece of trash',
        'expected' => 'block',
        'description' => 'Severe threat'
    ],
    [
        'text' => 'You stupid retard, get out of here',
        'expected' => 'block',
        'description' => 'Slurs'
    ],
    
    // Edge cases
    [
        'text' => 'This movie was so bad it made me want to cry',
        'expected' => 'pass',
        'description' => 'Negative opinion (mild)'
    ],
    [
        'text' => 'I hate Mondays',
        'expected' => 'pass',
        'description' => 'Common expression'
    ],
    [
        'text' => 'F U C K this assignment',
        'expected' => 'block',
        'description' => 'Spaced profanity'
    ],
    [
        'text' => 'You are the worst professor ever, completely useless',
        'expected' => 'block',
        'description' => 'Strong criticism with insults'
    ],
];

$passed = 0;
$failed = 0;

foreach ($testCases as $index => $test) {
    $result = $analyzer->analyze($test['text']);
    $actualResult = $result['is_toxic'] ? 'block' : 'pass';
    $success = ($actualResult === $test['expected']);
    
    if ($success) {
        $passed++;
        $status = "✓ PASS";
    } else {
        $failed++;
        $status = "✗ FAIL";
    }
    
    echo "Test #" . ($index + 1) . ": {$test['description']}\n";
    echo "  Content: \"{$test['text']}\"\n";
    echo "  Expected: {$test['expected']} | Actual: {$actualResult}\n";
    echo "  Score: {$result['toxicity_score']} | Sentiment: {$result['sentiment']}\n";
    if (!empty($result['flagged_terms'])) {
        echo "  Flagged: " . implode(', ', $result['flagged_terms']) . "\n";
    }
    echo "  Result: {$status}\n\n";
}

echo "==============================================\n";
echo "  Results: {$passed} passed, {$failed} failed\n";
echo "==============================================\n\n";

// Integration test with ContentModerationService
echo "Testing ContentModerationService Integration...\n\n";

$integrationTests = [
    'Hello world, nice to meet you!',
    'This is f***ing ridiculous',
];

foreach ($integrationTests as $content) {
    echo "Testing: \"{$content}\"\n";
    try {
        $result = $moderator->validateContent($content);
        echo "  ✓ Content allowed (toxicity: {$result['toxicity_score']})\n";
    } catch (ContentBlockedException $e) {
        echo "  ✗ Content BLOCKED: " . $e->getMessage() . "\n";
        echo "    Toxicity: " . $e->getToxicityScore() . "\n";
    }
    echo "\n";
}

echo "Test completed!\n";
