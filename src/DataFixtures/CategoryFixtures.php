<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {

        $this->slugger = $slugger;
    }

    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    # Cette fonction load() est exécutée en ligne de commande avec : php bin/console doctrine:fixture:load --append
    # => le drapeau --append permet de ne pas purger la BDD. Sinon vous aurez (en exécutant la cl) une question pour continuer ou non.
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Politique',
            'Société',
            'Sport',
            'Cinéma',
            'Santé',
            'Mode',
            'Sciences',
            'Environnement',
        ];

        foreach ($categories as $cat) {

            $category = new Category();

            $category->setTitle($cat);
            $category->setAlias($this->slugger->slug($cat));
            $category->setCreatedAt(new \DateTimeImmutable());

            # La méthode persist() exécute les requêtes SQL en BDD.
            $manager->persist($category);
        }

        # La méthode flush() n'est pas dans la boucle foreach() pour une raison :
        # => cette méthode "vide" l'objet $manager (c'est un container).
        $manager->flush();
    }
}
