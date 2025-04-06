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
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\Security\FileNameValidator;
use TYPO3\CMS\Core\Utility\PathUtility;
use Cylancer\Usertools\Domain\Model\ValidationResults;
use Cylancer\Usertools\Domain\Model\Password;
use Cylancer\Usertools\Domain\Model\Email;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Cylancer\Usertools\Service\FrontendUserService;

/**
 *
 * @package Cylancer\Usertools\Controller;
 */
class ProfileController extends ActionController
{

    const TOKEN_KEY = 'token';

    const EMAIL_KEY = 'email';

    const CURRENT_USER = 'currentUser';

    const VALIDATIOPN_RESULTS = 'validationResults';

    const PASSWORD_KEY = 'password';

    const TARGET_PAGE = 'targetPage';

    const VISIBLE_FE_GROUPS = 'visibleFeGroups';

    const PASSWORD_RULES = '^(?=(?:[^A-Z]*[A-Z]){1})(?=(?:[^0-9]*[0-9]){1})(?=(?:[^!#+-_"\/§$%&\()\[\]\{\}]*[!#+-_"\/§$%&\()\[\]\{\}]){1}).{8,}$';

    /** @var FrontendUserRepository   */
    private $frontendUserRepository = null;

    /** @var PersistenceManager **/
    private $persistenceManager;

    /** @var ResourceFactory  **/
    private $resourceFactory;

    /** @var FrontendUserService **/
    private $frontendUserService;

    private $_validationResults = null;

    private function getValidationResults()
    {
        if ($this->_validationResults == null) {
            $this->_validationResults = ($this->request->hasArgument(ProfileController::VALIDATIOPN_RESULTS)) ? //
                $this->request->getArgument(ProfileController::VALIDATIOPN_RESULTS) : //
                new ValidationResults();
        }
        return $this->_validationResults;
    }

    public function __construct(FrontendUserRepository $frontendUserRepository, PersistenceManager $persistenceManager, ResourceFactory $resourceFactory, FrontendUserService $frontendUserService)
    {
        $this->frontendUserRepository = $frontendUserRepository;
        $this->persistenceManager = $persistenceManager;
        $this->responseFactory = $resourceFactory;
        $this->frontendUserService = $frontendUserService;
    }

    /**
     *
     * @param FrontendUser $user
     * @return ResponseInterface
     *
     */
    public function doEditProfileAction(FrontendUser $currentUser = null): ResponseInterface
    {
        if ($this->request->hasArgument('uploadedImage')) {
            $currentUser->setUploadedImage($this->request->getArgument('uploadedImage'));
        }

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
        if ($currentUser->getUploadedImage() != null) {
            if (!GeneralUtility::makeInstance(FileNameValidator::class)->isValid($currentUser->getUploadedImage()['name'])) {
                $validationResults->addError('uploadedImage.scriptsNotAllowed');
            } else {

                $this->updateImage($currentUser);
            }
        }
        if (!$validationResults->hasErrors()) {
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
    private function updateImage(FrontendUser $frontendUser): bool
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        $uploadFileData = $frontendUser->getUploadedImage();
        $error = $uploadFileData['error'];
        if ($error !== UPLOAD_ERR_NO_FILE) {
            $fileName = $uploadFileData['name'];
            $fileExtension = PathUtility::pathinfo($fileName, PATHINFO_EXTENSION);
            if (!GeneralUtility::inList($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'], strtolower($fileExtension))) {
                $validationResults->addError('uploadedImage.imageFormatNotSupported', [
                    $fileExtension
                ]);
            } else if (!$validationResults->hasErrors() && $error === UPLOAD_ERR_OK) {
                if ($frontendUser->getImage()->count() === 0) {
                    // the first image... :)
                    $this->addFile($uploadFileData, $frontendUser);
                } else {
                    // one image exists:

                    /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileReference **/
                    $fileReference = $frontendUser->getImage()->getArray()[0];
                    $fileName = $fileReference->getOriginalResource()->getName();

                    if (($fileName !== $frontendUser->getUsername() . '.' . $fileExtension)) {
                        $frontendUser->getImage()->removeAll($frontendUser->getImage());
                        $fileResource = GeneralUtility::makeInstance(ResourceFactory::class)->getFileReferenceObject($fileReference->getUid());
                        $fileResource->getOriginalFile()->delete();
                    }

                    $this->addFile($uploadFileData, $frontendUser);
                }
                $this->frontendUserRepository->update($frontendUser);
                $this->persistenceManager->persistAll();

                return true;
            }
        }
        return $error === UPLOAD_ERR_NO_FILE;
    }

    /**
     *
     * @param array $uploadFileData
     * @param FrontendUser $frontendUser
     * @return void
     */
    private function addFile(array $uploadFileData, FrontendUser $frontendUser): void
    {
        $tmpFile = $uploadFileData['tmp_name'];
        $fileExtension = end(explode('.', $uploadFileData['name']));

        // Save the imgae in the storage...
        /** @var StorageRepository $storageRepository **/
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        $storage = $storageRepository->getDefaultStorage();

        /** @var Folder $folder **/
        $folder = $storage->getFolder($this->settings['userImageFolder']);

        /** @var FileInterface $imageFile **/
        $imageFile = $folder->addFile($tmpFile, $frontendUser->getUsername() . '.' . $fileExtension, DuplicationBehavior::REPLACE);

        $storageRepository->flush();

        /** @var File $file **/
        $coreFile = $storage->getFileByIdentifier($imageFile->getIdentifier());

        /** @var \\TYPO3\CMS\Extbase\Domain\Model\File $file **/
        $file = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Domain\Model\File::class);
        $file->setOriginalResource($coreFile);

        /** @var FileReference $coreFileReference **/
        $coreFileReference = GeneralUtility::makeInstance(FileReference::class, [
            'uid_local' => $coreFile->getUid()
        ]);

        /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileReference **/
        $fileReference = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Domain\Model\FileReference::class);
        $fileReference->setOriginalResource($coreFileReference);

        /** @var ObjectStorage $objectstorage */
        $frontendUser->getImage()->attach($fileReference);
    }

