<?php
defined('TYPO3') || die('Access denied.');

/**
 * This file is part of the "user tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C. Gogolin <service@cylancer.net>
 * 
 */

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', $GLOBALS['TCA']['fe_users']['ctrl']['type'], '', 'after:' . $GLOBALS['TCA']['fe_users']['ctrl']['label']);

$tmp_usertools_columns = [

    'allow_display_email' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.allow_display_email',
        'config' => [
            'readOnly' => true,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    'label' => '',
                ]
            ],
            'default' => 0
        ]
    ],
    'allow_display_phone' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.allow_display_phone',
        'config' => [
            'readOnly' => true,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    'label' => '',
                ]
            ],
            'default' => 0
        ]
    ],
    'allow_display_image_internal' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.allow_display_image_internal',
        'config' => [
            'readOnly' => true,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    'label' => '',
                ]
            ],
            'default' => 0
        ]
    ],
    'allow_display_image_public' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.allow_display_image_public',
        'config' => [
            'readOnly' => true,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    'label' => '',
                ]
            ],
            'default' => 0
        ]
    ],
    'currently_off_duty' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.currently_off_duty',
        'onChange' => 'reload',
        'config' => [
            'readOnly' => false,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    'label' => '',
                ]
            ],
            'default' => 0
        ]
    ],
    'currently_off_duty_until' => [
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.currently_off_duty_until',
        'displayCond' => 'FIELD:currently_off_duty:REQ:true',
        'config' => [
            'type' => 'datetime',
            'format' => 'date',
            'dbType' => 'date',
            'readOnly' => true,
        ]
    ],
    'new_email' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.new_email',
        'config' => [
            'readOnly' => true,
            'type' => 'input',
            'size' => 255,
            'eval' => 'trim'
        ]
    ],
    'new_email_token' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.new_email_token',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
            'readOnly' => true,
        ]
    ],
    'confirmed_new_email' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.confirmed_new_email',
        'config' => [
            'readOnly' => true,
            'type' => 'input',
            'size' => 255,
            'eval' => 'trim'
        ]
    ],
    'password_token' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.password_token',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
            'readOnly' => true,
        ]
    ],
    'portrait' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.portrait',
        'config' => [
            'type' => 'file',
            'maxitems' => 1,
            'allowed' => 'common-image-types',
            'readOnly' => false,
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tmp_usertools_columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', '--div--;LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:'
    . 'tx_usertools_domain_model_frontendUser.tab.user_preferences, allow_display_email, allow_display_phone, allow_display_image_internal, '
    . 'new_email, confirmed_new_email, currently_off_duty, currently_off_duty_from, currently_off_duty_until, portrait');

/* inherit and extend the show items from the parent class */

if (isset($GLOBALS['TCA']['fe_users']['types']['0']['showitem'])) {
    $GLOBALS['TCA']['fe_users']['types']['Tx_Usertools_User']['showitem'] = $GLOBALS['TCA']['fe_users']['types']['0']['showitem'];
} elseif (is_array($GLOBALS['TCA']['fe_users']['types'])) {
    // use first entry in types array
    $fe_users_type_definition = reset($GLOBALS['TCA']['fe_users']['types']);
    $GLOBALS['TCA']['fe_users']['types']['Tx_Usertools_User']['showitem'] = $fe_users_type_definition['showitem'];
} else {
    $GLOBALS['TCA']['fe_users']['types']['Tx_Usertools_User']['showitem'] = $fe_users_type_definition['showitem'];
}

$GLOBALS['TCA']['fe_users']['columns'][$GLOBALS['TCA']['fe_users']['ctrl']['type']]['config']['items'][] = [
    'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:fe_users.tx_extbase_type.Tx_Usertools_User',
    'Tx_Usertools_User'
];



