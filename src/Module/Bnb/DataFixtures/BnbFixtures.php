<?php

declare(strict_types=1);

namespace App\Module\Bnb\DataFixtures;

use Aurora\Module\Editorial\Post\Entity\Post;
use Aurora\Module\Editorial\Post\Entity\PostTranslation;
use Aurora\Module\Editorial\Post\Enum\PostStatusEnum;
use Aurora\Module\Editorial\Post\Service\PostTextExtractor;
use Aurora\Module\Editorial\PostType\Entity\PostType;
use Aurora\Module\Editorial\PostType\Entity\PostTypeField;
use Aurora\Module\Editorial\Taxonomy\Entity\Taxonomy;
use Aurora\Module\Editorial\Taxonomy\Entity\TaxonomyTerm;
use Aurora\Module\Ged\Document\Entity\Document;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

use function assert;

/**
 * Demo content for the guest house theme: a "Chambre" post type, the
 * taxonomies that describe a room, and half a dozen rooms to fill the site.
 *
 * Its own group (`bnb`) so it can be loaded next to the blog demo without
 * either replacing the other — `--group=bnb --append`. Aurora keeps one active
 * theme at a time, so the two demos coexist in the database and you switch by
 * activating a theme rather than reloading data.
 *
 * Rooms are modelled with the post type's custom fields rather than a bespoke
 * entity, which is the point of the demo: everything here is reachable from the
 * backend by an administrator, without writing PHP.
 */
class BnbFixtures extends Fixture implements FixtureGroupInterface
{
    private const string ROOM_TYPE_SLUG = 'chambre';

    /** Attached to the room type so a room can be filtered by what it offers. */
    private const array TAXONOMIES = [
        'equipement' => [
            'labels' => ['fr' => 'Équipement', 'en' => 'Amenity'],
            'terms' => [
                ['fr' => 'Wi-Fi', 'en' => 'Wi-Fi', 'slug' => 'wifi'],
                ['fr' => 'Petit-déjeuner inclus', 'en' => 'Breakfast included', 'slug' => 'petit-dejeuner'],
                ['fr' => 'Salle de bain privative', 'en' => 'Private bathroom', 'slug' => 'salle-de-bain-privative'],
                ['fr' => 'Vue sur jardin', 'en' => 'Garden view', 'slug' => 'vue-jardin'],
                ['fr' => 'Climatisation', 'en' => 'Air conditioning', 'slug' => 'climatisation'],
                ['fr' => 'Accès piscine', 'en' => 'Pool access', 'slug' => 'piscine'],
                ['fr' => 'Parking gratuit', 'en' => 'Free parking', 'slug' => 'parking'],
                ['fr' => 'Animaux acceptés', 'en' => 'Pets allowed', 'slug' => 'animaux'],
            ],
        ],
    ];

    /**
     * The shape of a room. `translatable: false` on the numbers and the toggle:
     * a price is a price in every language, and duplicating it per locale only
     * creates a way for the two to disagree.
     */
    private const array FIELDS = [
        ['name' => 'prix_nuit', 'label' => 'Prix par nuit (€)', 'type' => 'number', 'required' => true, 'translatable' => false],
        ['name' => 'capacite', 'label' => 'Capacité (personnes)', 'type' => 'number', 'required' => true, 'translatable' => false],
        ['name' => 'superficie', 'label' => 'Superficie (m²)', 'type' => 'number', 'required' => false, 'translatable' => false],
        ['name' => 'type_lit', 'label' => 'Type de lit', 'type' => 'select', 'required' => false, 'translatable' => false,
            'options' => ['choices' => [
                ['value' => 'double', 'label' => 'Lit double'],
                ['value' => 'twin', 'label' => 'Lits jumeaux'],
                ['value' => 'king', 'label' => 'Lit king size'],
                ['value' => 'familial', 'label' => 'Chambre familiale'],
            ]],
        ],
        ['name' => 'disponible', 'label' => 'Disponible à la réservation', 'type' => 'checkbox', 'required' => false, 'translatable' => false],
    ];

