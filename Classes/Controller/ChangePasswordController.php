<?php
namespace Cylancer\Usertools\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use Cylancer\Usertools\Domain\Repository\FrontendUserRepository;
use Cylancer\Usertools\Domain\Model\ValidationResults;
use Cylancer\Usertools\Domain\Model\Password;
use Cylancer\Usertools\Service\FrontendUserService;


/**
 *
 */
class ChangePasswordController extends ActionController
{

    private const VALIDATIOPN_RESULTS = 'validationResults';

    private const PASSWORD_KEY = 'password';

    private const PASSWORD_RULES = '^(?=(?:[^A-Z]*[A-Z]){1})(?=(?:[^0-9]*[0-9]){1})(?=(?:[^!#+-_"\/§$%&\()\[\]\{\}]*[!#+-_"\/§$%&\()\[\]\{\}]){1}).{8,}$';


    private $_validationResults = null;

    public function __construct(
        private readonly FrontendUserRepository $frontendUserRepository,
        private readonly FrontendUserService $frontendUserService
    ) {
    }

    private function getValidationResults()
    {
        if ($this->_validationResults == null) {
            $this->_validationResults = ($this->request->hasArgument(ChangePasswordController::VALIDATIOPN_RESULTS)) ? //
                $this->request->getArgument(ChangePasswordController::VALIDATIOPN_RESULTS) : //
                new ValidationResults();
        }
        return $this->_validationResults;
    }



    /**
     *
     * @param \Cylancer\Usertools\Domain\Model\Password $password
     * @return ResponseInterface
     */
    public function changePasswordAction(Password $password = null): ResponseInterface
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();
        if (!$this->frontendUserService->isLogged()) {
            $validationResults->addError('notLogged');
        }

        if ($password == null) {
            $password = GeneralUtility::makeInstance(Password::class);
            $this->view->assign(ChangePasswordController::PASSWORD_KEY, $password);
        }

        $this->view->assign(ChangePasswordController::VALIDATIOPN_RESULTS, $validationResults);
        return $this->htmlResponse();
    }

    /**
     *
     * @param Password $password
     * @return ResponseInterface
     */
    public function doChangePasswordAction(Password $password): ResponseInterface
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

        return GeneralUtility::makeInstance(ForwardResponse::class, 'changePassword')->withArguments([
            ChangePasswordController::PASSWORD_KEY => $password,
            ChangePasswordController::VALIDATIOPN_RESULTS => $validationResults
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

        if (!preg_match('/' . ChangePasswordController::PASSWORD_RULES . '/', $password->getPassword())) {
            $errors[] = 'passwordInvalid';
        }
        return $errors;
    }

    /**
     * Get the hashed password
     *
     * @param string $password
     * @return string|null
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
     * @return string|null
     */
    public function checkPassword(string $password, string $passwordHash): ?string
    {
        return GeneralUtility::makeInstance(PasswordHashFactory::class)->get($passwordHash, 'FE')->checkPassword($password, $passwordHash);
    }
}
