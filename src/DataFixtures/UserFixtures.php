<?php

namespace App\DataFixtures;

use App\Entity\Profile;
use App\Entity\ProfilePicture;
use App\Entity\User;
use App\Enum\UserRoleEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const FIXTURE_RANGE = 300;

    public const REFERENCE_IDENTIFIER = 'user_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $adminProfile = (new Profile())
            ->setUsername('Root')
        ;
        $admin = (new User())
            ->setEmail('root@root.com')
            ->setPlainPassword('root')
            ->setVerified(true)
            ->setRoles([UserRoleEnum::SuperAdmin->value, UserRoleEnum::AllowedToSwitch->value])
            ->setProfile($adminProfile)
        ;

        $manager->persist($admin);
        $this->addReference(self::REFERENCE_IDENTIFIER.'root', $admin);

        $moderatorPicture = (new ProfilePicture())
            ->setFile(new UploadedFile(
                __DIR__.'/tmp/profile/moderator.jpg',
                'moderator.jpg',
                test: true
            ))
        ;
        $moderatorProfile = (new Profile())
            ->setUsername('Moderator')
            ->setDescription('<script>alert("hacked!!!!");</script>')
            ->setPicture($moderatorPicture)
        ;
        $moderator = (new User())
            ->setEmail('moderator@moderator.com')
            ->setPlainPassword('moderator')
            ->setVerified(true)
            ->setRoles([UserRoleEnum::Moderator->value])
            ->setProfile($moderatorProfile)
        ;

        $manager->persist($moderator);
        $this->addReference(self::REFERENCE_IDENTIFIER.'moderator', $admin);

        foreach (range(1, self::FIXTURE_RANGE) as $i) {
            $profile = (new Profile())
                ->setUsername(str_replace('.', ' ', $faker->unique()->userName()))
                ->setDescription($faker->paragraph(5))
            ;
            $user = (new User())
                ->setEmail($faker->unique()->safeEmail())
                ->setPlainPassword($faker->password(8))
                ->setVerified(true)
                ->setProfile($profile)
            ;

            $manager->persist($user);
            $this->addReference(self::REFERENCE_IDENTIFIER.$i, $user);

            if (($i % 50) === 0) {
                $manager->flush();
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PreparePicturesFixtures::class,
        ];
    }
}
