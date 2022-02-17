<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CountryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // php bin/console doctrine:fixtures:load --purge-with-truncate

        $csv = fopen(dirname(__FILE__).'/country.csv', 'r');

        while (!feof($csv)) {
            $line = fgetcsv($csv);

            $country = new Country();

            $country->setName($line[1]);
            $country->setCode($line[2]);
            $manager->persist($country);
        }

        $manager->flush();
    }
}
