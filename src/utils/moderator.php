<?php

class Moderator {

    private static array $bannedWords = [
        'fuck', 'fucking', 'fucked', 'fucker',
        'shit', 'shitting', 'bullshit',
        'ass', 'asshole', 'arse',
        'bitch', 'bitches',
        'bastard',
        'cunt',
        'dick', 'dickhead',
        'piss', 'pissed',
        'cock', 'cockhead',
        'whore', 'slut',
        'wanker', 'wank',
        'twat',
        'crap',
        'damn', 'damnit',
        'hell',
    ];

    public static function filter(string $message): string {
        foreach (self::$bannedWords as $word) {
            $replacement = str_repeat('*', mb_strlen($word));
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
            $message = preg_replace($pattern, $replacement, $message);
        }
        return $message;
    }

    public static function containsProfanity(string $message): bool {
        foreach (self::$bannedWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
            if (preg_match($pattern, $message)) {
                return true;
            }
        }
        return false;
    }
}
