<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "usertools"
 *
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'User tools',
    'description' => 'Collection of user tools:  
- list all users
- edit the profile (image, phone number, email address, visualisation properties etc.)
- background task for a newsletter',
    'category' => 'plugin',
    'author' => 'Clemens Gogolin',
    'author_email' => 'service@cylancer.net',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '2.1.3',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'news' => '8.6.0-9.4.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

/** ---- CHANGELOG ----------
2.1.3 :: Add documentarion / change this icons. 

// ---- CHANGELOG ---------- */
