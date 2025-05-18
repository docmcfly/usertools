<?php


/**
 * This file is part of the "user tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C. Gogolin <service@cylancer.net>
 * 
 */ 

$EM_CONF[$_EXTKEY] = [
    'title' => 'User tools',
    'description' => 'Collection of user tools:  
- list all users
- edit the profile (image, phone number, email address, visualisation properties etc.)
- change your password
- change your email address',
    'category' => 'plugin',
    'author' => 'C. Gogolin',
    'author_email' => 'service@cylancer.net',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '4.1.1',
    'autoload' => [
        'psr-4' => [
            'Cylancer\\Usertools\\' => 'Classes/',

        ],
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'bootstrap_package' => '14.0.0-15.0.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

/** ---- CHANGELOG ----------
 * 
4.1.1 :: FIX : Fix the upgrade wizzard. 
4.1.0 :: FIX/UPD : An email adresse change validates the current and the new email address.
4.0.0 :: UPD : Update to TYPO3 13.4.0
3.0.2 :: UPD : Update to bootstrap_package 14.0.0
3.0.1 :: FIX : fast search : result is displayed in the center of the screen.
3.0.0 :: UPD : Update to TYPO3 12.4.0
2.3.0 :: Fix : Repair the plugin configuration / registry.
2.2.2 :: Fix : List all users -> fix the layout of the fast search form 
2.2.1 :: Update bootstrap_package dependency: Allows the version 13. 
2.2.0 :: Removes the dependency on the news extension (Removes the newsletter scheduler task. You can use the cy_newsletter extension).
2.1.3 :: Add documentarion / change this icons. 
// ---- CHANGELOG ---------- */
