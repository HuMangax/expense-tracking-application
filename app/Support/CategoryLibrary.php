<?php

namespace App\Support;

use App\Models\Category;
use App\Models\User;

/**
 * Source of truth for the default categories and the keyword rules used to
 * auto-categorize imported transactions.
 */
class CategoryLibrary
{
    /**
     * @return array<int, array{name: string, color: string, keywords?: array<int, string>}>
     */
    public static function defaults(): array
    {
        return config('expense_categories.defaults', []);
    }

    /**
     * Create the default categories for a user, skipping any names they
     * already have. Returns the number created.
     */
    public static function seedFor(User $user): int
    {
        $existing = Category::where('user_id', $user->id)
            ->pluck('name')
            ->map(fn ($name) => mb_strtolower($name))
            ->all();

        $created = 0;

        foreach (self::defaults() as $def) {
            if (in_array(mb_strtolower($def['name']), $existing, true)) {
                continue;
            }

            Category::create([
                'user_id' => $user->id,
                'name' => $def['name'],
                'color' => $def['color'],
            ]);

            $created++;
        }

        return $created;
    }

    /**
     * Guess a category name from a transaction description, or null when
     * nothing matches. Longer (more specific) keywords win over shorter ones,
     * so e.g. "ubereats" maps to Food & Dining rather than "uber" -> Transport.
     */
    public static function guessName(string $description): ?string
    {
        $haystack = mb_strtolower($description);

        if (trim($haystack) === '') {
            return null;
        }

        $pairs = [];
        foreach (self::defaults() as $def) {
            foreach ($def['keywords'] ?? [] as $keyword) {
                $pairs[] = [mb_strtolower($keyword), $def['name']];
            }
        }

        usort($pairs, fn ($a, $b) => mb_strlen($b[0]) <=> mb_strlen($a[0]));

        foreach ($pairs as [$keyword, $name]) {
            if ($keyword !== '' && str_contains($haystack, $keyword)) {
                return $name;
            }
        }

        return null;
    }
}
