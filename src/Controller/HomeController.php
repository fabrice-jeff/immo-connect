<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\MediaRepository;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(Request $request, PropertyRepository $propertyRepository, MediaRepository $mediaRepository): Response
    {
        $filters = [
            'q' => $request->query->getString('q'),
            'category' => $request->query->getString('category'),
            'operation' => $request->query->getString('operation'),
            'max_price' => $request->query->getString('max_price'),
        ];

        $latest = array_map(fn (Property $property): array => $this->mapProperty($property, $mediaRepository), array_slice($propertyRepository->searchCatalog(), 0, 6));
        $searchPreview = array_map(fn (Property $property): array => $this->mapProperty($property, $mediaRepository), array_slice($propertyRepository->searchCatalog($filters), 0, 3));

        return $this->render('home/index.html.twig', [
            'page' => array_merge($this->getHomePageData(), ['latest' => $latest]),
            'filters' => $filters,
            'search_preview' => $searchPreview,
        ]);
    }

    #[Route('/proprietes', name: 'app_property_index', methods: ['GET'])]
    public function properties(Request $request, PropertyRepository $propertyRepository, MediaRepository $mediaRepository): Response
    {
        $filters = [
            'q' => $request->query->getString('q'),
            'category' => $request->query->getString('category'),
            'operation' => $request->query->getString('operation'),
            'rooms' => $request->query->getInt('rooms'),
            'max_price' => $request->query->getString('max_price'),
            'sort' => $request->query->getString('sort', 'recent'),
        ];

        return $this->render('property/index.html.twig', [
            'properties' => array_map(fn (Property $property): array => $this->mapProperty($property, $mediaRepository), $propertyRepository->searchCatalog($filters)),
            'filters' => $filters,
            'categories' => ['Appartement', 'Maison', 'Terrain', 'Boutique', 'Hotel', 'Coworking'],
            'operations' => ['A vendre', 'A louer'],
        ]);
    }

    #[Route('/proprietes/{id}', name: 'app_property_show', requirements: ['id' => '\\d+'], methods: ['GET', 'POST'])]
    public function show(int $id, Request $request, PropertyRepository $propertyRepository, MediaRepository $mediaRepository): Response
    {
        $entity = $propertyRepository->find($id);
        if (!$entity instanceof Property) {
            throw $this->createNotFoundException('Bien immobilier introuvable.');
        }

        if ($request->isMethod('POST')) {
            $this->addFlash('success', 'Votre demande a ete enregistree. Un conseiller vous recontactera rapidement.');

            return $this->redirectToRoute('app_property_show', ['id' => $id]);
        }

        $property = $this->mapProperty($entity, $mediaRepository);
        $related = array_values(array_filter(
            array_map(fn (Property $item): array => $this->mapProperty($item, $mediaRepository), $propertyRepository->searchCatalog(['category' => $property['category']])),
            static fn (array $item): bool => $item['id'] !== $property['id']
        ));

        return $this->render('property/show.html.twig', [
            'property' => $property,
            'related' => array_slice($related, 0, 3),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getHomePageData(): array
    {
        return [
            'stats' => [
                ['value' => '1 500+', 'label' => 'biens diffuses'],
                ['value' => '320+', 'label' => 'proprietaires actifs'],
                ['value' => '96%', 'label' => 'clients satisfaits'],
                ['value' => '24/7', 'label' => 'support et accompagnement'],
            ],
            'categories' => [
                ['name' => 'Appartement', 'icon' => 'bi-buildings', 'description' => 'Locations et ventes en centre-ville'],
                ['name' => 'Maison', 'icon' => 'bi-house-heart', 'description' => 'Biens familiaux, villas et residences'],
                ['name' => 'Terrain', 'icon' => 'bi-map', 'description' => 'Parcelles, lotissements et investissement'],
                ['name' => 'Boutique', 'icon' => 'bi-shop', 'description' => 'Locaux commerciaux et espaces de vente'],
                ['name' => 'Hotel', 'icon' => 'bi-building', 'description' => 'Residence et hotellerie'],
                ['name' => 'Coworking', 'icon' => 'bi-pc-display', 'description' => 'Bureaux flexibles et espaces de travail'],
            ],
            'services' => [
                ['title' => 'Recherche intelligente', 'description' => 'Filtres rapides par zone, prix, type de bien et nombre de pieces.', 'icon' => 'bi-search'],
                ['title' => 'Visite a distance', 'description' => 'Prise de rendez-vous et visites video pour gagner du temps.', 'icon' => 'bi-camera-video'],
                ['title' => 'Accompagnement juridique', 'description' => 'Lien direct avec notaire, avocat et service de titre foncier.', 'icon' => 'bi-shield-check'],
                ['title' => 'Gestion proprietaire', 'description' => 'Paiements, contrats, messagerie interne et factures telechargeables.', 'icon' => 'bi-speedometer2'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapProperty(Property $property, MediaRepository $mediaRepository): array
    {
        $media = $mediaRepository->findBy(['property' => $property]);
        $images = array_map(static fn ($item): string => $item->getFileUrl(), $media);
        if ($images === []) {
            $images = ['https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80'];
        }

        $owner = $property->getOwner();
        $category = $property->getCategory();

        return [
            'id' => (int) $property->getId(),
            'title' => (string) $property->getTitle(),
            'price' => (float) $property->getPrice(),
            'surface' => (float) $property->getSurface(),
            'rooms' => (int) $property->getRoomsNumber(),
            'bathrooms' => max(1, (int) floor(((int) $property->getRoomsNumber()) / 2)),
            'category' => $category?->getTitle() ?? 'Maison',
            'operation' => $property->getOperation() ?? 'A vendre',
            'location' => $property->getLocation() ?? 'Localisation a completer',
            'description' => (string) $property->getDescription(),
            'excerpt' => mb_strimwidth((string) $property->getDescription(), 0, 145, '...'),
            'status' => $property->isAvailable() ? 'Disponible' : 'Indisponible',
            'images' => $images,
            'amenities' => ['Parking', 'Climatisation', 'Balcon', 'Securite'],
            'owner' => [
                'name' => trim((string) ($owner?->getFirstName() . ' ' . $owner?->getName())) ?: 'Proprietaire',
                'phone' => $owner?->getPhone() ?? '+225 00 00 00 00 00',
                'email' => $owner?->getEmail() ?? 'contact@immoconnect.local',
            ],
            'payment_methods' => ['MTN Money', 'Moov Money', 'Virement bancaire'],
            'legal_services' => ['Titre foncier', 'Verification notariale', 'Contrat de location'],
            'published_at' => $property->getCreatedAt()?->format(\DateTimeInterface::ATOM) ?? '2026-03-01T08:00:00+00:00',
        ];
    }
}
