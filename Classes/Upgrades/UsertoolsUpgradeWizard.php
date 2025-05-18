<?php

declare(strict_types=1);

namespace Cylancer\Usertools\Upgrades;

use Cylancer\Usertools\Domain\Model\FrontendUser;
use Cylancer\Usertools\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;
use TYPO3\CMS\Core\Resource\Driver\LocalDriver;

/**
 * This file is part of the "user tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C. Gogolin <service@cylancer.net>
 * 
 */

#[UpgradeWizard('usertools_usertoolsUpgradeWizard')]
final class UsertoolsUpgradeWizard implements UpgradeWizardInterface
{

    private FrontendUserRepository $frontendUserRepository;

    private PersistenceManager $persistentManager;

    private ResourceFactory $resourceFactory;

    public function __construct()
    {
        $this->frontendUserRepository = GeneralUtility::makeInstance(FrontendUserRepository::class);
        $settings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $settings->setRespectStoragePage(false);
        $this->frontendUserRepository->setDefaultQuerySettings($settings);
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
    }


    public function getTitle(): string
    {
        return 'Usertools: Portraits identifier';
    }

    public function getDescription(): string
    {
        return "Moves the user portraits from the user images list to the own portrait field.";
    }


    private function isPortraitFile(FileInterface $file, FrontendUser $frontendUser): bool
    {
        $extension = pathinfo($file->getName())['extension']; // It is important that the capitalization is preserved! 
        return GeneralUtility::makeInstance(LocalDriver::class)->sanitizeFileName($frontendUser->getUsername() . '.' . $extension) === $file->getName();
    }



    public function executeUpdate(): bool
    {

        $found = false;
        foreach ($this->frontendUserRepository->findAll() as $frontendUser) {
            if ($frontendUser->getImage()->count() > 0 && $frontendUser->getPortrait() == null) {
                $firstFileReference = $frontendUser->getImage()->getArray()[0];
                $orgFile = $firstFileReference->getOriginalResource()->getOriginalFile();
                if ($this->isPortraitFile($orgFile, $frontendUser)) {

                    // create a new portait file reference...
                    $newObject = [
                        'uid_local' => $orgFile->getUid(),
                        'uid_foreign' => StringUtility::getUniqueId('NEW'),
                        'uid' => StringUtility::getUniqueId('NEW'),
                        'crop' => null,
                    ];

                    $fileReference = $this->resourceFactory->createFileReferenceObject($newObject);
                    $fileReferenceObject = GeneralUtility::makeInstance(FileReference::class);
                    $fileReferenceObject->setOriginalResource($fileReference);

                    $frontendUser->setPortrait($fileReferenceObject);

                    // delete the first image... 
                    $frontendUser->getImage()->detach($firstFileReference);
                    $this->frontendUserRepository->update($frontendUser);
                    $found = true;
                }
            }
        }
        if ($found) {
            GeneralUtility::makeInstance(PersistenceManager::class)->persistAll();
        }

        return true;
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     *
     * @return bool Whether an update is required (TRUE) or not (FALSE)
     */
    public function updateNecessary(): bool
    {
        foreach ($this->frontendUserRepository->findAll() as $frontendUser) {
            if ($frontendUser->getImage()->count() > 0 && $frontendUser->getPortrait() == null) {
                $firstFileReference = $frontendUser->getImage()->getArray()[0];
                if ($this->isPortraitFile($firstFileReference->getOriginalResource()->getOriginalFile(), $frontendUser)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns an array of class names of prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [];
    }
}