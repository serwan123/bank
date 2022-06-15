<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Account;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $accounts=[
            ['accountNumber' => '34238498439000890034645374', 'active' => true],
            ['accountNumber' => '57238238439000890034346226', 'active' => true],
            ['accountNumber' => '27275495439000890023433463', 'active' => false],
        ];
        
        foreach ($accounts as $data) {
            $account = new Account();
            $account->setNumber($data['accountNumber']);
            $account->setActive($data['active']);
            $account->setCreationDate(new \DateTime());
            $manager->persist($account);
        }

        $manager->flush();
    }
}
