<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageProductController extends AbstractController
{
    #[Route('manage/product/new', name: 'manage_product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Product();

        $form = $this->createForm(
            ProductType::class,
            $product,
        );
        $form->add('Ajouter', SubmitType::class); // permet d'ajouter un champ à ceux prévus dans la classe ProductType
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // mise à jour de la date de création
            $product->setCreatedAt(new \DateTimeImmutable());
            // persister l'objet en bdd
            $em->persist($product);
            // synchronisation des objets persistés dans la bdd
            $em->flush();
            $this->addFlash('success', 'Le produit a été ajouté au catalogue.');

            // on redirige l'utilisateur (ex : vers la page du catalogue)
            return $this->redirectToRoute('product_show_all');
        }
        return $this->renderForm('product/product_new.html.twig',
            [
                'form' => $form
            ]
        );
    }

    #[Route('manage/product/edit/{id}', name: 'manage_product_edit', requirements: ['id' => '\d+'])]
    public function edit(Product $product, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(
            ProductType::class,
            $product,
        );
        $form->add('updateProduct', SubmitType::class, [
            'label' => 'Modifier le produit',
            'attr' => [
                'class' => 'Button -no-danger -reverse'
            ]
        ]); // permet d'ajouter un champ à ceux prévus dans la classe ProductType
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // mise à jour de la date de création
            $product->setCreatedAt(new \DateTimeImmutable());

            // synchronisation des objets persistés dans la bdd
            $em->flush();
            $this->addFlash('success', 'Le produit a été mis à jour dans la bdd.');

            // on redirige l'utilisateur (ex : vers la page du catalogue)
            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }
        return $this->renderForm('product/product_new.html.twig', [
            'form' => $form,
            'product' => $product
        ]);
    }

    #[Route('manage/product/delete/{id}', name: 'manage_product_delete', requirements: ['id' => '\d+'])]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $id = $product->getId();

        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Le produit a été supprimé',);

        return $this->redirectToRoute('product_show_all');
    }

    #[Route('manage/product/delete-confirm/{id}', 'manage_product_delete_confirm', requirements: ['id' => '\d+'])]
    public function deleteConfirm(Product $product)
    {
        return $this->render('product/product_delete_confirm.html.twig', [
            'product' => $product
        ]);
    }
}