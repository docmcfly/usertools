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
- change your password
- change your email address',
    'category' => 'plugin',
    'author' => 'Clemens Gogolin',
    'author_email' => 'service@cylancer.net',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '3.0.2',
    'autoload' => [
        'psr-4' => [
            'Cylancer\\Usertools\\' =>'Classes/',
            
        ],
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'bootstrap_package' => '14.0.0-14.0.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

/** ---- CHANGELOG ----------
 * 
3.0.2 :: UPD : Update to bootstrap_package 14.0.0
3.0.1 :: FIX : fast search : result is displayed in the center of the screen.
3.0.0 :: UPD : Update to TYPO3 12.4.0
2.3.0 :: Fix : Repair the plugin configuration / registry.
2.2.2 :: Fix : List all users -> fix the layout of the fast search form 
2.2.1 :: Update bootstrap_package dependency: Allows the version 13. 
2.2.0 :: Removes the dependency on the news extension (Removes the newsletter scheduler task. You can use the cy_newsletter extension).
2.1.3 :: Add documentarion / change this icons. 
 ---- CHANGELOG ---------- */
