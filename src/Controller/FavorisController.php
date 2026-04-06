<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Favoris;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/favoris')]
final class FavorisController extends AbstractController
{
    #[Route('/', name: 'app_favoris_index')]
    public function index(EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $favoris = $em->getRepository(Favoris::class)->findBy(['client' => $user]);
        $books = array_map(fn($favori) => $favori->getBook(), $favoris);

        return $this->render('favoris/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/{id}', name: 'app_favoris')]
    public function add_favoris(Request $request, Book $book, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour ajouter un livre aux favoris.');
            return $this->redirectToRoute('app_login');
        }

        $favoris = new Favoris();
        $favoris->setClient($this->getUser());
        $favoris->setBook($book);
        $em->persist($favoris);
        $em->flush();

       return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/{id}/remove', name: 'app_favoris_remove')]
    public function remove(Request $request, Favoris $favoris, EntityManagerInterface $em): Response
    {
        $em->remove($favoris);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
