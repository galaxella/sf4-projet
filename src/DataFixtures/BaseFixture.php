<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Class "modèle" pour les fixtures
 * On ne peut pas instancier un abstraction
 */
abstract class BaseFixture extends Fixture
{
    /** @var ObjectManager */
    private $manager;
    /** @var Generator */
    protected $faker;
    /** @var array  liste des réferences connues (cf.memoîsation*/
    private $references = [];

    /**
     * Méthode à implémenter par les classes qui héritent
     * pour générer les fausses données
     */
    abstract protected function loadData();

    /**
     * Méthode appelée par le système de fixtures
     */
    public function load(ObjectManager $manager)
    {
        // On enregistre le ObjectManager
        $this->manager = $manager;

        // On instancie Faker
        $this->faker = Factory::create('fr_FR');

        // On appelle loadData pour avoir les fausses données
        $this->loadData();

        // On exécute l'enregistrement en base
        $this->manager->flush();
    }
    /**
     * Enregistrer plusieurs entités
     * @param int $count    nombre d'ntités à générer
     * @param  string $groupName nom du groupe de références
     * @param callable $factory fonction qui génère 1 entité
     */
    protected function createMany(int $count, string  $groupName, callable  $factory)
    {

        for($i = 0; $i < $count; $i++){
            // On exécute $factory qui doit retourner l'entité généré
            $entity = $factory($i);


            // Vérifier que l'entité ait été retournée
            if($entity === null ){
                throw new \LogicException('L\' entité doit être retournée. Auriez-vous oublié un "return" ');
            }
            // On prépare à l'enregistrement de l'entité
            $this->manager->persist($entity);

            //Enregistre un référence à l'entité
            $reference = sprintf('%s_%d', $groupName, $i); /* sprintf() pour formater une chaîne de caractère*/
            $this->addReference($reference, $entity);
        }
    } /* fermeture de many */
    /**
     * Récupérer 1 entité par son nom de groupe de références
     * @param string $groupName nom de groupe de référence
     */
    protected function getRandomReference(string $groupName)
    {
        //Vérifier si on a déjà enregistré les références du groupe demandé
        if(!isset($this->references[$groupName])){
            //si non, on va rechercher les références
            $this->references[$groupName] = [];

            // On parcourt la liste de toutes les références (toutes classes confondues)
            foreach ($this->referenceRepository->getReferences() as $key => $ref ){
                // La clé $key correspond à nos références
                // Si $key commence par $groupName, on le sauvegarde
                if(strpos($key, $groupName) === 0 ) {
                    $this->references[$groupName][] = $key;
                }
            }

        }

        // Vérifier que l'on a récupéré des références
        if($this->references[$groupName] === []){
            throw new \Exception(sprintf('Aucune référence trouvée pour le groupe "%s"', $groupName));
        }

        // Retourner une entité correspondant à une référence aléatoire
        $randomReference = $this->faker->randomElement($this->references[$groupName]);
        return $this->getReference($randomReference);

    }

}



