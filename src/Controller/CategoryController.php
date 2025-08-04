<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;


class CategoryController extends AbstractController
{
    // Liste, Ajout, Modif et Supp les category
    #[Route('/category', name: 'app_category')]
    public function new(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $categories = $categoryRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,

        ]);

    }

    // nouvelle page Ajout

   /* #[Route('/category/new', name: 'category_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_new');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }*/
}
