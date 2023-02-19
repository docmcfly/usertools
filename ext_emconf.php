<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "usertools"
 *
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'User tools',
    'description' => 'Collection of user tools:  
- list all users
- edit the profile (image, phone number, email address, visualisation properties etc.)',
    'category' => 'plugin',
    'author' => 'Clemens Gogolin',
    'author_email' => 'service@cylancer.net',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '2.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'bootstrap_package' => '12.0.0-12.99.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

/** ---- CHANGELOG ----------
2.1.3 :: Add documentarion / change this icons. 
2.2.0 :: Removes the news extension dependecy (Removes the newsletter task. You can use the scheduler task cy_newsletter.) 
// ---- CHANGELOG ---------- */
