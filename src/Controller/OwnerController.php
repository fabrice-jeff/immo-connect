<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Property;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\MediaRepository;
use App\Repository\PropertyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class OwnerController extends AbstractController
{
    private const PUBLISHER_ROLES = ['ROLE_OWNER', 'ROLE_AGENCY', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];

    #[Route('/proprietaire/dashboard', name: 'app_owner_dashboard', methods: ['GET'])]
    public function dashboard(PropertyRepository $propertyRepository, MediaRepository $mediaRepository, SessionInterface $session): Response
    {
        if (!$session->has('immo_user')) {
            $this->addFlash('info', 'Connectez-vous pour acceder a votre espace proprietaire.');

            return $this->redirectToRoute('app_login', [
                'redirect_to' => $this->generateUrl('app_owner_dashboard'),
            ]);
        }

        $currentUser = (array) $session->get('immo_user', []);
        if (!$this->canPublish($currentUser)) {
            $this->addFlash('info', 'Seuls les proprietaires, agences et administrateurs peuvent gerer ou publier des annonces.');

            return $this->redirectToRoute('app_home');
        }

        $properties = array_map(fn (Property $property): array => $this->mapDashboardProperty($property), $propertyRepository->searchCatalog());

        return $this->render('owner/dashboard.html.twig', [
            'dashboard' => [
                'metrics' => [
                    ['label' => 'Biens publies', 'value' => count($properties), 'icon' => 'bi-house-door'],
                    ['label' => 'Demandes recues', 'value' => 38, 'icon' => 'bi-chat-dots'],
                    ['label' => 'Paiements du mois', 'value' => '3,8 M FCFA', 'icon' => 'bi-wallet2'],
                    ['label' => 'Visites planifiees', 'value' => 9, 'icon' => 'bi-calendar-check'],
                ],
                'portfolio' => array_slice($properties, 0, 4),
                'messages' => [
                    ['from' => 'M. Kouame', 'subject' => 'Visite video pour terrain a Bingerville', 'time' => 'il y a 15 min'],
                    ['from' => 'Agence Nsia Habitat', 'subject' => 'Offre de reprise sur villa duplex', 'time' => 'il y a 1 h'],
                    ['from' => 'Mme Diallo', 'subject' => 'Question sur le contrat de location', 'time' => 'aujourd hui'],
                ],
                'payments' => [
                    ['label' => 'Loyer residence Riviera 3', 'amount' => '850 000 FCFA', 'status' => 'Paye'],
                    ['label' => 'Reservation hotel business', 'amount' => '420 000 FCFA', 'status' => 'En attente'],
                    ['label' => 'Frais de dossier juridique', 'amount' => '125 000 FCFA', 'status' => 'Programme'],
                ],
            ],
        ]);
    }

    #[Route('/proprietaire/publier', name: 'app_owner_publish', methods: ['GET', 'POST'])]
    public function publish(Request $request, SessionInterface $session, EntityManagerInterface $entityManager, UserRepository $userRepository, CategoryRepository $categoryRepository): Response
    {
        if (!$session->has('immo_user')) {
            $this->addFlash('info', 'Vous devez vous connecter avant de publier une annonce.');

            return $this->redirectToRoute('app_login', [
                'redirect_to' => $this->generateUrl('app_owner_publish'),
            ]);
        }

        $currentUser = (array) $session->get('immo_user', []);
        if (!$this->canPublish($currentUser)) {
            $this->addFlash('info', 'La publication d annonce est reservee aux proprietaires, agences et administrateurs.');

            return $this->redirectToRoute('app_home');
        }

        $categories = $categoryRepository->findBy([], ['title' => 'ASC']);
        $formData = [
            'title' => '',
            'category' => '',
            'operation' => 'A vendre',
            'price' => '',
            'surface' => '',
            'rooms' => '',
            'city' => '',
            'location' => '',
            'description' => '',
            'available_now' => true,
        ];

        if ($request->isMethod('POST')) {
            $formData = [
                'title' => trim((string) $request->request->get('title', '')),
                'category' => trim((string) $request->request->get('category', '')),
                'operation' => trim((string) $request->request->get('operation', 'A vendre')),
                'price' => trim((string) $request->request->get('price', '')),
                'surface' => trim((string) $request->request->get('surface', '')),
                'rooms' => trim((string) $request->request->get('rooms', '')),
                'city' => trim((string) $request->request->get('city', '')),
                'location' => trim((string) $request->request->get('location', '')),
                'description' => trim((string) $request->request->get('description', '')),
                'available_now' => $request->request->getBoolean('available_now'),
            ];

            if (!$this->isCsrfTokenValid('publish_property', (string) $request->request->get('_token'))) {
                $this->addFlash('error', 'La session du formulaire a expire. Merci de reessayer.');
            } else {
                $owner = $userRepository->find((int) ($currentUser['id'] ?? 0));
                if (!$owner instanceof User) {
                    $session->remove('immo_user');
                    $this->addFlash('error', 'Votre session n est plus valide. Merci de vous reconnecter.');

                    return $this->redirectToRoute('app_login', [
                        'redirect_to' => $this->generateUrl('app_owner_publish'),
                    ]);
                }

                $category = $categoryRepository->findOneBy(['title' => $formData['category']]);
                $mainImage = $request->files->get('main_image');
                $galleryImages = $request->files->get('gallery_images', []);
                $uploadFiles = [];
                if ($mainImage instanceof UploadedFile) {
                    $uploadFiles[] = $mainImage;
                }
                if (is_array($galleryImages)) {
                    foreach ($galleryImages as $galleryImage) {
                        if ($galleryImage instanceof UploadedFile) {
                            $uploadFiles[] = $galleryImage;
                        }
                    }
                }

                $errors = $this->validatePublishForm($formData, $category !== null, $uploadFiles);
                if ($errors !== []) {
                    foreach ($errors as $error) {
                        $this->addFlash('error', $error);
                    }
                } else {
                    $property = new Property();
                    $property
                        ->setTitle($formData['title'])
                        ->setCategory($category)
                        ->setOperation(in_array($formData['operation'], ['A vendre', 'A louer'], true) ? $formData['operation'] : 'A vendre')
                        ->setPrice((float) $formData['price'])
                        ->setSurface((float) $formData['surface'])
                        ->setRoomsNumber((int) $formData['rooms'])
                        ->setLocation($this->buildLocation($formData['city'], $formData['location']))
                        ->setDescription($formData['description'])
                        ->setIsAvailable($formData['available_now'])
                        ->setOwner($owner)
                        ->setInsertBy($owner);

                    $entityManager->persist($property);
                    $entityManager->flush();

                    foreach ($uploadFiles as $file) {
                        if (!$file->isValid()) {
                            continue;
                        }

                        $media = new Media();
                        $media
                            ->setProperty($property)
                            ->setFileUrl($this->storePropertyImage($file))
                            ->setTypeFile('image')
                            ->setInsertBy($owner);

                        $entityManager->persist($media);
                    }

                    $entityManager->flush();

                    $this->addFlash('success', 'Annonce publiee avec succes.');

                    return $this->redirectToRoute('app_property_show', ['id' => $property->getId()]);
                }
            }
        }

        return $this->render('owner/publish.html.twig', [
            'categories' => $categories,
            'operations' => ['A vendre', 'A louer'],
            'form_data' => $formData,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function mapDashboardProperty(Property $property): array
    {
        return [
            'id' => (int) $property->getId(),
            'title' => (string) $property->getTitle(),
            'location' => $property->getLocation() ?? 'Localisation a completer',
            'price' => (float) $property->getPrice(),
            'status' => $property->isAvailable() ? 'Disponible' : 'Indisponible',
        ];
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

    /**
     * @param array<string, mixed> $formData
     * @param UploadedFile[] $uploadFiles
     *
     * @return string[]
     */
    private function validatePublishForm(array $formData, bool $categoryExists, array $uploadFiles): array
    {
        $errors = [];

        if ($formData['title'] === '') {
            $errors[] = 'Le titre de l annonce est obligatoire.';
        }

        if (!$categoryExists) {
            $errors[] = 'Le type de bien selectionne est invalide.';
        }

        if (!in_array($formData['operation'], ['A vendre', 'A louer'], true)) {
            $errors[] = 'Le mode de publication est invalide.';
        }

        if ((float) $formData['price'] <= 0) {
            $errors[] = 'Le prix doit etre superieur a 0.';
        }

        if ((float) $formData['surface'] <= 0) {
            $errors[] = 'La surface doit etre superieure a 0.';
        }

        if ((int) $formData['rooms'] <= 0) {
            $errors[] = 'Le nombre de pieces doit etre superieur a 0.';
        }

        if ($formData['city'] === '' && $formData['location'] === '') {
            $errors[] = 'Merci de renseigner au moins une localisation.';
        }

        if ($formData['description'] === '') {
            $errors[] = 'La description du bien est obligatoire.';
        }

        if ($uploadFiles === []) {
            $errors[] = 'Ajoutez au moins une image pour publier le bien.';
        }

        return $errors;
    }

    private function buildLocation(string $city, string $area): string
    {
        $parts = array_values(array_filter([$area, $city], static fn (?string $item): bool => $item !== null && trim($item) !== ''));

        return implode(', ', $parts);
    }

    private function storePropertyImage(UploadedFile $file): string
    {
        $filesystem = new Filesystem();
        $relativeDirectory = 'uploads/properties/' . date('Y/m');
        $targetDirectory = $this->getParameter('kernel.project_dir') . '/public/' . $relativeDirectory;
        $filesystem->mkdir($targetDirectory);

        $extension = $file->guessExtension() ?: 'jpg';
        $filename = uniqid('property_', true) . '.' . $extension;
        $file->move($targetDirectory, $filename);

        return '/' . $relativeDirectory . '/' . $filename;
    }
}
