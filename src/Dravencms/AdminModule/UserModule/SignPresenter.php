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
use Dravencms\Model\Admin\Entities\Menu;
use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Security\Authenticator;
use Dravencms\Model\User\Repository\PasswordResetRepository;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{
    /** @persistent */
    public $backlink = '';

    /** @var PasswordResetRepository @inject */
    public $userPasswordResetRepository;

    /** @var MenuRepository @inject */
    public $adminMenuRepository;

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

    private $allowRegister = false; //!FIXME INTO CONFIG

    private $foundPasswordReset = null;


    /**
     * @return \Dravencms\AdminModule\Components\User\SignInForm\SignInForm
     */
    protected function createComponentSignInForm(): SignInForm
    {
        $signInControl = $this->signInFormFactory->create();
        $signInControl['form']->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $signInControl;
    }

    /**
     * @param Form $form
     */
    public function signInFormSucceeded(Form $form, $values): void
    {
        $user = $this->getUser();

        if ($values->remember) {
            $user->setExpiration('14 days', false);
        } else {
            $user->setExpiration('50 minutes', true);
        }

        try {
            $user->setAuthenticator($this->authenticator);
            $user->login($values->email, $values->password);


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

            /** @var Menu $homepage */
            if ($homepage = $this->adminMenuRepository->getHomePageForUser($this->getUserEntity())) {
                $redirect = $homepage->getPresenter() . ($homepage->getAction() ? ':' . $homepage->getAction() : ':default');
                $parameters = $homepage->getParameters();
            } else {
                $redirect = ':Admin:Homepage:Homepage:default';
                $parameters = [];
            }

            $this->redirect($redirect, $parameters);
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }

    }

    public function actionOut(): void
    {
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.', Flash::INFO);
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
            $this->flashMessage('Email with reset url was send.', Flash::SUCCESS);
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
        if(!$foundPasswordReset)
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
        $control = $this->doResetPasswordFormFactory->create($this->foundPasswordReset);
        $control->onSuccess[] = function(){
            $this->flashMessage('Password has been successfully changed', Flash::SUCCESS);
            $this->redirect('Sign:in');
        };

        return $control;
    }

    public function renderUp(): void
    {
        if (!$this->allowRegister) {
            $this->error();
        }

        $this->template->h1 = 'Registrace';
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
        $component = $this->signUpFormFactory->create();
        $component->onSuccess[] = function()
        {
            $this->flashMessage('Registrace proběhla úspěšně.', Flash::SUCCESS);
            $this->redirect('Sign:in');
        };
        return $component;
    }
}
