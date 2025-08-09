<?php


namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository
    ): Response {
        // Si on passe ?edit=ID dans l'URL, on modifie cette catégorie
        $categoryId = $request->query->get('edit');
        $category = $categoryId ? $categoryRepository->find($categoryId) : new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', $categoryId ? 'Catégorie modifiée.' : 'Catégorie créée.');
            return $this->redirectToRoute('app_category');
        }

        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'editMode' => $categoryId !== null,
            'category' => $category,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Catégorie supprimée.');
        }

        return $this->redirectToRoute('app_category');
    }
}
