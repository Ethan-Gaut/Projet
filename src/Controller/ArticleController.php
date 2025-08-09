<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    #[Route('/article', name: 'app_article')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findGroupedByCategoryLimited();
    
        return $this->render('article/index.html.twig', [
            'groupedArticles' => $articles,
        ]);
    }

    #[Route('/article/new', name: 'article_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload de l’image.');
                }

                $article->setImage($newFilename);
            }

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_article');
        }

        return $this->render('article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}', name: 'article_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}/delete', name: 'article_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Article $article,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $submittedToken = $request->request->get('_token');

        if ($csrfTokenManager->isTokenValid(new CsrfToken('delete-article', $submittedToken))) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', 'Article supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_article');
    }

    #[Route('/article/categorie/{name}', name: 'articles_by_category')]
    public function byCategory(ArticleRepository $articleRepository, string $name): Response
    {
        $articles = $articleRepository->findByCategoryName($name);
    
        return $this->render('article/by_category.html.twig', [
            'categoryName' => $name,
            'articles' => $articles
        ]);
    }


    
}
