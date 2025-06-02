<?php
namespace Cylancer\Usertools\Controller;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use Cylancer\Usertools\Domain\Repository\FrontendUserRepository;
use Cylancer\Usertools\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use Cylancer\Usertools\Domain\Model\ValidationResults;
use Cylancer\Usertools\Domain\Model\Password;
use Cylancer\Usertools\Domain\Model\Email;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Cylancer\Usertools\Service\FrontendUserService;

use TYPO3\CMS\Core\Resource\Enum\DuplicationBehavior;
use TYPO3\CMS\Extbase\Validation\Validator\MimeTypeValidator;
use TYPO3\CMS\Extbase\Mvc\Controller\FileUploadConfiguration;
use TYPO3\CMS\Extbase\Mvc\Controller\Argument;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 *
 */
class ProfileController extends ActionController
{


    private const CURRENT_USER = 'currentUser';

    private const EMAIL = 'email';

    private const VALIDATIOPN_RESULTS = 'validationResults';

    private const VISIBLE_FE_GROUPS = 'visibleFeGroups';


    private $_validationResults = null;

    public function __construct(
        private readonly FrontendUserRepository $frontendUserRepository,
        private readonly FrontendUserService $frontendUserService,
        private readonly PersistenceManager $persistenceManager,
        private readonly ResourceFactory $resourceFactory
    ) {
    }


    private function getValidationResults()
    {
        if ($this->_validationResults == null) {
            $this->_validationResults = ($this->request->hasArgument(ProfileController::VALIDATIOPN_RESULTS)) ? //
                $this->request->getArgument(ProfileController::VALIDATIOPN_RESULTS) : //
                new ValidationResults();
        }
        return $this->_validationResults;
    }

    public function initializeDoEditProfileAction(): void
    {
        $mimeTypeValidator = GeneralUtility::makeInstance(MimeTypeValidator::class);
        $mimeTypeValidator->setOptions(['allowedMimeTypes' => ['image/jpeg', 'image/png']]);

        /** @var Argument */
        $currentUser = $this->arguments->getArgument('currentUser');
        $fileHandlingServiceConfiguration = $currentUser->getFileHandlingServiceConfiguration();
        $uploadConfiguration = (new FileUploadConfiguration('uploadedPortrait'))
            ->addValidator($mimeTypeValidator)
            ->setMaxFiles(1)
            ->setUploadFolder($this->settings['userImageFolder'] . '/');

        $uploadConfiguration->setDuplicationBehavior(DuplicationBehavior::REPLACE);
        $fileHandlingServiceConfiguration->addFileUploadConfiguration($uploadConfiguration);
        $currentUser->getPropertyMappingConfiguration()->skipProperties('uploadedPortrait');

    }


    /**
     *
     * @param FrontendUser $user
     * @return ResponseInterface
     *
     */
    public function doEditProfileAction(FrontendUser $currentUser): ResponseInterface
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if ($currentUser == null) {
            return GeneralUtility::makeInstance(ForwardResponse::class, 'editProfile');
        } else if ($this->frontendUserService->isLogged()) {
            if ($this->frontendUserService->getCurrentUserUid() != $currentUser->getUid()) {
                $validationResults->addError('onlyCurrentUserIsEditable');
            }
        } else {
            $validationResults->addError('notLogged');
        }

        if (!$validationResults->hasErrors()) {

            $this->updatePortrait($currentUser);

            $this->frontendUserRepository->update($currentUser);
            $validationResults->addInfo('savingSuccessful');
        }
        return GeneralUtility::makeInstance(ForwardResponse::class, 'editProfile')->withArguments([
            ProfileController::VALIDATIOPN_RESULTS => $validationResults
        ]);
    }

    /**
     *
     * @param FrontendUser $frontendUser
     * @return bool
     */
    private function updatePortrait(FrontendUser $frontendUser): void
    {
        if ($frontendUser->getUploadedPortrait() != null) {
            if ($frontendUser->getPortrait() != null) {
                $frontendUser->getPortrait()->getOriginalResource()->delete();

            }
            $uploadedPortrait = $frontendUser->getUploadedPortrait();

            $extension = $uploadedPortrait->getOriginalResource()->getOriginalFile()->getExtension();
            $uploadedPortrait->getOriginalResource()->getOriginalFile()->rename($frontendUser->getUsername() . '.' . $extension);
            $frontendUser->setPortrait($uploadedPortrait);
            $frontendUser->setUploadedPortrait(null);
        }
    }


    /**
     *
     * @param FrontendUser $frontendUser
     * @return ResponseInterface
     */
    public function deletePortraitAction(): ?object
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if (!$this->frontendUserService->isLogged()) {
            $validationResults->addError('notLogged');
        }
        $frontendUser = $this->frontendUserService->getCurrentUser();
        $portrait = $frontendUser->getPortrait();
        if (!$validationResults->hasErrors() && $portrait != null) {

            $frontendUser->setPortrait(null);
            $fileResource = $this->resourceFactory->getFileReferenceObject($portrait->getUid());
            $fileResource->getOriginalFile()->delete();

            $this->frontendUserRepository->update($frontendUser);
        }
        return GeneralUtility::makeInstance(ForwardResponse::class, 'editProfile')->withArguments([
            ProfileController::VALIDATIOPN_RESULTS => $validationResults
        ]);
    }

    /**
     * Show the user settings
     *
     * @return ResponseInterface
     */
    public function editProfileAction(): ResponseInterface
    {

        $validationResults = $this->getValidationResults();
        if ($this->frontendUserService->isLogged()) {
            $frontendUser = $this->frontendUserService->getCurrentUser();
            $this->view->assign(ProfileController::CURRENT_USER, $frontendUser);
            $this->view->assign(ProfileController::VISIBLE_FE_GROUPS, GeneralUtility::intExplode(',', $this->settings['visibleFeGroups']));


            // migration from usertools 3.x (for TYPO3 12) to 4.x (for TYPO3 13)
            // Moves the first image to the portait, if the filename is equals with the username. 
            if ($frontendUser->getImage()->count() > 0 && $frontendUser->getPortrait() == null) {
                $firstFileReference = $frontendUser->getImage()->getArray()[0];
                $orgFile = $firstFileReference->getOriginalResource()->getOriginalFile();
                $extension = $orgFile->getExtension();
                if (($frontendUser->getUsername() . '.' . $extension) == $orgFile->getName()) {

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
                }

            }


        } else {
            $validationResults->addError('notLogged');
        }
        $this->view->assign(ProfileController::VALIDATIOPN_RESULTS, $validationResults);
        return $this->htmlResponse();
    }


}
