<?php
namespace Cylancer\Usertools\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use \TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 *
 * This file is part of the "User tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C. Gogolin <service@cylancer.net>
 *
 */
class FrontendUser extends AbstractEntity
{

    protected bool $allowDisplayEmail = false;

    protected bool $allowDisplayImageInternal = false;

    protected bool $allowDisplayImagePublic = false;

    protected bool $currentlyOffDuty = false;

    protected ?string $newEmail = '';

    protected ?string $newEmailToken = '';

    protected ?string $confirmedNewEmail = '';

    protected ?string $passwordToken = '';

    protected bool $allowDisplayPhone = false;

    /** @var ObjectStorage<FrontendUserGroup> */
    protected ObjectStorage $usergroup;

    /** @deprecated 
     * @var ObjectStorage<FileReference> */
    protected ObjectStorage $image;

    protected ?FileReference $portrait = null;

    protected ?FileReference $uploadedPortrait = null;

    protected ?string $telephone = '';

    protected ?string $username = '';

    protected ?string $name = '';

    protected ?string $firstName = '';

    protected ?string $lastName = '';

    protected ?string $password = '';

    protected ?string $email = '';

    public function __construct()
    {
        $this->usergroup = new ObjectStorage();
        $this->image = new ObjectStorage();
    }

    public function initializeObject(): void
    {
        $this->usergroup = $this->usergroup ?? new ObjectStorage();
        $this->image = $this->image ?? new ObjectStorage();
    }

    public function getAllowDisplayEmail(): bool
    {
        return $this->allowDisplayEmail;
    }

    public function setAllowDisplayEmail(bool $allowDisplayEmail): void
    {
        $this->allowDisplayEmail = $allowDisplayEmail;
    }

    public function getAllowDisplayImageInternal(): bool
    {
        return $this->allowDisplayImageInternal;
    }

    public function setAllowDisplayImageInternal(bool $allowDisplayImageInternal): void
    {
        $this->allowDisplayImageInternal = $allowDisplayImageInternal;
    }

    public function getAllowDisplayImagePublic(): bool
    {
        return $this->allowDisplayImagePublic;
    }

    public function setAllowDisplayImagePublic(bool $allowDisplayImagePublic): void
    {
        $this->allowDisplayImagePublic = $allowDisplayImagePublic;
    }

    public function getCurrentlyOffDuty(): bool
    {
        return $this->currentlyOffDuty;
    }

    public function setCurrentlyOffDuty(bool $currentlyOffDuty): void
    {
        $this->currentlyOffDuty = $currentlyOffDuty;
    }

    public function getNewEmail(): ?string
    {
        return $this->newEmail;
    }

    public function setNewEmail(?string $newEmail): void
    {
        $this->newEmail = $newEmail;
    }

    public function getNewEmailToken(): ?string
    {
        return $this->newEmailToken;
    }

    public function setNewEmailToken(?string $newEmailToken): void
    {
        $this->newEmailToken = $newEmailToken;
    }

    public function getConfirmedNewEmail(): ?string
    {
        return $this->confirmedNewEmail;
    }

    public function setConfirmedNewEmail(?string $confirmedNewEmail): void
    {
        $this->confirmedNewEmail = $confirmedNewEmail;
    }

    public function getPasswordToken(): ?string
    {
        return $this->passwordToken;
    }

    public function setPasswordToken(?string $passwordToken): void
    {
        $this->passwordToken = $passwordToken;
    }

    public function getAllowDisplayPhone(): bool
    {
        return $this->allowDisplayPhone;
    }

    public function setAllowDisplayPhone(bool $allowDisplayPhone): void
    {
        $this->allowDisplayPhone = $allowDisplayPhone;
    }

    public function setUsergroup(ObjectStorage $usergroup)
    {
        $this->usergroup = $usergroup;
    }

    public function addUsergroup(FrontendUserGroup $usergroup)
    {
        $this->usergroup->attach($usergroup);
    }

    public function removeUsergroup(FrontendUserGroup $usergroup)
    {
        $this->usergroup->detach($usergroup);
    }

    public function getUsergroup():ObjectStorage
    {
        return $this->usergroup;
    }

    public function setImage(ObjectStorage $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ObjectStorage
    {
        return $this->image;
    }

    public function getUploadedPortrait(): ?FileReference
    {
        return $this->uploadedPortrait;
    }

    public function setUploadedPortrait(?FileReference $uploadedPortrait): void
    {
        $this->uploadedPortrait = $uploadedPortrait;
    }

    public function getPortrait(): ?FileReference
    {
        return $this->portrait;
    }

    public function setPortrait(?FileReference $portrait): void
    {
        $this->portrait = $portrait;
    }

    public function setTelephone($telephone):void
    {
        $this->telephone = $telephone;
    }

    public function getTelephone():?string
    {
        return $this->telephone;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setFirstName($firstName):void
    {
        $this->firstName = $firstName;
    }

    public function getFirstName():?string 
    {
        return $this->firstName;
    }

    public function setLastName($lastName):void
    {
        $this->lastName = $lastName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getAllSortedUserGroups(): array
    {
        $return = [];
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

    private function getSubUserGroups(FrontendUserGroup $frontendUserGroup, array &$duplicateProtection): array
    {
        $return = [];
        foreach ($frontendUserGroup->getSubgroup() as $sg) {
            if (!in_array($sg->getUid(), $duplicateProtection)) {
                $duplicateProtection[] = $sg->getUid();
                $return[$sg->getTitle()] = $sg;
                $return = array_merge($return, $this->getSubUserGroups($sg, $duplicateProtection));
            }
        }
        return $return;
    }
}
