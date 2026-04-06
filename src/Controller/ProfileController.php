<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile')]
final class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function index(ReservationRepository $repo, EntityManagerInterface $em): Response
    {

        if(!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $favoris = $em->getRepository(Favoris::class)->findBy(['client' => $user]);
        $books = array_map(fn($favori) => $favori->getBook(), $favoris);

        $reservations = $repo->findBy(['client' => $this->getUser()]);

        return $this->render('profile/index.html.twig', [
            'reservations' => $reservations,
            'books' => $books,
        ]);
    }

    #[Route('/reservation', name: 'app_profile_reservation', methods: ['GET'])]
    public function page_reservation(Request $request, BookRepository $bookRepository): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));

        $paginator = $bookRepository->findAllPaginate($page, 8);
        return $this->render('reservation/index.html.twig', [
            'books' => $paginator,
            'page' => $page,
            'total' => count($paginator),
            'limit' => 8
        ]);
    }
}
