<?php
namespace Cylancer\Usertools\Controller;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use Cylancer\Usertools\Domain\Repository\FrontendUserRepository;
use Cylancer\Usertools\Domain\Model\FrontendUser;
use Cylancer\Usertools\Domain\Model\ValidationResults;
use Cylancer\Usertools\Domain\Model\Email;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Cylancer\Usertools\Service\FrontendUserService;

/**
 *
 */
class ChangeEmailController extends ActionController
{

    private const SEND_CONFIRM_LINK_KEY = 'sendConfirmLink';
    private const TOKEN_KEY = 'token';

    private const EMAIL_KEY = 'email';

    private const CURRENT_USER = 'currentUser';
    private const CONFIRM_EMAIL_CHANGE_PAGE = 'confirmEmailChangePage';

    private const VALIDATIOPN_RESULTS = 'validationResults';
    private ?ValidationResults $_validationResults = null;
    public function __construct(
        private readonly FrontendUserRepository $frontendUserRepository,
        private readonly FrontendUserService $frontendUserService
    ) {
    }

    private function getValidationResults(): ValidationResults
    {
        if ($this->_validationResults == null) {
            $this->_validationResults = ($this->request->hasArgument(ChangeEmailController::VALIDATIOPN_RESULTS)) ? //
                $this->request->getArgument(ChangeEmailController::VALIDATIOPN_RESULTS) : //
                new ValidationResults();
        }
        return $this->_validationResults;
    }


    public function changeEmailAction(): ResponseInterface
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        $email = ($this->request->hasArgument(ChangeEmailController::EMAIL_KEY)) ? $this->request->getArgument(ChangeEmailController::EMAIL_KEY) : GeneralUtility::makeInstance(Email::class);

