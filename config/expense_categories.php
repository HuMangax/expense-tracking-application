<?php

/*
|--------------------------------------------------------------------------
| Default expense categories
|--------------------------------------------------------------------------
|
| These categories are created for every new user so they have something to
| work with out of the box (they can rename, recolor, or delete any of them).
| The `keywords` are used to auto-categorize imported transactions by matching
| against the merchant / description text — see App\Support\CategoryLibrary.
|
*/

return [
    'defaults' => [
        [
            'name' => 'Groceries',
            'color' => '#10B981',
            'keywords' => [
                'save on foods', 'save-on-foods', 'whole foods', 'trader joe', 'costco',
                'safeway', 'no frills', 'nofrills', 'loblaws', 'superstore', 'sobeys',
                't&t', 'iga', 'freshco', 'food basics', 'grocery', 'supermarket',
            ],
        ],
        [
            'name' => 'Food & Dining',
            'color' => '#F59E0B',
            'keywords' => [
                'restaurant', 'restaura', 'cafe', 'café', 'coffee', 'starbucks',
                'tim horton', 'timhorton', 'mcdonald', 'a&w', 'wendy', 'burger', 'pizza',
                'sushi', 'thai', 'ramen', 'pho', 'bbq', 'grill', 'kitchen', 'bistro',
                'eatery', 'bakery', 'donut', 'doughnut', 'ubereats', 'uber eats',
                'doordash', 'skipthedishes', 'skip the dishes', 'chef', 'diner',
                'tavern', 'pub', 'brewery', 'dining',
            ],
        ],
        [
            'name' => 'Transport',
            'color' => '#3B82F6',
            'keywords' => [
                'uber', 'ubertrip', 'lyft', 'transit', 'translink', 'compass', 'presto',
                'metro card', 'metrocard', 'gas station', 'petro-canada', 'petro canada',
                'petrocan', 'shell', 'esso', 'chevron', 'husky', 'parking', 'impark',
                'taxi', 'via rail', 'car2go', 'modo', 'bcaa', 'toll',
            ],
        ],
        [
            'name' => 'Shopping',
            'color' => '#8B5CF6',
            'keywords' => [
                'amazon', 'amzn', 'staples', 'best buy', 'bestbuy', 'walmart', 'uniqlo',
                'ikea', 'apple store', 'indigo', 'chapters', 'winners', 'marshalls',
                'the bay', 'hudson', 'canadian tire', 'dollarama', 'dollar tree',
                'london drugs', 'home depot', 'lowes', 'rona', 'hardware', 'etsy',
                'aliexpress', 'shein', 'memory express', 'newegg',
            ],
        ],
        [
            'name' => 'Entertainment',
            'color' => '#EC4899',
            'keywords' => [
                'spotify', 'netflix', 'disney', 'crave', 'cineplex', 'cinema',
                'landmark cinemas', 'steam games', 'steampowered', 'playstation', 'xbox',
                'nintendo', 'concert', 'theatre', 'theater', 'prime video', 'youtube',
                'twitch', 'audible', 'patreon', 'hbo', 'apple music', 'epic games',
            ],
        ],
        [
            'name' => 'Bills & Utilities',
            'color' => '#06B6D4',
            'keywords' => [
                'hydro', 'bc hydro', 'fortisbc', 'fortis', 'electric', 'fido', 'rogers',
                'telus', 'bell canada', 'bell mobility', 'shaw', 'koodo', 'virgin plus',
                'virgin mobile', 'lucky mobile', 'chatr', 'internet', 'wireless',
                'insurance', 'mortgage', 'utilities', 'enmax', 'epcor', 'atco',
            ],
        ],
        [
            'name' => 'Health & Fitness',
            'color' => '#EF4444',
            'keywords' => [
                'pharmacy', 'shoppers drug', 'rexall', 'pharmaprix', 'clinic', 'dental',
                'dentist', 'medical', 'physio', 'optical', 'optometr', 'gym', 'goodlife',
                'anytime fitness', 'fitness', 'wellness', 'chiropract', 'massage',
            ],
        ],
        [
            'name' => 'Travel',
            'color' => '#14B8A6',
            'keywords' => [
                'airbnb', 'hotel', 'motel', 'expedia', 'booking.com', 'bookingcom',
                'flight', 'airline', 'air canada', 'aircanada', 'westjet', 'flair air',
                'porter air', 'vrbo', 'marriott', 'hilton', 'hyatt', 'holiday inn',
                'hostel', 'travelocity', 'car rental', 'hertz', 'avis',
            ],
        ],
    ],
];
