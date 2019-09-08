<?php

namespace App\DataFixtures;

use App\Entity\Common\Quotes;
use Doctrine\Common\Persistence\ObjectManager;

class QuoteFixtures extends BaseFixture
{
    /**
     * @param ObjectManager $manager
     */
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_quotes', function ($i) {
            $quote = new Quotes();
            $quote->setQuote($this->faker->sentence);
            $quote->setDetails($this->faker->lastName . ', '. $this->faker->century);

            return $quote;
        });

        $manager->flush();
    }
}
