<?php
namespace Cylancer\Usertools\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Cylancer\Usertools\Domain\Repository\FrontendUserRepository;
use Cylancer\Usertools\Service\FrontendUserService;

/**
 * This file is part of the "user tools" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2025 C. Gogolin <service@cylancer.net>
 */
class ListController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    private const USERS = 'users';

    private const CAN_VIEW_PHONE_NUMBER = 'canViewPhoneNumbers';

    private const CAN_VIEW_CURRENTLY_OFF_DUTY = 'canViewCurrentlyOffDuty';

    private const VISIBLE_FE_GROUPS = 'visibleFeGroups';

    public function __construct(
        private readonly FrontendUserRepository $frontendUserRepository,
        private readonly FrontendUserService $frontendUserService
    ) {
    }

    /** 
     *
     * @return ResponseInterface
     */
    public function listUsersAction(): ResponseInterface
    {
        if ($this->frontendUserService->isLogged()) {

            $this->frontendUserRepository->setDefaultOrderings([
                'last_name' => QueryInterface::ORDER_ASCENDING,
                'first_name' => QueryInterface::ORDER_ASCENDING
            ]);

            $this->view->assign(ListController::USERS, $this->frontendUserRepository->findAll());

            $currentUser = $this->frontendUserService->getCurrentUser();

            $allowGroupFound = false;
            $allowGroup = $this->settings[ListController::CAN_VIEW_PHONE_NUMBER];
            foreach ($currentUser->getUserGroup() as $ug) {
                if ($this->frontendUserService->contains($ug, $allowGroup)) {
                    $allowGroupFound = true;
                    break;
                }
            }
            $this->view->assign(ListController::CAN_VIEW_PHONE_NUMBER, $allowGroupFound);

            $allowGroupFound = false;
            $allowGroup = $this->settings[ListController::CAN_VIEW_CURRENTLY_OFF_DUTY];
            foreach ($currentUser->getUserGroup() as $ug) {
                if ($this->frontendUserService->contains($ug, $allowGroup)) {
                    $allowGroupFound = true;
                    break;
                }
            }
            $this->view->assign(ListController::CAN_VIEW_CURRENTLY_OFF_DUTY, $allowGroupFound);
            $this->view->assign(ListController::VISIBLE_FE_GROUPS, GeneralUtility::intExplode(',', $this->settings[ListController::VISIBLE_FE_GROUPS]));
            $this->view->assign('alternativEmailLink', $this->settings['alternativEmailLink']);


        }
        return $this->htmlResponse();
    }

}
