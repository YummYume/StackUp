<?php

namespace App\DataFixtures;

use App\Entity\Stack;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class StackFixtures extends Fixture implements DependentFixtureInterface
{
    public const FIXTURE_RANGE = 100;

    public const REFERENCE_IDENTIFIER = 'stack_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $stackUp = (new Stack())
            ->setName('StackUp')
            ->setDescription('The greatest stack to ever exist.')
            ->setProfile($this->getReference(UserFixtures::REFERENCE_IDENTIFIER.'root')->getProfile())
        ;

        foreach ([
            TechFixtures::SYMFONY,
            TechFixtures::PHP,
            TechFixtures::STIMULUS,
            TechFixtures::TURBO,
            TechFixtures::GIT,
            TechFixtures::TAILWIND_CSS,
            TechFixtures::DAISYUI,
            TechFixtures::JAVASCRIPT,
            TechFixtures::NGINX,
            TechFixtures::DOCKER,
            TechFixtures::DOCTRINE,
            TechFixtures::MYSQL,
            TechFixtures::FIGMA,
        ] as $tech) {
            $stackUp->addTech($this->getReference(TechFixtures::REFERENCE_IDENTIFIER.$tech));
        }

        $manager->persist($stackUp);
        $this->addReference(self::REFERENCE_IDENTIFIER.'stack_up', $stackUp);

        foreach (range(1, self::FIXTURE_RANGE) as $i) {
            $stack = (new Stack())
                ->setName(ucfirst($faker->unique()->word()))
                ->setDescription($faker->sentences(random_int(3, 8), true))
                ->setProfile($this->getReference(UserFixtures::REFERENCE_IDENTIFIER.$i)->getProfile())
            ;
            $techs = $faker->shuffle(array_keys(TechFixtures::FIXTURE_ITEMS));

            foreach (\array_slice($techs, 0, random_int(2, \count($techs))) as $tech) {
                $stack->addTech($this->getReference(TechFixtures::REFERENCE_IDENTIFIER.$tech));
            }

            $manager->persist($stack);
            $this->addReference(self::REFERENCE_IDENTIFIER.$i, $stack);

            if (($i % 25) === 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TechFixtures::class,
        ];
    }
}
