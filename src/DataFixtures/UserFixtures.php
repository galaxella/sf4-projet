<?php

namespace App\DataFixtures;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use APP\Entity\User;

class UserFixtures extends baseFixture
{
    private  $encoder;

    /**
     * Dans une class (autre que un controller),
     * on peut récupérer des services par autowiring uniquement dans
     * un constructeur
     */
    public function  __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    protected function loadData()
    {
        //utilisateurs
        $this->createMany(5,'user_user',function (int $num){
            $user = new User();
            $password = $this->encoder->encodePassword($user,'admin' . $num);

            return $user
                ->setEmail('user' . $num . '@kikiriki.fr')
                ->setPassword($password)
                ->setName('NOM' . $num)
                ->setLastname('Prenom')
                ;
        });

    }
}