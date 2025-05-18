<?php
namespace Cylancer\Usertools\Service;

/**
 *
 * This file is part of the "user tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 C. Gogolin <service@cylancer.net>
 *
 */
use TYPO3\CMS\Core\SingletonInterface;
use Cylancer\Usertools\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Context\Context;
use Cylancer\Usertools\Domain\Model\FrontendUserGroup;
use Cylancer\Usertools\Domain\Model\FrontendUser;

class FrontendUserService implements SingletonInterface
{

    public function __construct(
        private readonly FrontendUserRepository $frontendUserRepository
    ) {
    }

    public function getCurrentUser(): bool|FrontendUser
    {
        if (!$this->isLogged()) {
            return false;
        }
        return $this->frontendUserRepository->findByUid($this->getCurrentUserUid());
    }

    public function getCurrentUserUid(): int
    {
        if (!$this->isLogged()) {
            return false;
        }
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('frontend.user', 'id');
    }

    public function isLogged(): bool
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

    public function contains($userGroup, $feugid, &$loopProtect = array()): bool
    {
        if ($userGroup->getUid() == $feugid) {
            return true;
        } else {
            if (!in_array($userGroup->getUid(), $loopProtect)) {
                $loopProtect[] = $userGroup->getUid();
                foreach ($userGroup->getSubgroup() as $sg) {
                    if ($this->contains($sg, $feugid, $loopProtect)) {
                        return true;
                    }
                }
            }
            return false;
        }
    }

}