    /**
     * @var list<array{slug: string, fr: array{title: string, teaser: string}, en: array{title: string, teaser: string}, fields: array<string, mixed>, amenities: list<string>, media: int}>
     */
    private const array ROOMS = [
        [
            'slug' => 'chambre-glycine',
            'fr' => ['title' => 'Chambre Glycine', 'teaser' => 'Une chambre lumineuse ouvrant sur la treille, idéale pour un séjour à deux.'],
            'en' => ['title' => 'Wisteria Room', 'teaser' => 'A bright room opening onto the arbour, ideal for two.'],
            'fields' => ['prix_nuit' => '95', 'capacite' => '2', 'superficie' => '18', 'type_lit' => 'double', 'disponible' => true],
            'amenities' => ['wifi', 'petit-dejeuner', 'salle-de-bain-privative', 'vue-jardin'],
            'media' => 1,
        ],
        [
            'slug' => 'suite-oliveraie',
            'fr' => ['title' => "Suite de l'Oliveraie", 'teaser' => 'Notre plus grande suite, avec terrasse privée face aux oliviers.'],
            'en' => ['title' => 'Olive Grove Suite', 'teaser' => 'Our largest suite, with a private terrace facing the olive trees.'],
            'fields' => ['prix_nuit' => '165', 'capacite' => '4', 'superficie' => '38', 'type_lit' => 'king', 'disponible' => true],
            'amenities' => ['wifi', 'petit-dejeuner', 'salle-de-bain-privative', 'climatisation', 'piscine', 'parking'],
            'media' => 2,
        ],
        [
            'slug' => 'chambre-lavande',
            'fr' => ['title' => 'Chambre Lavande', 'teaser' => 'Calme et sobre, sous les combles, avec vue sur les collines.'],
            'en' => ['title' => 'Lavender Room', 'teaser' => 'Quiet and understated, under the eaves, looking out over the hills.'],
            'fields' => ['prix_nuit' => '85', 'capacite' => '2', 'superficie' => '15', 'type_lit' => 'twin', 'disponible' => true],
            'amenities' => ['wifi', 'petit-dejeuner', 'salle-de-bain-privative'],
            'media' => 3,
        ],
        [
            'slug' => 'chambre-familiale-figuier',
            'fr' => ['title' => 'Chambre familiale Le Figuier', 'teaser' => 'Deux espaces communicants pour voyager avec les enfants.'],
            'en' => ['title' => 'Fig Tree Family Room', 'teaser' => 'Two connecting spaces for travelling with children.'],
            'fields' => ['prix_nuit' => '140', 'capacite' => '5', 'superficie' => '32', 'type_lit' => 'familial', 'disponible' => true],
            'amenities' => ['wifi', 'petit-dejeuner', 'salle-de-bain-privative', 'parking', 'animaux'],
            'media' => 4,
        ],
        [
            'slug' => 'chambre-romarin',
            'fr' => ['title' => 'Chambre Romarin', 'teaser' => 'Notre chambre la plus abordable, au rez-de-chaussée du mas.'],
            'en' => ['title' => 'Rosemary Room', 'teaser' => 'Our most affordable room, on the ground floor of the farmhouse.'],
            'fields' => ['prix_nuit' => '75', 'capacite' => '2', 'superficie' => '14', 'type_lit' => 'double', 'disponible' => true],
            'amenities' => ['wifi', 'petit-dejeuner', 'vue-jardin'],
            'media' => 1,
        ],
        [
            'slug' => 'suite-amandier',
            'fr' => ['title' => "Suite de l'Amandier", 'teaser' => 'En rénovation jusqu\'au printemps — réouverture prévue en avril.'],
            'en' => ['title' => 'Almond Tree Suite', 'teaser' => 'Under renovation until spring — reopening in April.'],
            // Deliberately unavailable: the theme has to show what an
            // unbookable room looks like, and a demo where everything is
            // perfect never exercises that path.
            'fields' => ['prix_nuit' => '150', 'capacite' => '3', 'superficie' => '30', 'type_lit' => 'king', 'disponible' => false],
            'amenities' => ['wifi', 'salle-de-bain-privative', 'climatisation', 'piscine'],
            'media' => 2,
        ],
    ];

    public function __construct(
        private readonly PostTextExtractor $textExtractor,
    ) {}

    public static function getGroups(): array
    {
        return ['bnb'];
    }

    public function load(ObjectManager $manager): void
    {
        assert($manager instanceof EntityManagerInterface);

        $roomType = $this->roomType($manager);
        $terms = $this->taxonomies($manager, $roomType);

        foreach (self::ROOMS as $room) {
            $this->room($manager, $roomType, $terms, $room);
        }

        $manager->flush();
    }

