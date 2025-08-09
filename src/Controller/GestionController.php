<?php

// src/Controller/GestionController.php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GestionController extends AbstractController
{
    #[Route('/gestion', name: 'app_gestion')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        // Trier les utilisateurs par rôle : Admins d'abord
        usort($users, function ($a, $b) {
            $priority = [
                'ROLE_ADMIN' => 1,
                'ROLE_USER' => 2,
            ];

            $aRole = min(array_map(fn($role) => $priority[$role] ?? 99, $a->getRoles()));
            $bRole = min(array_map(fn($role) => $priority[$role] ?? 99, $b->getRoles()));

            return $aRole <=> $bRole;
        });

        return $this->render('gestion/index.html.twig', [
            'users' => $users,
        ]);
    }
}
