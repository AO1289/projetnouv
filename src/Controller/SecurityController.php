<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authUtils, EntityManagerInterface $em, Request $request): Response
    {
        // Récupère l'erreur d'authentification
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        // Vérifie si l'erreur est due à un utilisateur inconnu
        if ($error && $lastUsername) {
            $user = $em->getRepository(User::class)->findOneBy(['email' => $lastUsername]);

            if (!$user) {
                // Message d'erreur personnalisé si l'utilisateur est inconnu
                $error = 'Cet utilisateur n\'est pas reconnu. Veuillez essayer avec un autre utilisateur.';
            }
        }

        // Stocke l'erreur dans la session pour l'afficher dans le template
        $request->getSession()->set('login_error', $error);

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
