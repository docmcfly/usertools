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

$icons = [];
foreach (['changeEmail', 'changePassword', 'confirmNewEmail', 'confirmEmailChange', 'editProfile', 'listUsers'] as $key) {
    $icons['usertools-' . $key] = [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:usertools/Resources/Public/Icons/Plugins/' . $key . '.svg',
    ];

}
return $icons;
