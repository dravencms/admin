<?php declare(strict_types = 1);

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\AdminModule\Components\User\DoResetPasswordForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Security\PasswordManager;
use Dravencms\Model\User\Entities\PasswordReset;
use Dravencms\Model\User\Repository\UserRepository;
use Dravencms\Database\EntityManager;
use Dravencms\Components\BaseForm\Form;

class DoResetPasswordForm extends BaseControl
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var UserRepository */
    private $userRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var PasswordReset */
    private $passwordReset;

    private \Dravencms\Model\User\Repository\PasswordResetRepository $passwordResetRepository;

    /** @var PasswordManager */
    private $passwordManager;


    public $onSuccess = [];

    /**
     * DoResetPasswordForm constructor.
     * @param PasswordReset $passwordReset
     * @param BaseFormFactory $baseFormFactory
     * @param UserRepository $userRepository
     * @param EntityManager $entityManager
     * @param PasswordManager $passwordManager,
        \Dravencms\Model\User\Repository\PasswordResetRepository $passwordResetRepository
     */
    public function __construct(
        PasswordReset $passwordReset,
        BaseFormFactory $baseFormFactory,
        UserRepository $userRepository,
        EntityManager $entityManager,
        PasswordManager $passwordManager,
        \Dravencms\Model\User\Repository\PasswordResetRepository $passwordResetRepository
    ) {
        $this->baseFormFactory = $baseFormFactory;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordReset = $passwordReset;
        $this->passwordManager = $passwordManager;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = $this->baseFormFactory->create();

        $form->addPassword('password')->addFilter(\Nette\Utils\Strings::trim(...))->setRequired('admin.validation.passwordRequired')
            ->addRule(Form::MIN_LENGTH, 'admin.validation.passwordMinLength', 8);

        $form->addPassword('passwordAgain')->addFilter(\Nette\Utils\Strings::trim(...))
            ->setRequired('admin.validation.passwordAgainRequired')
            ->addRule(Form::EQUAL, 'admin.validation.passwordsMismatch', $form['password']);

        $form->addSubmit('send', 'admin.auth.resetPassword');

        $form->onSuccess[] = [$this, 'onSuccessForm'];
        $form->onValidate[] = [$this, 'onValidateForm'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function onValidateForm(Form $form): void
    {
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function onSuccessForm(Form $form): void
    {
        $values = $form->getValues();

        if (!$this->passwordResetRepository->consume($this->passwordReset, $values->password, $this->passwordManager)) {
            $form->addError('admin.validation.invalidReset'); return;
        }

        $this->onSuccess();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/DoResetPasswordForm.latte');
        $template->render();
    }
}