<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(Request $request, BookRepository $repo): Response
    {
        $value = $request->query->get('q', '');
        $page = max(1, (int) $request->query->get('page', 1));

        $paginator = $repo->searchPaginated($value, $page, 8);

        return $this->render('search/index.html.twig', [
            'books' => $paginator,
            'page' => $page,
            'total' => count($paginator),
            'limit' => 8,
            'query' => $value,
        ]);
    }
}
