<?php
namespace App\Service;

class ProfanityFilterService
{
    // List of bad words to check for
    private array $badWords = [
        'fuck', 'shit', 'damn', 'ass', 'bitch', // Expanded list
        'cunt', 'piss', 'cock', 'bastard', 'whore' // Add more as needed
    ];

    // Function to censor bad words in the text
    public function censorText(string $text): string
    {
        foreach ($this->badWords as $badWord) {
            $pattern = '/\b' . preg_quote($badWord, '/') . '\b/i'; // Case-insensitive, exact word match
            $text = preg_replace($pattern, str_repeat('*', strlen($badWord)), $text);
        }
        return $text;
    }
}