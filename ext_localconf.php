<?php
use Cylancer\Usertools\Controller\ChangeEmailController;
use Cylancer\Usertools\Controller\ProfileController;
use Cylancer\Usertools\Controller\ListController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

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

ExtensionUtility::configurePlugin(
    'Usertools',
    'EditProfile',
    [
        ProfileController::class => 'editProfile, doEditProfile, deletePortrait'
    ],
    // non-cacheable actions
    [
        ProfileController::class => 'editProfile, doEditProfile, deletePortrait'
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

ExtensionUtility::configurePlugin(
    'Usertools',
    'ListUsers',
    [
        ListController::class => 'listUsers'
    ],
    // non-cacheable actions
    [
        ListController::class => 'listUsers'
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

ExtensionUtility::configurePlugin(
    'Usertools',
    'ChangeEmail',
    [
        ChangeEmailController::class => 'changeEmail, prepareEmailChange'
    ],
    // non-cacheable actions
    [
        ChangeEmailController::class => 'changeEmail, prepareEmailChange'
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

ExtensionUtility::configurePlugin(
    'Usertools',
    'ConfirmEmailChange',
    [
        ChangeEmailController::class => 'confirmEmailChange, '
    ],
    // non-cacheable actions
    [
        ChangeEmailController::class => 'confirmEmailChange'
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);


ExtensionUtility::configurePlugin(
    'Usertools',
    'ConfirmNewEmail',
    [
        ChangeEmailController::class => 'confirmNewEmail, '
    ],
    // non-cacheable actions
    [
        ChangeEmailController::class => 'confirmNewEmail'
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

ExtensionUtility::configurePlugin(
    'Usertools',
    'ChangePassword',
    [
        ProfileController::class => 'changePassword, doChangePassword'
    ],
    // non-cacheable actions
    [
        ProfileController::class => 'changePassword,doChangePassword'
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);


$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths']['cy_usertools_confirmemail'] = 'EXT:usertools/Resources/Private/Templates/ConfirmEmail/';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths']['cy_usertools_confirmemail'] = 'EXT:usertools/Resources/Private/Layouts/ConfirmEmail/';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths']['cy_usertools_confirmemail'] = 'EXT:usertools/Resources/Private/Partials/ConfirmEmail/';


$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths']['cy_usertools_confirmemailchange'] = 'EXT:usertools/Resources/Private/Templates/ConfirmEmailChange/';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths']['cy_usertools_confirmemailchange'] = 'EXT:usertools/Resources/Private/Layouts/ConfirmEmailChange/';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths']['cy_usertools_confirmemailchange'] = 'EXT:usertools/Resources/Private/Partials/ConfirmEmailChange/';

