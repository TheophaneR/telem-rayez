<?php

namespace App\Controller;

use App\Form\ProductType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        return $this->render('base.html.twig', ['products'=>$products]);
    }

    #[Route('product/new', name: 'product_add')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();

        $form = $this->createForm(
            ProductType::class,
            $product
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($product);
            $em->flush();

            return new Response('produit ajouté');
        }

        return $this->render('product/product_form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}