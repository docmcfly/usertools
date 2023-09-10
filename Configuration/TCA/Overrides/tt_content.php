<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

(static function (): void{


    ExtensionUtility::registerPlugin(
        'Usertools',
        'Editprofile',
        'LLL:EXT:usertools/Resources/Private/Language/locallang_be_editProfile.xlf:plugin.name'
    );

    ExtensionUtility::registerPlugin(
        'Usertools',
        'Listusers',
        'LLL:EXT:usertools/Resources/Private/Language/locallang_be_listUsers.xlf:plugin.name'
    );

    ExtensionUtility::registerPlugin(
        'Usertools',
        'Changeemailform',
        'LLL:EXT:usertools/Resources/Private/Language/locallang_be_changeEmail.xlf:plugin.name'
    );

    ExtensionUtility::registerPlugin(
        'Usertools',
        'Confirmnewemail',
        'LLL:EXT:usertools/Resources/Private/Language/locallang_be_confirmNewEmail.xlf:plugin.name'
    );

    ExtensionUtility::registerPlugin(
        'Usertools',
        'Changeuserpassword',
        'LLL:EXT:usertools/Resources/Private/Language/locallang_be_changePassword.xlf:plugin.name'
    );


    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['usertools_changeemailform'] = 'pi_flexform';
    ExtensionManagementUtility::addPiFlexFormValue(
        // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
        'usertools_changeemailform',
        // Flexform configuration schema file
        'FILE:EXT:usertools/Configuration/FlexForms/ChangeEmail.xml'
    );
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['usertools_listusers'] = 'pi_flexform';
    ExtensionManagementUtility::addPiFlexFormValue(
        // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
        'usertools_listusers',
        // Flexform configuration schema file
        'FILE:EXT:usertools/Configuration/FlexForms/ListUsers.xml'
    );
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['usertools_editprofile'] = 'pi_flexform';
    ExtensionManagementUtility::addPiFlexFormValue(
        // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
        'usertools_editprofile',
        // Flexform configuration schema file
        'FILE:EXT:usertools/Configuration/FlexForms/EditProfile.xml'
    );

})();