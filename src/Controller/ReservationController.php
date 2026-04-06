<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Commentaire;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation')]
#[IsGranted('ROLE_USER')]
final class ReservationController extends AbstractController
{

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, BookRepository $bookRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation, [
            'users' => $userRepository->findAll(),
            'books' => $bookRepository->findAvailableBooks()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile_reservation', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/attente', name: 'app_reservation_attente', methods: ['GET'])]
    public function show_reservgation_en_attente(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findByStatus();

        return $this->render('reservation/reservation_attente.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/action/{id}/{action}', name: 'app_reservation_action', methods: ['GET', 'POST'])]
    public function action(Reservation $reservation, EntityManagerInterface $entityManager, string $action): Response
    {

        if (!$this->getUser() || !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour effectuer cette action.');
            return $this->redirectToRoute('app_home');
        }

        foreach ($reservation->getBooks() as $selectedBook) {
            if ($selectedBook->getQuantite() <= 0) {
                $this->addFlash(
                    'danger',
                    "Le livre <strong>{$selectedBook->getTitle()}</strong> n'est pas disponible."
                );
                return $this->redirectToRoute('app_reservation_attente');
            }
        }

 
        if ($action === 'validate') {
            $reservation->setStatus('En cours');

            foreach ($reservation->getBooks() as $selectedBook) {
                $selectedBook->setQuantite($selectedBook->getQuantite() - 1);
                $entityManager->persist($selectedBook);
            }
        } elseif ($action === 'cancel') {
            $reservation->setStatus('Rejeté');
        }

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('app_reservation_attente');
    }


    #[Route('/history', name: 'app_reservation_history', methods: ['GET'])]
    public function show_history(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findAll();

        return $this->render('reservation/history.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/client', name: 'app_reservation_show_client', methods: ['GET'])]
    public function mesReservations(ReservationRepository $repo): Response
    {
        $reservations = $repo->findBy(['client' => $this->getUser()]);

        return $this->render('reservation/mes_reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/reserver/{id}', name: 'app_reservation_reserver', methods: ['GET', 'POST'])]
    public function reserver(Book $book, Request $request, EntityManagerInterface $em, ReservationRepository $resRepo, BookRepository $bookRepository): Response
    {
        $reservation = new Reservation();
        $reservation->addBook($book);
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour réserver un livre.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ReservationType::class, $reservation, [
            'books' => $bookRepository->findAvailableBooks()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $start = $reservation->getDateDebut();
            $end = $reservation->getDateFin();

            foreach ($reservation->getBooks() as $selectedBook) {
                // Vérification disponibilité
                $count = $resRepo->countOverlappingReservations($selectedBook, $start, $end);

                if ($count >= $selectedBook->getQuantite()) {
                    $this->addFlash(
                        'danger',
                        "Le livre <strong>{$selectedBook->getTitle()}</strong> n'est pas disponible à ces dates."
                    );
                    return $this->redirectToRoute('app_reservation_reserver', ['id' => $book->getId()]);
                }

                // Vérification réservation en attente
                if ($resRepo->userHasPendingReservation($this->getUser(), $selectedBook)) {
                    $this->addFlash(
                        'danger',
                        "Vous avez déjà une réservation en attente pour le livre <strong>{$selectedBook->getTitle()}</strong>."
                    );
                    return $this->redirectToRoute('app_reservation_reserver', ['id' => $book->getId()]);
                }
            }
            $reservation->setClient($this->getUser());
            $em->persist($reservation);

            $em->flush();

            $this->addFlash('success', 'Réservation effectuée avec succès.');
            return $this->redirectToRoute('app_profile_reservation');
        }

        return $this->render('reservation/reserver.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/commentaire', name: 'app_reservation_commentaire', methods: ['GET'])]
    public function show_commentaire(Book $book): Response
    {

        $comments = [];

        foreach ($book->getReservations() as $reservation) {
            if ($reservation->getStatus() === 'Terminé') {
                foreach ($reservation->getCommentaires() as $commentaire) {
                    $comments[] = $commentaire;
                }
            }
        }

        return $this->render('commentaire/index.html.twig', [
            'comments' => $comments,
            'book' => $book
        ]);
    }

    #[Route('/{id}/add/commentaire', name: 'app_reservation_commentaire_add', methods: ['POST'])]
    public function add_commentaire(Request $request, Book $book, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $reservation = null;
        foreach ($book->getReservations() as $res) {
            if ($res->getClient() === $user && $res->getStatus() === 'Terminé') {
                $reservation = $res;
                break;
            }
        }

        if (!$reservation) {
            $this->addFlash('error', 'Vous ne pouvez commenter que les livres que vous avez réservés et terminés.');
            return $this->redirectToRoute('app_reservation_commentaire', ['id' => $book->getId()]);
        }

        $comment = new Commentaire();
        $comment->setReservation($reservation);
        $comment->setMessage($request->request->get('message'));
        $comment->setRaiting((int) $request->request->get('raiting'));
        $comment->setCreated(new \DateTime());

        $em->persist($comment);
        $em->flush();

        $this->addFlash('success', 'Votre commentaire a été ajouté.');

        return $this->redirectToRoute('app_reservation_commentaire', ['id' => $book->getId()]);
    }

       #[Route('/{id}/remove/commentaire', name: 'app_reservation_commentaire_remove', methods: ['POST'])]
    public function remove_commentaire(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {

        $em->remove($commentaire);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager, UserRepository $userRepository, BookRepository $bookRepository): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation,
            [
                'users' => $userRepository->findAll(),
                'books' => $bookRepository->findAvailableBooks()
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_history', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profile_reservation', [], Response::HTTP_SEE_OTHER);
    }
}