    /**
     *
     * @param FrontendUser $frontendUser
     * @return ResponseInterface
     */
    public function deleteCurrentUserImagesAction(): ?object
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if (!$this->frontendUserService->isLogged()) {
            $validationResults->addError('notLogged');
        }
        if (!$validationResults->hasErrors()) {

            /** @var ResourceFactory $resourceFactory **/
            $resourcefactory = GeneralUtility::makeInstance(ResourceFactory::class);

            $frontendUser = $this->frontendUserService->getCurrentUser();
            $fileReferences = $frontendUser->getImage()->getArray();

            /** @var \TYPO3\CMS\Extbase\Domain\Model\FileReference $fileReference **/
            foreach ($fileReferences as $fileReference) {
                $frontendUser->getImage()->detach($fileReference);

                $fileResource = $resourcefactory->getFileReferenceObject($fileReference->getUid());
                $fileResource->getOriginalFile()->delete();
            }

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
            $this->view->assign(ProfileController::CURRENT_USER, $this->frontendUserService->getCurrentUser());
            $this->view->assign(ProfileController::VISIBLE_FE_GROUPS, GeneralUtility::intExplode(',', $this->settings['visibleFeGroups']));
        } else {
            $validationResults->addError('notLogged');
        }
        $this->view->assign(ProfileController::VALIDATIOPN_RESULTS, $validationResults);
        return $this->htmlResponse();
    }

    /**
     *
     * @param \Cylancer\Usertools\Domain\Model\Password $password
     * @return ResponseInterface
     */
    public function changePasswordFormAction(Password $password = null): ResponseInterface
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();
        if (!$this->frontendUserService->isLogged()) {
            $validationResults->addError('notLogged');
        }

        if ($password == null) {
            $password = GeneralUtility::makeInstance(Password::class);
            $this->view->assign(ProfileController::PASSWORD_KEY, $password);
        }

        $this->view->assign(ProfileController::VALIDATIOPN_RESULTS, $validationResults);
        return $this->htmlResponse();
    }

    /**
     *
     * @param Password $password
     * @return ResponseInterface
     */
    public function changePasswordAction(Password $password): ResponseInterface
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if ($this->frontendUserService->isLogged()) {
            $validationResults->addErrors($this->validatePassword($password));
        } else {
            $validationResults->addError('notLogged');
        }

        if (!$validationResults->hasErrors()) {
            $user = $this->frontendUserRepository->findByUid($this->frontendUserService->getCurrentUserUid());
            $user->setPassword($this->getHashedPassword($password->getPassword()));
            $this->frontendUserRepository->update($user);
            $validationResults->addInfo('passwordChangeSuccessful');
            $password = null;
        } else {
            $password->setConfirmPassword("");
        }

        return GeneralUtility::makeInstance(ForwardResponse::class, 'changePasswordForm')->withArguments([
            ProfileController::PASSWORD_KEY => $password,
            ProfileController::VALIDATIOPN_RESULTS => $validationResults
        ]);
    }

    /**
     * Checks the password of the current front end user
     *
     * @param Password $password
     * @return array
     */
    private function validatePassword(Password $password): array
    {
        $errors = array();
        $user = $this->frontendUserService->getCurrentUser();
        if ($this->checkPassword($password->getPassword(), $user->getPassword())) {
            $errors[] = 'newPasswordIsOldPassword';
        }

        if ($password->getPassword() !== $password->getConfirmPassword()) {
            $errors[] = 'confirmPasswordIsNotEquals';
        }

        if (!preg_match('/' . ProfileController::PASSWORD_RULES . '/', $password->getPassword())) {
            $errors[] = 'passwordInvalid';
        }
        return $errors;
    }

    /**
     * Displays the change email form
     * @return ResponseInterface
     */
    public function changeEmailFormAction()
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        $email = ($this->request->hasArgument(ProfileController::EMAIL_KEY)) ? $this->request->getArgument(ProfileController::EMAIL_KEY) : GeneralUtility::makeInstance(Email::class);

        if ($this->frontendUserService->isLogged()) {
            $user = $this->frontendUserService->getCurrentUser();
        } else {
            $validationResults->addError('notLogged');
        }
        $this->view->assign(ProfileController::EMAIL_KEY, $email);
        $this->view->assign(ProfileController::CURRENT_USER, $user);
        $this->view->assign(ProfileController::VALIDATIOPN_RESULTS, $validationResults);
        return $this->htmlResponse();
    }

    /**
     *
     * @param Email $email
     *  @return ResponseInterface
     */
    public function prepareEmailChangeAction(Email $email): ?object
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        $errors = array();
        if ($this->frontendUserService->isLogged()) {
            $user = $this->frontendUserService->getCurrentUser();
            // No email
            if (empty($email->getEmail())) {
                $errors[] = 'newEmailEmpty';
            } // E-Mail Format
            elseif (!filter_var($email->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'newEmailInvalid';
            } elseif ($email->getEmail() === $user->getEmail()) {
                $errors[] = 'newEmailIsOldEmail';
            } elseif (count($this->frontendUserRepository->findByEmail($email->getEmail())) > 0) {
                $errors[] = 'newEmailExists';
            }
        } else {
            $errors[] = 'notLogged';
        }
        $validationResults->addErrors($errors);

        if (!$validationResults->hasErrors()) {
            $user->setNewEmail($email->getEmail());
            $emailHash = sha1(uniqid($email->getEmail()));
            $user->setNewEmailToken($emailHash);
            $this->frontendUserRepository->update($user);

            $fluidEmail = GeneralUtility::makeInstance(FluidEmail::class);
            $fluidEmail
                ->setRequest($this->request)
                ->to(new Address($user->getEmail(), $user->getName()))
                ->from(new Address($this->settings['sender'], $this->settings['senderName']))
                ->subject(LocalizationUtility::translate('changeEmail.mail.confirm.subject', 'Usertools'))
                ->format(FluidEmail::FORMAT_BOTH) // send HTML and plaintext mail
                ->setTemplate('ConfirmEmail')
                ->assign(ProfileController::TOKEN_KEY, $emailHash)
                ->assign(ProfileController::CURRENT_USER, $user)
                ->assign(ProfileController::TARGET_PAGE, $this->settings['redirectSuccess']);
            try {
                GeneralUtility::makeInstance(MailerInterface::class)->send($fluidEmail);
                $validationResults->addInfo('confirmEMailMailSuccessful');
            } catch (\Exception $e) {
                $validationResults->addError('confirmEMailMailFailed');
            }

            $email = GeneralUtility::makeInstance(Email::class);
        }

        return GeneralUtility::makeInstance(ForwardResponse::class, 'changeEmailForm')->withArguments([
            ProfileController::EMAIL_KEY => $email,
            ProfileController::VALIDATIOPN_RESULTS => $validationResults
        ]);
    }

    /**
     * Confirms the new email address
     *
     * @return ResponseInterface
     */
    public function confirmNewEmailAction(): ?object
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if ($this->request->hasArgument(ProfileController::TOKEN_KEY) && $this->request->hasArgument(ProfileController::EMAIL_KEY)) {

            $token = $this->request->getArgument(ProfileController::TOKEN_KEY);
            $email = $this->request->getArgument(ProfileController::EMAIL_KEY);

            /** @var FrontendUser $user **/
            $user = null;
            $users = $this->frontendUserRepository->findByNewEmailToken($token);
            if (count($users) > 0) {
                foreach ($users as $u) {
                    if ($u->getNewEmail() === $email) {
                        $user = $u;
                        break;
                    }
                }
            }
            if ($user == null) {
                $validationResults->addError('tokenEmailCombinationNotFound');
            }
            if (!$validationResults->hasErrors()) {
                $user->setNewEmail('');
                $user->setNewEmailToken('');
                $user->setEmail($email);
                $this->frontendUserRepository->update($user);
                $this->view->assign(ProfileController::CURRENT_USER, $user);
                $validationResults->addInfo('emailChangingSuccessful');
            }
        } else {
            $validationResults->addError('invalidLink');
        }

        $this->view->assign(ProfileController::VALIDATIOPN_RESULTS, $validationResults);
        return $this->htmlResponse();
    }

    /**
     * Get the hashed password
     *
     * @param string $password
     * @return string|NULL
     */
    private function getHashedPassword(string $password): ?string
    {
        return GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance('FE')->getHashedPassword($password);
    }

    /**
     * Check password
     *
     * @param string $password
     * @param string $passwordHash
     * @return string|NULL
     */
    public function checkPassword(string $password, string $passwordHash): ?string
    {
        return GeneralUtility::makeInstance(PasswordHashFactory::class)->get($passwordHash, 'FE')->checkPassword($password, $passwordHash);
    }
}
