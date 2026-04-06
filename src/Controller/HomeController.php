<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->render('home/index.html.twig');
        }

        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            return $this->redirectToRoute('admin_user_index');
        }

        if (in_array('ROLE_LIBRARIAN', $roles, true)) {
            return $this->redirectToRoute('app_book_index');
        }

        if (in_array('ROLE_USER', $roles, true)) {
            return $this->redirectToRoute('app_profile');
        }

        // Fallback (rare)
        return $this->render('home/index.html.twig');
    }

}
