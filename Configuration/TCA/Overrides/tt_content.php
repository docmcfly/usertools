<?php
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['usertools_changeemailform'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
    'usertools_changeemailform', 
    // Flexform configuration schema file
    'FILE:EXT:usertools/Configuration/FlexForms/ChangeEmail.xml');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['usertools_listusers'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
    'usertools_listusers', 
    // Flexform configuration schema file
    'FILE:EXT:usertools/Configuration/FlexForms/ListUsers.xml');
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['usertools_editprofile'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    // plugin signature: <extension key without underscores> '_' <plugin name in lowercase>
    'usertools_editprofile', 
    // Flexform configuration schema file
    'FILE:EXT:usertools/Configuration/FlexForms/EditProfile.xml');
?>