        if ($this->frontendUserService->isLogged()) {
            $user = $this->frontendUserService->getCurrentUser();
        } else {
            $validationResults->addError('notLogged');
        }
        $this->view->assign(ChangeEmailController::EMAIL_KEY, $email);
        $this->view->assign(ChangeEmailController::CURRENT_USER, $user);
        $this->view->assign(ChangeEmailController::VALIDATIOPN_RESULTS, $validationResults);
        return $this->htmlResponse();
    }

    public function prepareEmailChangeAction(Email $email): ForwardResponse
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();
        $errors = [];
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

            // user has none email
            if ($user->getEmail() == null || empty($user->getEmail())) {

                $user->setNewEmail('');
                $token = sha1(uniqid($email->getEmail()));
                $user->setNewEmailToken($token);
                $user->setConfirmedNewEmail($email->getEmail());
                $this->frontendUserRepository->update($user);

                $this->sendConfirmMail($user, $token, $validationResults);
                $email = GeneralUtility::makeInstance(Email::class);

                // user want to change the email
            } else {
                $sendChangeConfirmationLink2currentMail = $this->settings['sendChangeConfirmationLink2currentMail'] == 1;
                $token = sha1(uniqid($email->getEmail()));

                if ($sendChangeConfirmationLink2currentMail) {

                    $user->setNewEmail($email->getEmail());
                    $user->setNewEmailToken($token);
                    $user->setConfirmedNewEmail('');
                    $this->frontendUserRepository->update($user);
                    
                    $this->sendConfirmEmailChange($user, $token, $validationResults);
                    if (!$validationResults->hasErrors()) {
                        $email = GeneralUtility::makeInstance(Email::class);
                     }
                 } else {

                    $user->setNewEmail('');
                    $token = sha1(uniqid($email->getEmail()));
                    $user->setNewEmailToken($token);
                    $user->setConfirmedNewEmail($email->getEmail());
                    $this->frontendUserRepository->update($user);

                    $this->sendConfirmEmailChange($user, null, $validationResults);
                    $this->sendConfirmMail($user, $token, $validationResults);

                    if (!$validationResults->hasErrors()) {
                        $email = GeneralUtility::makeInstance(Email::class);
                    }
                }

     
               

            }
        }

        return GeneralUtility::makeInstance(ForwardResponse::class, 'changeEmail')->withArguments([
            ChangeEmailController::EMAIL_KEY => $email,
            ChangeEmailController::VALIDATIOPN_RESULTS => $validationResults
        ]);
    }

    private function sendConfirmEmailChange(FrontendUser $user, ?string $token, ValidationResults $validationResults): void
    {
        $fluidEmail = GeneralUtility::makeInstance(FluidEmail::class);
        $fluidEmail
            ->setTemplate('ConfirmEmailChange')
            ->setRequest($this->request)
            ->to(new Address($user->getEmail(), $user->getName()))
            ->from(new Address($this->settings['sender'], $this->settings['senderName']))
            ->subject(LocalizationUtility::translate('changeEmail.mail.confirmEmailChange.subject', 'Usertools'))
            ->format(FluidEmail::FORMAT_BOTH) // send HTML and plaintext mail
            ->assign(ChangeEmailController::SEND_CONFIRM_LINK_KEY, $token !== null)
            ->assign(ChangeEmailController::TOKEN_KEY, $token)
            ->assign(ChangeEmailController::CURRENT_USER, $user)
            ->assign(ChangeEmailController::CONFIRM_EMAIL_CHANGE_PAGE, $this->settings['confirmEmailChangePage']);
        try {
            GeneralUtility::makeInstance(MailerInterface::class)->send($fluidEmail);
            $validationResults->addInfo('confirmEMailMailSuccessful');
        } catch (\Exception $e) {
            debug($e);
            $validationResults->addError('confirmEMailMailFailed');
        }

    }

    private function sendConfirmMail(FrontendUser $user, string $token, ValidationResults $validationResults): void
    {
        $fluidEmail = GeneralUtility::makeInstance(FluidEmail::class);
        $fluidEmail
            ->setTemplate('ConfirmEmail')
            ->setRequest($this->request)
            ->to(new Address($user->getConfirmedNewEmail(), $user->getName()))
            ->from(new Address($this->settings['sender'], $this->settings['senderName']))
            ->subject(LocalizationUtility::translate('changeEmail.mail.confirm.subject', 'Usertools'))
            ->format(FluidEmail::FORMAT_BOTH) // send HTML and plaintext mail
            ->assign(ChangeEmailController::TOKEN_KEY, $token)
            ->assign(ChangeEmailController::CURRENT_USER, $user)
            ->assign(ChangeEmailController::CONFIRM_EMAIL_CHANGE_PAGE, $this->settings['confirmNewEmailPage']);
        try {
            GeneralUtility::makeInstance(MailerInterface::class)->send($fluidEmail);
            $validationResults->addInfo('confirmEMailMailSuccessful');
        } catch (\Exception $e) {
            $validationResults->addError('confirmEMailMailFailed');
        }

    }


    public function confirmEmailChangeAction(): ResponseInterface
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();

        if (
            $this->request->hasArgument(ChangeEmailController::TOKEN_KEY)
            && $this->request->hasArgument(ChangeEmailController::EMAIL_KEY)
        ) {

            $token = $this->request->getArgument(ChangeEmailController::TOKEN_KEY);
            $email = $this->request->getArgument(ChangeEmailController::EMAIL_KEY);

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
                $user->setConfirmedNewEmail($email);
                $token = sha1(uniqid($email));
                $user->setNewEmailToken($token);
                $user->setNewEmail('');
                $this->frontendUserRepository->update($user);

                $this->sendConfirmMail($user, $token, $validationResults);
            }
        } else {
            $validationResults->addError('invalidLink');
        }
        $this->view->assign(ChangeEmailController::VALIDATIOPN_RESULTS, $validationResults);
        return $this->htmlResponse();
    }

    public function confirmNewEmailAction(): ResponseInterface
    {
        /** @var ValidationResults $validationResults **/
        $validationResults = $this->getValidationResults();
        if (
            $this->request->hasArgument(ChangeEmailController::TOKEN_KEY)
            && $this->request->hasArgument(ChangeEmailController::EMAIL_KEY)
        ) {

            $token = $this->request->getArgument(ChangeEmailController::TOKEN_KEY);
            $email = $this->request->getArgument(ChangeEmailController::EMAIL_KEY);

            /** @var FrontendUser $user **/
            $user = null;
            $users = $this->frontendUserRepository->findByNewEmailToken($token);
            if (count($users) > 0) {
                foreach ($users as $u) {
                    if ($u->getConfirmedNewEmail() === $email) {
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
                $user->setConfirmedNewEmail('');
                $user->setEmail($email);
                $this->frontendUserRepository->update($user);
                $this->view->assign(ChangeEmailController::CURRENT_USER, $user);
                $validationResults->addInfo('emailChangingSuccessful');
            }
        } else {
            $validationResults->addError('invalidLink');
        }

        $this->view->assign(ChangeEmailController::VALIDATIOPN_RESULTS, $validationResults);
        return $this->htmlResponse();
    }

}
