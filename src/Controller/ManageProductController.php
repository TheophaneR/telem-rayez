<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Type\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageProductController extends AbstractController
{
    #[Route('manage/product/new', name: 'manage_product_new')]
    public function new(): Response
    {
        $product = new Product();

        $form = $this->createForm(
            ProductType::class,
            $product,
        );
        $form->add('Ajouter', SubmitType::class); // permet d'ajouter un champ à ceux prévus dans la classe ProductType
        return $this->renderForm('product/product_new.html.twig',
            [
                'form' => $form
            ]
        );
    }
}