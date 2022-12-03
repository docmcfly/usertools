<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function () {


    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('Cylancer.Usertools', 'Editprofile', 'LLL:EXT:usertools/Resources/Private/Language/locallang_be_editProfile.xlf:plugin.name');

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('Cylancer.Usertools', 'Listusers', 'LLL:EXT:usertools/Resources/Private/Language/locallang_be_listUsers.xlf:plugin.name');

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('Cylancer.Usertools', 'Changeemailform', 'LLL:EXT:usertools/Resources/Private/Language/locallang_be_changeEmail.xlf:plugin.name');

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('Cylancer.Usertools', 'Confirmnewemail', 'LLL:EXT:usertools/Resources/Private/Language/locallang_be_confirmEmail.xlf:plugin.name');

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('Cylancer.Usertools', 'Changeuserpassword', 'LLL:EXT:usertools/Resources/Private/Language/locallang_be_changePassword.xlf:plugin.name');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('usertools', 'Configuration/TypoScript', 'User tools');
});
