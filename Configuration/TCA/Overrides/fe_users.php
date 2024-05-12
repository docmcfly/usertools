<?php
defined('TYPO3') || die('Access denied.');

if (! isset($GLOBALS['TCA']['fe_users']['ctrl']['type'])) {
    // no type field defined, so we define it here. This will only happen the first time the extension is installed!!
    $GLOBALS['TCA']['fe_users']['ctrl']['type'] = 'tx_extbase_type';
    $tempColumnstx_usertools_fe_users = [];
    $tempColumnstx_usertools_fe_users[$GLOBALS['TCA']['fe_users']['ctrl']['type']] = [
        'exclude' => true,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools.tx_extbase_type',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                [
                    '',
                    ''
                ],
                [
                    'User',
                    'Tx_Usertools_User'
                ]
            ],
            'default' => 'Tx_Usertools_User',
            'size' => 1,
            'maxitems' => 1
        ]
    ];
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tempColumnstx_usertools_fe_users);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', $GLOBALS['TCA']['fe_users']['ctrl']['type'], '', 'after:' . $GLOBALS['TCA']['fe_users']['ctrl']['label']);

$tmp_usertools_columns = [

    'allow_display_email' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.allow_display_email',
        'config' => [
            'readOnly' => 1,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    0 => '',
                    1 => '',
                ]
            ],
            'default' => 0
        ]
    ],
    'allow_display_phone' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.allow_display_phone',
        'config' => [
            'readOnly' => 1,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    0 => '',
                    1 => '',
                ]
            ],
            'default' => 0
        ]
    ],
    'allow_display_image_internal' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.allow_display_image_internal',
        'config' => [
            'readOnly' => 1,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    0 => '',
                    1 => '',
                ]
            ],
            'default' => 0
        ]
    ],
    'allow_display_image_public' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.allow_display_image_public',
        'config' => [
            'readOnly' => 1,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    0 => '',
                    1 => '',
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
            'readOnly' => 0,
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [
                [
                    0 => '',
                    1 => '',
                ]
            ],
            'default' => 0
        ]
    ],
    'currently_off_duty_until' => [
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.currently_off_duty_until',
        'displayCond' => 'FIELD:currently_off_duty:REQ:true',
        'config' => [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'dbType' => 'date',
            'eval' => 'date,int',
            'default' => 0,
        ]
    ],
    'new_email' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.new_email',
        'config' => [
            'readOnly' => 1,
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ]
    ],
    'new_email_token' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.new_email_token',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ]
    ],
    'password_token' => [
        'exclude' => false,
        'label' => 'LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_user.password_token',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ]
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tmp_usertools_columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('fe_users', '--div--;LLL:EXT:usertools/Resources/Private/Language/locallang_db.xlf:tx_usertools_domain_model_frontendUser.tab.user_preferences, allow_display_email, allow_display_phone, allow_display_image_internal, new_email, currently_off_duty, currently_off_duty_from, currently_off_duty_until');

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



