<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @param ProductRepository $productRepository
     * @return Response
     * Contrôleur qui sert une page contenant la liste de tous les produits
     */
    #[Route('/product/show-all', name: 'product_show_all')]
    public function showall(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
//        construction de la page HTML avec les produits récupérés
        return $this->render('product/product_show_all.html.twig', ['products' => $products]);
    }

    /**
     * @return Response
     *
     * Contrôleur qui sert une page contenant la fiche d'un produit
     */
    #[Route('/product/show/{id}', name: 'product_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        // récupération du produit dont l'id a été passé à notre contrôleur
        $product = $productRepository->find($id);
        if (null === $product) {
            throw new NotFoundHttpException('Ce produit n\'existe pas.');
        }
        // Construction de la page HTML avec le produit réceupéré
        return $this->render('product/product_show.html.twig', ['product' => $product]);
    }

    /**
     * Recherche des produits à partir d'un mot clé
     *
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     */
    #[Route('/product/search', name: 'product_search', methods: ['POST'])]
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        $keywordSearched = ($request->request->get('searchProduct'));

        $products = $productRepository->search($keywordSearched);
//        $nbOfResults = $productRepository->searchCount($keywordSearched);
        $nbOfResults = count($products);

        return $this->render('product/product_show_all.html.twig', [
            'products' => $products,
            'nb_of_results' => $nbOfResults,
        ]);
    }
}