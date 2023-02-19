<?php
namespace Cylancer\Usertools\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 *
 * This file is part of the "User tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Clemens Gogolin <service@cylancer.net>
 *
 * @package Cylancer\Usertools\Domain\Model
 */
class FrontendUser extends AbstractEntity
{

    /**
     *
     * @var bool
     */
    protected $allowDisplayEmail = false;

    /**
     *
     * @var bool
     */
    protected $allowDisplayImageInternal = false;

    /**
     *
     * @var bool
     */
    protected $allowDisplayImagePublic = false;

    /**
     *
     * @var bool
     */
    protected $currentlyOffDuty = false;

    /**
     *
     * @var string
     */
    protected $newEmail = '';

    /**
     *
     * @var string
     */
    protected $newEmailToken = '';

    /**
     *
     * @var string
     */
    protected $passwordToken = '';

    /**
     *
     * @var bool
     */
    protected $allowDisplayPhone = false;

    /**
     *
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected $usergroup;

    /**
     *
     * @var ObjectStorage<FileReference>
     */
    protected $image;

    /**
     *
     * @var array
     */
    protected $uploadedImage;

    /**
     *
     * @var string
     */
    protected $telephone = '';

    /**
     *
     * @var string
     */
    protected $username = '';

    /**
     *
     * @var string
     */
    protected $name = '';

    /**
     *
     * @var string
     */
    protected $firstName = '';

    /**
     *
     * @var string
     */
    protected $lastName = '';

    /**
     *
     * @var string
     */
    protected $password = '';

    /**
     *
     * @var string
     */
    protected $email = '';

