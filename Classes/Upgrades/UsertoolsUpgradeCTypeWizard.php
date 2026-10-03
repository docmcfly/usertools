<?php

declare(strict_types=1);

namespace Cylancer\Usertools\Upgrades;


use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * This file is part of the "user tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2026 C. Gogolin <service@cylancer.net>
 *
 */

#[UpgradeWizard('usertools_usertoolsUpgradeCTypeWizard')]
final class UsertoolsUpgradeCTypeWizard implements UpgradeWizardInterface
{


    private const MIGRATIONS = [
        'changeemailform',
        'changepassword',
        'confirmemailchange',
        'confirmnewemail',
        'editprofile',
        'listusers',
    ];

    public function getTitle(): string
    {
        return 'Usertools: Update the content elements';
    }

    public function getDescription(): string
    {
        return 'Moves the content elements from list_type to CType.';
    }

    public function executeUpdate(): bool
    {
        $connection = (new ConnectionPool())
            ->getConnectionForTable('tt_content');

        foreach (self::MIGRATIONS as $contentType) {
            $connection->update(
                'tt_content',
                [
                    'CType' => 'usertools_' . $contentType,
                    'list_type' => '',
                ],
                [
                    'CType' => 'list',
                    'list_type' => 'usertools_' . $contentType,
                ]
            );
        }

        return true;
    }

    public function updateNecessary(): bool
    {
        $logger = GeneralUtility::makeInstance(LogManager::class)
            ->getLogger(__CLASS__);

        foreach (self::MIGRATIONS as $contentType) {

            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('tt_content');

            $queryBuilder->getRestrictions()->removeAll();
            $queryBuilder->getRestrictions()->add(
                new DeletedRestriction()
            );
            $found = $queryBuilder
                ->select('uid')
                ->from('tt_content')
                ->where(
                    $queryBuilder->expr()->eq(
                        'CType',
                        $queryBuilder->createNamedParameter('list')
                    ),
                    $queryBuilder->expr()->eq(
                        'list_type',
                        $queryBuilder->createNamedParameter('usertools_' . $contentType)
                    )
                )
                // ->setMaxResults(1)
                ->executeQuery()
                ->fetchOne() !== false ;
            $logger->info("usertools_$contentType found:" . ($found ? 'true' : 'false'));
            if ($found) {
                return true;
            }
        }

        return false;
    }

    public function getPrerequisites(): array
    {
        return [];
    }
}