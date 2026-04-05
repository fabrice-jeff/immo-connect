<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class SecurityController extends AbstractController
{
    private const PUBLISHER_ROLES = ['ROLE_OWNER', 'ROLE_AGENCY', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];

    #[Route('/connexion', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request, SessionInterface $session, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($session->has('immo_user') && $request->isMethod('GET')) {
            $currentUser = (array) $session->get('immo_user', []);

            return $this->redirectToRoute($this->canPublish($currentUser) ? 'app_owner_dashboard' : 'app_home');
        }

        if ($request->isMethod('POST')) {
            $identifier = trim((string) $request->request->get('identifier', ''));
            $password = (string) $request->request->get('password', '');

            $user = $userRepository->findOneByEmailOrPhone($identifier);
            if (!$user instanceof User || !$passwordHasher->isPasswordValid($user, $password)) {
                $this->addFlash('error', 'Identifiants invalides.');

                return $this->redirectToRoute('app_login', array_filter([
                    'redirect_to' => (string) $request->query->get('redirect_to', ''),
                ]));
            }

            $session->set('immo_user', [
                'id' => $user->getId(),
                'name' => trim((string) ($user->getFirstName() . ' ' . $user->getName())),
                'identifier' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]);

            $this->addFlash('success', 'Connexion reussie. Bienvenue sur votre espace ImmoConnect.');

            $redirectTo = (string) $request->query->get('redirect_to', '');

            return $redirectTo !== ''
                ? $this->redirect($redirectTo)
                : $this->redirectToRoute($this->canPublish((array) $session->get('immo_user', [])) ? 'app_owner_dashboard' : 'app_home');
        }

        return $this->render('security/login.html.twig');
    }

    #[Route('/inscription', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $name = trim((string) $request->request->get('name', ''));
            $firstName = trim((string) $request->request->get('first_name', ''));
            $email = trim((string) $request->request->get('email', ''));
            $phone = trim((string) $request->request->get('phone', ''));
            $password = (string) $request->request->get('password', '');
            $role = trim((string) $request->request->get('role', 'Proprietaire'));

            if ($email === '' || $phone === '' || $password === '') {
                $this->addFlash('error', 'Merci de renseigner email, numero et mot de passe.');

                return $this->redirectToRoute('app_register');
            }

            if ($userRepository->findOneBy(['email' => $email]) instanceof User) {
                $this->addFlash('error', 'Un compte existe deja avec cet email.');

                return $this->redirectToRoute('app_register');
            }

            $user = new User();
            $user
                ->setName($name)
                ->setFirstName($firstName !== '' ? $firstName : 'Utilisateur')
                ->setEmail($email)
                ->setPhone($phone)
                ->setIsActive(true)
                ->setRoles($roles = match ($role) {
                    'Agence immobiliere' => ['ROLE_AGENCY'],
                    'Proprietaire' => ['ROLE_OWNER'],
                    'Acheteur' => ['ROLE_BUYER'],
                    default => ['ROLE_TENANT'],
                })
                ->setPassword($passwordHasher->hashPassword($user, $password));

            $entityManager->persist($user);
            $entityManager->flush();

            $session->set('immo_user', [
                'id' => $user->getId(),
                'name' => trim($firstName . ' ' . $name) !== '' ? trim($firstName . ' ' . $name) : 'Nouvel utilisateur',
                'identifier' => $email,
                'roles' => array_values(array_unique(array_merge($roles, ['ROLE_USER']))),
            ]);

            $this->addFlash('success', 'Compte cree avec succes. Vous etes maintenant connecte.');

            return $this->redirectToRoute($this->canPublish((array) $session->get('immo_user', [])) ? 'app_owner_dashboard' : 'app_home');
        }

        return $this->render('security/register.html.twig');
    }

    #[Route('/deconnexion', name: 'app_logout', methods: ['POST'])]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('immo_user');
        $this->addFlash('info', 'Vous avez ete deconnecte.');

        return $this->redirectToRoute('app_home');
    }

    /**
     * @param array<string, mixed> $sessionUser
     */
    private function canPublish(array $sessionUser): bool
    {
        $roles = $sessionUser['roles'] ?? [];

        if (!is_array($roles)) {
            return false;
        }

        return [] !== array_intersect($roles, self::PUBLISHER_ROLES);
    }
}
