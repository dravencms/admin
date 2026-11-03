<?php declare(strict_types = 1);

namespace Dravencms\AdminModule\UserModule;

use Dravencms\Admin\AdminAuthenticator;
use Dravencms\AdminModule\BasePresenter;
use Dravencms\AdminModule\Components\User\DoResetPasswordForm\DoResetPasswordForm;
use Dravencms\AdminModule\Components\User\DoResetPasswordForm\DoResetPasswordFormFactory;
use Dravencms\AdminModule\Components\User\ResetPasswordForm\ResetPasswordForm;
use Dravencms\AdminModule\Components\User\ResetPasswordForm\ResetPasswordFormFactory;
use Dravencms\AdminModule\Components\User\SignInForm\SignInForm;
use Dravencms\AdminModule\Components\User\SignInForm\SignInFormFactory;
use Dravencms\AdminModule\Components\User\SignUpForm\SignUpForm;
use Dravencms\AdminModule\Components\User\SignUpForm\SignUpFormFactory;
use Dravencms\Flash;
use Dravencms\Admin\Menu\AdminMenuRegistry;
use Dravencms\Security\Authenticator;
use Dravencms\Model\User\Repository\PasswordResetRepository;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{
    #[Persistent]
    public $backlink = '';

    /** @var PasswordResetRepository @inject */
    public $userPasswordResetRepository;

    /** @var AdminMenuRegistry @inject */
    public $adminMenuRegistry;

    /** @var SignInFormFactory @inject */
    public $signInFormFactory;

    /** @var ResetPasswordFormFactory @inject */
    public $resetPasswordFormFactory;

    /** @var SignUpFormFactory @inject */
    public $signUpFormFactory;

    /** @var DoResetPasswordFormFactory @inject */
    public $doResetPasswordFormFactory;

    /** @var AdminAuthenticator @inject */
    public $authenticator;

    /** @var \Dravencms\Admin\AuthRateLimiter @inject */
    public $authRateLimiter;

    private $allowRegister = false; //!FIXME INTO CONFIG

    private $foundPasswordReset = null;

    public function beforeRender(): void
    {
        parent::beforeRender();

        $this->template->name = 'DravenCMS';
        $this->template->bodyClass = $this->getAction() === 'up' ? 'page-signup' : 'page-signin';
    }


    /**
     * @return \Dravencms\AdminModule\Components\User\SignInForm\SignInForm
     */
    protected function createComponentSignInForm(): SignInForm
    {
        $signInControl = $this->signInFormFactory->create();
        $signInControl->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $signInControl;
    }

    /**
     * @param Form $form
     */
    public function signInFormSucceeded(Form $form): void
    {
        $user = $this->getUser();
        $values = $form->getValues();
        if (!$this->authRateLimiter->consume('login', $values->email, $this->getHttpRequest()->getRemoteAddress() ?? 'unknown')) {
            $form->addError('admin.validation.tooManyAttempts');
            return;
        }
        if ($values->remember) {
            $user->setExpiration('14 days', false);
        } else {
            $user->setExpiration('50 minutes', true);
        }

        try {
            $user->setAuthenticator($this->authenticator);
            $user->login($values->email, $values->password);
            \Dravencms\Security\MutationGuard::rotate($this->getSession());


            // Custom implementation of restoreRequest
            // Check if restore request is not targeting Admin homepage, if is not, continue, if is try to go to menu home, if no right for menuhome go to Home
            $homepage = ':Admin:Homepage:Homepage:default';
            $session = $this->getSession('Nette.Application/requests');
            if (!(!isset($session[$this->backlink]) || ($session[$this->backlink][0] !== null && $session[$this->backlink][0] !== $user->getId()))) {
                $presenterName = $session[$this->backlink][1]->getPresenterName();
                $parameters = $session[$this->backlink][1]->getParameters();

                if ($homepage != ':' . $presenterName . ':' . $parameters['action']) {
                    //Restore request if its not homepage
                    $this->restoreRequest($this->backlink);
                }
            }

            if ($homepage = $this->adminMenuRegistry->getHomePage(fn(string $resource, ?string $privilege): bool => $user->isAllowed($resource, $privilege))) {
                $redirect = $homepage->destination;
                $parameters = $homepage->parameters;
            } else {
                $redirect = ':Admin:Homepage:Homepage:default';
                $parameters = [];
            }

            $this->redirect($redirect, $parameters);
        } catch (AuthenticationException $e) {
            $form->addError('admin.validation.invalidCredentials');
        }

    }

    #[\Dravencms\Security\Mutation]
    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        \Dravencms\Security\MutationGuard::rotate($this->getSession());
        $this->flashMessage($this->translator->translate('admin.flash.signedOut'), Flash::INFO);
        $this->redirect('in');
    }

    /**
     * @return \Dravencms\AdminModule\Components\User\ResetPasswordForm\ResetPasswordForm
     */
    protected function createComponentResetPasswordForm(): ResetPasswordForm
    {
        $control = $this->resetPasswordFormFactory->create();
        $control->onSuccess[] = function()
        {
            $this->flashMessage($this->translator->translate('admin.flash.resetEmailSent'), Flash::SUCCESS);
            $this->redirect('in');
        };
        return $control;
    }


    /**
     * @param string $hash
     */
    public function actionPasswordReset(string $hash): void
    {
        $foundPasswordReset = $this->userPasswordResetRepository->getActiveByHash($hash);
        if (!$foundPasswordReset || !$foundPasswordReset->getUser()->isActive() || $foundPasswordReset->getUser()->getNamespace() !== 'Admin')
        {
            $this->error();
        }

        $this->foundPasswordReset = $foundPasswordReset;
    }

    /**
     * @return \Dravencms\AdminModule\Components\User\DoResetPasswordForm\DoResetPasswordForm
     */
    public function createComponentDoPasswordReset(): DoResetPasswordForm
    {
        if ($this->foundPasswordReset === null) { $this->error(); }
        $control = $this->doResetPasswordFormFactory->create($this->foundPasswordReset);
        $control->onSuccess[] = function(){
            $this->flashMessage($this->translator->translate('admin.flash.passwordChanged'), Flash::SUCCESS);
            $this->redirect('Sign:in');
        };

        return $control;
    }

    public function actionUp(): void
    {
        if (!$this->allowRegister) { $this->error(); }
    }

    public function renderUp(): void
    {
        if (!$this->allowRegister) {
            $this->error();
        }

        $this->template->h1 = $this->translator->translate('admin.auth.registration');
    }

    public function renderIn(): void
    {
        $this->template->allowRegister = $this->allowRegister;
    }

    /**
     * @return \Dravencms\AdminModule\Components\User\SignUpForm\SignUpForm
     */
    public function createComponentSignUpForm(): SignUpForm
    {
        if (!$this->allowRegister) { $this->error(); }
        $component = $this->signUpFormFactory->create();
        $component->onSuccess[] = function()
        {
            $this->flashMessage($this->translator->translate('admin.flash.registered'), Flash::SUCCESS);
            $this->redirect('Sign:in');
        };
        return $component;
    }
}
