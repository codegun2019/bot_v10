<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 *
 * 🌍 View all other existing AltumCode projects via https://altumcode.com/
 * 📧 Get in touch for support or general queries via https://altumcode.com/contact
 * 📤 Download the latest version via https://altumcode.com/downloads
 *
 * 🐦 X/Twitter: https://x.com/AltumCode
 * 📘 Facebook: https://facebook.com/altumcode
 * 📸 Instagram: https://instagram.com/altumcode
 */

return [
    'dall-e-2' => [
        'api' => 'openai',
        'name' => 'OpenAI Dall-E 2',
        'max_length' => 1000,
        'sizes' => [
            '256x256', '512x512', '1024x1024'
        ],
        'variants' => [1,2,3,4,5]
    ],

    'dall-e-3' => [
        'api' => 'openai',
        'name' => 'OpenAI Dall-E 3',
        'max_length' => 4000,
        'sizes' => [
            '1024x1024', '1792x1024', '1024x1792'
        ],
        'variants' => [1]
    ],

    'clipdrop' => [
        'api' => 'clipdrop',
        'name' => 'ClipDrop StableDiffusion',
        'max_length' => 1000,
        'sizes' => [
            '1024x1024'
        ],
        'variants' => [1]
    ]
];