    /**
     * Constructs a new Front-End User
     */
    public function __construct()
    {
        $this->usergroup = new ObjectStorage();
        $this->image = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject()
    {
        $this->usergroup = $this->usergroup ?? new ObjectStorage();
        $this->image = $this->image ?? new ObjectStorage();
    }

    /**
     * Returns the allowDisplayEmail
     *
     * @return bool $allowDisplayEmail
     */
    public function getAllowDisplayEmail(): bool
    {
        return $this->allowDisplayEmail;
    }

    /**
     * Sets the allowDisplayEmail
     *
     * @param bool $allowDisplayEmail
     * @return void
     */
    public function setAllowDisplayEmail(bool $allowDisplayEmail): void
    {
        $this->allowDisplayEmail = $allowDisplayEmail;
    }

    /**
     * Returns the allowDisplayImageInternal
     *
     * @return bool allowDisplayImageInternal
     */
    public function getAllowDisplayImageInternal(): bool
    {
        return $this->allowDisplayImageInternal;
    }

    /**
     * Sets the allowDisplayImageInternal
     *
     * @param bool $allowDisplayImageInternal
     * @return void
     */
    public function setAllowDisplayImageInternal(bool $allowDisplayImageInternal): void
    {
        $this->allowDisplayImageInternal = $allowDisplayImageInternal;
    }

    /**
     * Returns the allowDisplayImagePublic
     *
     * @return bool $allowDisplayImagePublic
     */
    public function getAllowDisplayImagePublic(): bool
    {
        return $this->allowDisplayImagePublic;
    }

    /**
     * Sets the allowDisplayImagePublic
     *
     * @param bool $allowDisplayImagePublic
     * @return void
     */
    public function setAllowDisplayImagePublic(bool $allowDisplayImagePublic): void
    {
        $this->allowDisplayImagePublic = $allowDisplayImagePublic;
    }

    /**
     * Returns the allowDisplayImagePublic
     *
     * @return bool $allowDisplayImagePublic
     */
    public function getCurrentlyOffDuty(): bool
    {
        return $this->currentlyOffDuty;
    }

    /**
     * Sets the currentlyOffDuty
     *
     * @param bool $currentlyOffDuty
     * @return void
     */
    public function setCurrentlyOffDuty(bool $currentlyOffDuty): void
    {
        $this->currentlyOffDuty = $currentlyOffDuty;
    }

    /**
     * Returns the newEmail
     *
     * @return string $newEmail
     */
    public function getNewEmail(): ?String
    {
        return $this->newEmail;
    }

    /**
     * Sets the newEmail
     *
     * @param string $newEmail
     * @return void
     */
    public function setNewEmail(String $newEmail): void
    {
        $this->newEmail = $newEmail;
    }

    /**
     * Returns the newEmailToken
     *
     * @return string $newEmailToken
     */
    public function getNewEmailToken(): ?String
    {
        return $this->newEmailToken;
    }

    /**
     * Sets the newEmailToken
     *
     * @param string $newEmailToken
     * @return void
     */
    public function setNewEmailToken(String $newEmailToken): void
    {
        $this->newEmailToken = $newEmailToken;
    }

    /**
     * Returns the passwordToken
     *
     * @return string $passwordToken
     */
    public function getPasswordToken(): ?String
    {
        return $this->passwordToken;
    }

    /**
     * Sets the newEmailToken
     *
     * @param string $newEmailToken
     * @return void
     */
    public function setPasswordToken(String $passwordToken): void
    {
        $this->passwordToken = $passwordToken;
    }

    /**
     * Returns the allowDisplayPhone
     *
     * @return bool $allowDisplayPhone
     */
    public function getAllowDisplayPhone(): bool
    {
        return $this->allowDisplayPhone;
    }

    /**
     * Sets the allowDisplayPhone
     *
     * @param bool $allowDisplayPhone
     * @return void
     */
    public function setAllowDisplayPhone(bool $allowDisplayPhone): void
    {
        $this->allowDisplayPhone = $allowDisplayPhone;
    }

    /**
     * Sets the usergroups.
     * Keep in mind that the property is called "usergroup"
     * although it can hold several usergroups.
     *
     * @param ObjectStorage<FrontendUserGroup> $usergroup
     */
    public function setUsergroup(ObjectStorage $usergroup)
    {
        $this->usergroup = $usergroup;
    }

    /**
     * Adds a usergroup to the frontend user
     *
     * @param FrontendUserGroup $usergroup
     */
    public function addUsergroup(FrontendUserGroup $usergroup)
    {
        $this->usergroup->attach($usergroup);
    }

    /**
     * Removes a usergroup from the frontend user
     *
     * @param FrontendUserGroup $usergroup
     */
    public function removeUsergroup(FrontendUserGroup $usergroup)
    {
        $this->usergroup->detach($usergroup);
    }

    /**
     * Returns the usergroups.
     * Keep in mind that the property is called "usergroup"
     * although it can hold several usergroups.
     *
     * @return ObjectStorage<FrontendUserGroup> An object storage containing the usergroup
     */
    public function getUsergroup()
    {
        return $this->usergroup;
    }

    /**
     * Sets the image value
     *
     * @param ObjectStorage<FileReference> $image
     */
    public function setImage(ObjectStorage $image): void
    {
        $this->image = $image;
    }

    /**
     * Gets the image value
     *
     * @return ObjectStorage<FileReference>
     */
    public function getImage(): ObjectStorage
    {
        return $this->image;
    }

    /**
     *
     * @return array
     */
    public function getUploadedImage(): ?array
    {
        return $this->uploadedImage;
    }

    /**
     *
     * @param array $uploadedImage
     * @return void
     */
    public function setUploadedImage($uploadedImage): void
    {
        $this->uploadedImage = $uploadedImage;
    }

    /**
     * Sets the telephone value
     *
     * @param string $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Returns the telephone value
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Sets the username value
     *
     * @param string $username
     */
    public function setUsername(String $username): void
    {
        $this->username = $username;
    }

    /**
     * Returns the username value
     *
     * @return string
     */
    public function getUsername(): String
    {
        return $this->username;
    }

    /**
     * Sets the name value
     *
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the name value
     *
     * @return string
     */
    public function getName(): String
    {
        return $this->name;
    }

    /**
     * Sets the firstName value
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Returns the firstName value
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Sets the lastName value
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Returns the lastName value
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Sets the password value
     *
     * @param string $password
     * @return void
     */
    public function setPassword(String $password): void
    {
        $this->password = $password;
    }

    /**
     * Returns the password value
     *
     * @return string
     */
    public function getPassword(): String
    {
        return $this->password;
    }

    /**
     * Sets the email value
     *
     * @param string $email
     */
    public function setEmail(String $email): void
    {
        $this->email = $email;
    }

    /**
     * Returns the email value
     *
     * @return string
     */
    public function getEmail(): ?String
    {
        return $this->email;
    }

    /**
     *
     * @return array
     */
    public function getAllSortedUserGroups(): array
    {
        $return = array();
        $duplicateProtection = array();

        /** @var FrontendUserGroup $frontendUserGroup **/
        foreach ($this->getUsergroup() as $frontendUserGroup) {
            $return[$frontendUserGroup->getTitle()] = $frontendUserGroup;
            $duplicateProtection[] = $frontendUserGroup->getUid();
            $return = array_merge($return, $this->getSubUserGroups($frontendUserGroup, $duplicateProtection));
        }
        ksort($return);
        return array_values($return);
    }

    /**
     *
     * @param FrontendUserGroup $userGroup
     * @param array $duplicateProtection
     * @return array
     */
    private function getSubUserGroups(FrontendUserGroup $frontendUserGroup, array &$duplicateProtection): array
    {
        $return = array();
        foreach ($frontendUserGroup->getSubgroup() as $sg) {
            if (! in_array($sg->getUid(), $duplicateProtection)) {
                $duplicateProtection[] = $sg->getUid();
                $return[$sg->getTitle()] = $sg;
                $return = array_merge($return, $this->getSubUserGroups($sg, $duplicateProtection));
            }
        }
        return $return;
    }
}
