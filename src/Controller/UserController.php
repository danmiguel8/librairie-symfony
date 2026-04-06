<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile/user')]
class UserController extends AbstractController
{
    #[Route('/account', name: 'app_user_account')]
    public function account(): Response
    {
        return $this->render('user/account.html.twig');
    }
}
