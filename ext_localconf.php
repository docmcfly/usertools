<?php
use Cylancer\Usertools\Controller\ProfileController;
use Cylancer\Usertools\Controller\ListController;

defined('TYPO3') || die('Access denied.');

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Usertools',
        'Editprofile',
        [
            ProfileController::class => 'editProfile, doEditProfile, deleteCurrentUserImages'
        ],
        // non-cacheable actions
        [
            ProfileController::class => 'editProfile, doEditProfile, deleteCurrentUserImages'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Usertools',
        'Listusers',
        [
            ListController::class => 'listAll'
        ],
        // non-cacheable actions
        [
            ListController::class => 'listAll'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Usertools',
        'Changeemailform',
        [
            ProfileController::class => 'changeEmailForm, prepareEmailChange, changeEmail'
        ],
        // non-cacheable actions
        [
            ProfileController::class => 'changeEmailForm, prepareEmailChange, changeEmail'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Usertools',
        'Confirmnewemail',
        [
            ProfileController::class => 'confirmNewEmail'
        ],
        // non-cacheable actions
        [
            ProfileController::class => 'confirmNewEmail'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Usertools',
        'Changeuserpassword',
        [
            ProfileController::class => 'changePasswordForm, changePassword'
        ],
        // non-cacheable actions
        [
            ProfileController::class => 'changePasswordForm, changePassword'
        ]
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                   editprofile {
                        iconIdentifier = usertools-plugin-editprofile
                        title = LLL:EXT:usertools/Resources/Private/Language/locallang_be_editProfile.xlf:plugin.name
                        description = LLL:EXT:usertools/Resources/Private/Language/locallang_be_editProfile.xlf:plugin.description
                        tt_content_defValues {
                            CType = list
                            list_type = usertools_editprofile
                        }
                    }
                    listusers {
                        iconIdentifier = usertools-plugin-listusers
                        title = LLL:EXT:usertools/Resources/Private/Language/locallang_be_listUsers.xlf:plugin.name
                        description = LLL:EXT:usertools/Resources/Private/Language/locallang_be_listUsers.xlf:plugin.description
                        tt_content_defValues {
                            CType = list
                            list_type = usertools_listusers
                        }
                    }
                    changeemailform {
                        iconIdentifier = usertools-plugin-changeemailform
                        title = LLL:EXT:usertools/Resources/Private/Language/locallang_be_changeEmail.xlf:plugin.name
                        description = LLL:EXT:usertools/Resources/Private/Language/locallang_be_changeEmail.xlf:plugin.description
                        tt_content_defValues {
                            CType = list
                            list_type = usertools_changeemailform
                        }
                    }
                    confirmnewemail {
                        iconIdentifier = usertools-plugin-confirmnewemail
                        title = LLL:EXT:usertools/Resources/Private/Language/locallang_be_confirmNewEmail.xlf:plugin.name
                        description = LLL:EXT:usertools/Resources/Private/Language/locallang_be_confirmNewEmail.xlf:plugin.description
                        tt_content_defValues {
                            CType = list
                            list_type = usertools_confirmnewemail
                        }
                    }
                    changeuserpassword {
                        iconIdentifier = usertools-plugin-changeuserpassword
                        title = LLL:EXT:usertools/Resources/Private/Language/locallang_be_changePassword.xlf:plugin.name
                        description = LLL:EXT:usertools/Resources/Private/Language/locallang_be_changePassword.xlf:plugin.description
                        tt_content_defValues {
                            CType = list
                            list_type = usertools_changeuserpassword
                        }
                    }
                }
                show = *
            }
       }');
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

    $iconRegistry->registerIcon('usertools-plugin-editprofile', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:usertools/Resources/Public/Icons/user_plugin_editprofile.svg'
    ]);

    $iconRegistry->registerIcon('usertools-plugin-listusers', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:usertools/Resources/Public/Icons/user_plugin_listusers.svg'
    ]);

    $iconRegistry->registerIcon('usertools-plugin-changeemailform', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:usertools/Resources/Public/Icons/user_plugin_changeemailform.svg'
    ]);

    $iconRegistry->registerIcon('usertools-plugin-confirmnewemail', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:usertools/Resources/Public/Icons/user_plugin_confirmnewemail.svg'
    ]);

    $iconRegistry->registerIcon('usertools-plugin-changeuserpassword', \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class, [
        'source' => 'EXT:usertools/Resources/Public/Icons/user_plugin_changeuserpassword.svg'
    ]);

    $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths']['cy_usertools']    = 'EXT:usertools/Resources/Private/Templates/ConfirmEmail/';
    $GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths']['cy_usertools']    = 'EXT:usertools/Resources/Private/Layouts/ConfirmEmail/';
    $GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths']['cy_usertools']    = 'EXT:usertools/Resources/Private/Partials/ConfirmEmail/';

});