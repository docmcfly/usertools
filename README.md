# Typo3 Extension :: usertools
This extension contains a collection of user tools: 
- frontend plugin for listing of all frontend users 
- frontend plugins for editing of the frontend user profile 
	- image
	- phone number
	- email address
	- password 
	- visualisation properties

## Change log

* 4.1.2 :: UPD : Update (cleaning) TCA configuration
* 4.1.1 :: FIX : Fix the upgrade wizzard.
* 4.1.0 :: FIX/UPD : An email adresse change validates the current and the new email address.
* 4.0.0 :: UPD : Update to TYPO3 13.4.0
* 3.0.2 :: UPD : Update to bootstrap_package 14.0.0
* 3.0.1 :: FIX : fast search : result is displayed in the center of the screen.
* 3.0.0 :: UPD : Update to TYPO3 12.4.0
* 2.3.0 :: Fix : Repair the plugin configuration / registry.
* 2.2.2 :: Fix : List all users -> fix the layout of the fast search form
* 2.2.1 :: Update bootstrap_package dependency: Allows the version 13.
* 2.2.0 :: Removes the dependency on the news extension (Removes the newsletter scheduler task. You can use the cy_newsletter extension).
* 2.1.3 :: Add documentarion / change this icons.

## Limitations

**DEPENDENCIES:**  
	- TYPO3 (13.4.0 - 13.4.99)
	- bootstrap_package (15.0.0 - 15.0.99)

## Installation

You can install the extension via the extensions module or via composer.json. 

![Add the plugin typoscript to the static template](./Documentation/images/screen-insertStaticTemplate.png "Add the plugin typoscript to the static template") 

In the second step you have to add the plugin to the TypoScript. To do this, you need to add the TypoScript of the plugin via the static template. 

## Configuration 

## Using

### Select the your frontend plugin

![Select the frontend plugin.](./Documentation/images/screen-selectFrontendPlugin.png "Select the frontend plugin.") 

#### Frontend plugin: user profiles preferences

* **Visbible frontend user groups** :: The plugin displays the group membership of the user, but only the selected groups.
* **Storage folder for the user image** :: All user pictures are saved in specified folder. 

#### Frontend plugin: Email address change form

* **Redirection link after successful e-mail change ** :: The user gets a address confirm mail with a link (is the email adress correct?). The link refers to the 
specified page. Important: The page must contain the email confirm plugin. 
* **Sender name** :: is the sender name from the address confirm mail. 
* **Sender email address** :: is the sender email address from the address confirm mail. 

#### Frontend plugin: Confirm the new email

You do not have any plugin specific settings. You only need to place this plugin on a public page to confirm the new email address.

#### Frontend plugin: Password change form

You do not have any plugin specific settings. 

#### Frontend plugin: User listing

* **Can always view the phone numbers** :: All frontend user with this group membershhip can always look the phone numbers.
* **Can view the currenty off duty flag** :: All frontend user with this group membershhip can always look the "currenty off duty" flag.
* **Visbible frontend user groups** :: The plugin displays the group membership of the user, but only the selected groups.
* **Alternative link if the user has not public his email address** :: This is displayed if the user has set private the own email. 

### The frontend user data record...

has a new "Private user preferences" sheet. You can see but you can not change the private preference of the frontend user. 

You can only the flag "currently off duty". 




