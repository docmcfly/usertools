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
 * (c) 2022 Clemens Gogolin <service@cylancer.net>
 */
class ListController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    const USERS = 'users';

    const CAN_VIEW_PHONE_NUMBER = 'canViewPhoneNumbers';

    const CAN_VIEW_CURRENTLY_OFF_DUTY = 'canViewCurrentlyOffDuty';

    const VISIBLE_FE_GROUPS = 'visibleFeGroups';

    /** @var FrontendUserRepository     */
    public $frontendUserRepository = null;

    /** @var FrontendUserService     */
    public $frontendUserService = null;

    public function __construct(FrontendUserRepository $frontendUserRepository, FrontendUserService $frontendUserService)
    {
        $this->frontendUserRepository = $frontendUserRepository;
        $this->frontendUserService = $frontendUserService;
    }

    /**
     *
     * @return ResponseInterface
     */
    public function listAllAction(): ResponseInterface
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