    private function roomType(EntityManagerInterface $manager): PostType
    {
        $existing = $manager->getRepository(PostType::class)->findOneBy(['slug' => self::ROOM_TYPE_SLUG]);
        if ($existing instanceof PostType) {
            return $existing;
        }

        $roomType = new PostType()
            ->setSlug(self::ROOM_TYPE_SLUG)
            ->setLabel('Chambres')
            ->setIcon('bed-double')
            // Rooms get their own archive: it is the listing page the theme
            // builds on, and the URL a visitor lands on from the menu.
            ->setHasArchive(true)
            ->setIsBuiltIn(false)
            ->setSupports(['thumbnail']);

        $manager->persist($roomType);

        foreach (self::FIELDS as $position => $config) {
            $field = new PostTypeField()
                ->setPostType($roomType)
                ->setName($config['name'])
                ->setLabel($config['label'])
                ->setType($config['type'])
                ->setRequired($config['required'])
                ->setTranslatable($config['translatable'])
                ->setOptions($config['options'] ?? [])
                ->setPosition($position);

            $manager->persist($field);
        }

        return $roomType;
    }

    /**
     * @return array<string, TaxonomyTerm> keyed by term slug
     */
    private function taxonomies(EntityManagerInterface $manager, PostType $roomType): array
    {
        $terms = [];

        foreach (self::TAXONOMIES as $slug => $config) {
            $taxonomy = $manager->getRepository(Taxonomy::class)->findOneBy(['slug' => $slug]);

            if (!$taxonomy instanceof Taxonomy) {
                $taxonomy = new Taxonomy()->setSlug($slug)->setHierarchical(false)->setIsBuiltIn(false);
                foreach ($config['labels'] as $locale => $label) {
                    $taxonomy->translate($locale)->setLabel($label);
                }

                $manager->persist($taxonomy);
            }

            $taxonomy->getPostTypes()->add($roomType);

            foreach ($config['terms'] as $termConfig) {
                // Reused when already present: this fixture is loaded with
                // --append so it can sit next to the blog demo, which means it
                // must survive being run twice. Creating the terms blindly hit
                // the (locale, slug) unique index on the second run.
                $term = $this->existingTerm($manager, $taxonomy, $termConfig['slug']);

                if (!$term instanceof TaxonomyTerm) {
                    $term = new TaxonomyTerm()->setTaxonomy($taxonomy);
                    $term->translate('fr')->setName($termConfig['fr'])->setSlug($termConfig['slug']);
                    $term->translate('en')->setName($termConfig['en'])->setSlug($termConfig['slug']);
                    $manager->persist($term);
                }

                $terms[$termConfig['slug']] = $term;
            }
        }

        return $terms;
    }

    /**
     * Looks a term up by its slug within a taxonomy. Terms carry their slug on
     * the translation, so this goes through the translation table rather than
     * findOneBy on the term itself.
     */
    private function existingTerm(EntityManagerInterface $manager, Taxonomy $taxonomy, string $slug): ?TaxonomyTerm
    {
        foreach ($taxonomy->getTerms() as $term) {
            if ($term->translate('fr')->getSlug() === $slug) {
                return $term;
            }
        }

        return null;
    }

    /**
     * @param array<string, TaxonomyTerm>                                                                                                                                                $terms
     * @param array{slug: string, fr: array{title: string, teaser: string}, en: array{title: string, teaser: string}, fields: array<string, mixed>, amenities: list<string>, media: int} $room
     */
    private function room(EntityManagerInterface $manager, PostType $roomType, array $terms, array $room): void
    {
        // Same reasoning as the terms: loaded with --append, so a second run
        // must not produce a second set of rooms.
        if (null !== $manager->getRepository(PostTranslation::class)->findOneBy(['slug' => $room['slug'], 'locale' => 'fr'])) {
            return;
        }

        $post = new Post()->setPostType($roomType)->setStatus(PostStatusEnum::Published);
        $post->setFeaturedMedia($manager->getRepository(Document::class)->find($room['media']));

        foreach ($room['amenities'] as $amenitySlug) {
            if (isset($terms[$amenitySlug])) {
                $post->getTerms()->add($terms[$amenitySlug]);
            }
        }

        $manager->persist($post);

        foreach (['fr', 'en'] as $locale) {
            $translation = new PostTranslation()
                ->setPost($post)
                ->setLocale($locale)
                ->setTitle($room[$locale]['title'])
                ->setSlug($room['slug'])
                ->setMetaDescription($room[$locale]['teaser'])
                // No blocks: the room type declares only `thumbnail`, so the
                // editor hides the block surface for it. Writing blocks that
                // cannot be edited would be exactly the kind of data/UI
                // mismatch this demo is meant to avoid.
                // Values live on the translation, keyed by field name — the
                // shape PostTypeField declares. Non-translatable fields are
                // written to both locales so a reader in either language sees
                // the same price.
                ->setCustomFields($room['fields']);

            $translation->setSearchContent($this->textExtractor->extract($translation));
            $manager->persist($translation);
        }
    }
}
