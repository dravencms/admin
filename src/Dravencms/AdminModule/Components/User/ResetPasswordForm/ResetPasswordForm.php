<?php declare(strict_types = 1);

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\AdminModule\Components\User\ResetPasswordForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\User\Entities\PasswordReset;
use Dravencms\Model\User\Entities\User;
use Dravencms\Model\User\Repository\PasswordResetRepository;
use Dravencms\Model\User\Repository\UserRepository;
use Dravencms\Database\EntityManager;
use Dravencms\Components\BaseForm\Form;
use Salamek\TemplatedEmail\TemplatedEmail;

class ResetPasswordForm extends BaseControl
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var TemplatedEmail */
    private $templatedEmail;

    /** @var UserRepository */
    private $userRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var PasswordResetRepository */
    private $passwordResetRepository;

    /** @var string */
    private $namespace = 'Admin';

    public $onSuccess = [];

    /**
     * ResetPasswordForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param TemplatedEmail $templatedEmail
     * @param UserRepository $userRepository
     * @param EntityManager $entityManager
     * @param PasswordResetRepository $passwordResetRepository
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        TemplatedEmail $templatedEmail,
        UserRepository $userRepository,
        EntityManager $entityManager,
        PasswordResetRepository $passwordResetRepository
    )
    {
        $this->baseFormFactory = $baseFormFactory;
        $this->templatedEmail = $templatedEmail;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = $this->baseFormFactory->create();

        $form->addText('email')
            ->setRequired('admin.validation.emailRequired')
            ->addRule(Form::EMAIL, 'admin.validation.emailInvalid');

        $form->addSubmit('send', 'admin.auth.sendResetLink');

        $form->onSuccess[] = [$this, 'onSuccessForm'];
        $form->onValidate[] = [$this, 'onValidateForm'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function onValidateForm(Form $form): void
    {
        $values = $form->getValues();
        if (!$this->userRepository->getOneByEmail($values->email, $this->namespace)) {
            $form->addError('admin.validation.emailNotFound');
        }
    }

    /**
     * @param Form $form
     * @throws \Nette\Application\BadRequestException
     */
    public function onSuccessForm(Form $form): void
    {
        $values = $form->getValues();

        /** @var User $user */
        $user = $this->userRepository->getOneByEmail($values->email, $this->namespace);
        if (!$user) {
            $this->getPresenter()->error();
        }
        // Softdelte any previous password resets
        $this->passwordResetRepository->cleanPreviousPasswordResetsForUser($user);

        // Create new password reset
        $expiry = new \DateTime;
        $expiry->modify('+24 hours');

        $newPasswordReset = new PasswordReset($user, $expiry);

        $this->entityManager->persist($newPasswordReset);
        $this->entityManager->flush();

        $subject = $form->getTranslator()->translate('admin.email.passwordResetSubject');
        $this->templatedEmail->resetPassword([
            'title' => $subject,
            'email' => $user->getEmail(),
            'hash' => $newPasswordReset->getHash()
        ])
            ->addTo($user->getEmail())
            ->setSubject($subject)
            ->send();

        $this->onSuccess();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/ResetPasswordForm.latte');
        $template->render();
    }
}
