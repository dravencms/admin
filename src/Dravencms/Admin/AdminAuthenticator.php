<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 10/15/21
 * Time: 10:35 PM
 */

namespace Dravencms\Admin;


use Dravencms\Model\User\Repository\UserRepository;
use Dravencms\Security\Authenticator;
use Dravencms\Security\PasswordManager;

class AdminAuthenticator extends Authenticator
{
    public function __construct(PasswordManager $passwordManager, UserRepository $userRepository)
    {
        parent::__construct('Admin', $passwordManager, $userRepository);
    }
}