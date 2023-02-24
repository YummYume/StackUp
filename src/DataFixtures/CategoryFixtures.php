<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class CategoryFixtures extends Fixture
{
    public const FRONT = 'front';
    public const BACK = 'back';
    public const ORM = 'orm';
    public const UTILITY = 'utility';
    public const DESIGN = 'design';
    public const FULLSTACK = 'fullstack';
    public const FRAMEWORK = 'framework';
    public const UI_FRAMEWORK = 'ui_framework';
    public const SERVER = 'server';
    public const CDN = 'cdn';
    public const SECURITY = 'security';
    public const MISCELLANEOUS = 'miscellaneous';
    public const BUNDLER = 'bundler';
    public const JS_LIBRARY = 'js_library';
    public const DB = 'db';
    public const API = 'api';
    public const TESTING = 'testing';

    public const FIXTURE_ITEMS = [
        self::FRONT => 'Front',
        self::BACK => 'Back',
        self::ORM => 'ORM',
        self::UTILITY => 'Utility',
        self::DESIGN => 'Design',
        self::FULLSTACK => 'Fullstack',
        self::FRAMEWORK => 'Framework',
        self::UI_FRAMEWORK => 'UI Framework',
        self::SERVER => 'Server',
        self::CDN => 'CDN',
        self::SECURITY => 'Security',
        self::MISCELLANEOUS => 'Miscellaneous',
        self::BUNDLER => 'Bundler',
        self::JS_LIBRARY => 'JS Library',
        self::DB => 'Database',
        self::API => 'API',
        self::TESTING => 'Testing',
    ];

    public const REFERENCE_IDENTIFIER = 'category_';

    public function load(ObjectManager $manager): void
    {
        foreach (self::FIXTURE_ITEMS as $key => $name) {
            $category = (new Category())
                ->setName($name)
            ;

            $manager->persist($category);
            $this->addReference(self::REFERENCE_IDENTIFIER.$key, $category);
        }

        $manager->flush();
    }
}
