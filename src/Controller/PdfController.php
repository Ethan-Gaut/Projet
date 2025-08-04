<?php

namespace App\Controller;

use App\Entity\Pdf;
use App\Form\PdfType;
use App\Repository\PdfRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class PdfController extends AbstractController
{
    #[Route('/pdfs', name: 'app_pdf')]
    public function index(PdfRepository $pdfRepository): Response
    {
        $pdfs = $pdfRepository->findAll();

        return $this->render('pdf/index.html.twig', [
            'pdfs' => $pdfs,
        ]);
    }

    #[Route('/pdfs/new', name: 'pdf_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $pdf = new Pdf();
        $form = $this->createForm(PdfType::class, $pdf);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdfFile = $form->get('file')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l’upload du fichier.');
                }

                $pdf->setFile($newFilename);
            }

            $em->persist($pdf);
            $em->flush();

            return $this->redirectToRoute('app_pdf');
        }

        return $this->render('pdf/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pdfs/{id}', name: 'pdf_show')]
    public function show(Pdf $pdf): Response
    {
        return $this->render('pdf/show.html.twig', [
            'pdf' => $pdf,
        ]);
    }

    #[Route('/pdf/{id}/delete', name: 'pdf_delete', methods: ['POST'])]
public function delete(
    Request $request,
    Pdf $pdf,
    EntityManagerInterface $em,
    CsrfTokenManagerInterface $csrfTokenManager
): Response {
    $submittedToken = $request->request->get('_token');

    if ($csrfTokenManager->isTokenValid(new CsrfToken('delete-pdf' . $pdf->getId(), $submittedToken))) {
        $em->remove($pdf);
        $em->flush();

        $this->addFlash('success', 'PDF supprimé avec succès.');
    } else {
        $this->addFlash('error', 'Token CSRF invalide.');
    }

    return $this->redirectToRoute('app_pdf');
}
}