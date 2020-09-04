<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Event;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EventFixtures extends baseFixture implements DependentFixtureInterface
{
    public function loadData()
    {
        //evenements
        $this->createMany(10,'event_event',function (){
            $event = new Event();


            return $event
                ->setName($this->faker->catchPhrase)
                ->setDescription($this->faker->realText())
                ->setPlace($this->faker->address)
                ->setDate($this->faker->dateTime())
                ->setAuthor($this->getRandomReference('user'))
                ;
        });
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }

}