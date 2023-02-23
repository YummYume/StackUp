<?php

namespace App\DataFixtures;

use App\Entity\Request;
use App\Entity\Tech;
use App\Entity\TechPicture;
use App\Entity\Vote;
use App\Enum\RequestStatusEnum;
use App\Enum\TechTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class TechFixtures extends Fixture implements DependentFixtureInterface
{
    public const JAVASCRIPT = 'javascript';
    public const PHP = 'php';
    public const PYTHON = 'python';
    public const CSS = 'css';
    public const SVELTE = 'svelte';
    public const SVELTEKIT = 'sveltekit';
    public const STIMULUS = 'stimulus';
    public const TURBO = 'turbo';
    public const FIGMA = 'figma';
    public const REACT = 'react';
    public const LARAVEL = 'laravel';
    public const POSTGRESQL = 'postgresql';
    public const TAILWIND_CSS = 'tailwind_css';
    public const GIT = 'git';
    public const VUE_JS = 'vue_js';
    public const BOOTSTRAP = 'bootstrap';
    public const NEXT_JS = 'next_js';
    public const WEBPACK = 'webpack';
    public const EXPRESS_JS = 'express_js';
    public const DOCKER = 'docker';
    public const AWS_LAMBDA = 'aws_lambda';
    public const JQUERY = 'jquery';
    public const REDUX = 'redux';
    public const GRAPHQL = 'graphql';
    public const JEST = 'jest';
    public const CYPRESS = 'cypress';
    public const MONGODB = 'mongodb';
    public const MYSQL = 'mysql';
    public const REDIS = 'redis';
    public const NGINX = 'nginx';
    public const DJANGO = 'django';
    public const FLASK = 'flask';
    public const SYMFONY = 'symfony';
    public const DOCTRINE = 'doctrine';
    public const ANGULAR = 'angular';
    public const DAISYUI = 'daisyui';
    public const SKELETON = 'skeleton';

    public const FIXTURE_ITEMS = [
        self::JAVASCRIPT => [
            'name' => 'JavaScript',
            'picture' => __DIR__.'/tmp/tech/javascript.png',
            'type' => TechTypeEnum::Language,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
        self::PHP => [
            'name' => 'PHP',
            'picture' => __DIR__.'/tmp/tech/php.png',
            'type' => TechTypeEnum::Language,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::BACK,
            ],
        ],
        self::PYTHON => [
            'name' => 'Python',
            'picture' => __DIR__.'/tmp/tech/python.png',
            'type' => TechTypeEnum::Language,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::UTILITY,
            ],
        ],
        self::CSS => [
            'name' => 'CSS',
            'picture' => __DIR__.'/tmp/tech/css.png',
            'type' => TechTypeEnum::Language,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::UTILITY,
            ],
        ],
        self::SVELTE => [
            'name' => 'Svelte',
            'picture' => __DIR__.'/tmp/tech/svelte.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Accepted,
            'links' => [
                Tech::LINK_NPM_OR_YARN => 'https://yarnpkg.com/package/svelte',
                Tech::LINK_OTHER => 'https://svelte.dev/',
            ],
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::JS_LIBRARY,
                CategoryFixtures::FRAMEWORK,
            ],
        ],
        self::SVELTEKIT => [
            'name' => 'SvelteKit',
            'picture' => __DIR__.'/tmp/tech/sveltekit.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::SVELTE,
            'status' => RequestStatusEnum::Accepted,
            'links' => [
                Tech::LINK_NPM_OR_YARN => 'https://yarnpkg.com/package/@sveltejs/kit',
                Tech::LINK_OTHER => 'https://kit.svelte.dev/',
            ],
            'categories' => [
                CategoryFixtures::FULLSTACK,
                CategoryFixtures::JS_LIBRARY,
                CategoryFixtures::FRAMEWORK,
            ],
        ],
        self::STIMULUS => [
            'name' => 'Stimulus',
            'picture' => __DIR__.'/tmp/tech/stimulus.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
        self::TURBO => [
            'name' => 'Turbo',
            'picture' => __DIR__.'/tmp/tech/turbo.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
        self::FIGMA => [
            'name' => 'Figma',
            'picture' => __DIR__.'/tmp/tech/figma.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::DESIGN,
            ],
        ],
        self::REACT => [
            'name' => 'React',
            'picture' => __DIR__.'/tmp/tech/react.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::FRONT,
            ],
        ],
        self::LARAVEL => [
            'name' => 'Laravel',
            'picture' => __DIR__.'/tmp/tech/laravel.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PHP,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::BACK,
            ],
        ],
        self::POSTGRESQL => [
            'name' => 'PostgreSQL',
            'picture' => __DIR__.'/tmp/tech/postgresql.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::BACK,
                CategoryFixtures::DB,
            ],
        ],
        self::TAILWIND_CSS => [
            'name' => 'Tailwind CSS',
            'picture' => __DIR__.'/tmp/tech/tailwind_css.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::CSS,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::UI_FRAMEWORK,
            ],
        ],
        self::GIT => [
            'name' => 'Git',
            'picture' => __DIR__.'/tmp/tech/git.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::MISCELLANEOUS,
                CategoryFixtures::UTILITY,
            ],
        ],
        self::VUE_JS => [
            'name' => 'Vue.js',
            'picture' => __DIR__.'/tmp/tech/vuejs.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
        self::BOOTSTRAP => [
            'name' => 'Bootstrap',
            'picture' => __DIR__.'/tmp/tech/bootstrap.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::CSS,
            'status' => RequestStatusEnum::Rejected,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::UI_FRAMEWORK,
            ],
        ],
        self::NEXT_JS => [
            'name' => 'Next.js',
            'picture' => __DIR__.'/tmp/tech/next_js.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::REACT,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::FULLSTACK,
            ],
        ],
        self::WEBPACK => [
            'name' => 'Webpack',
            'picture' => __DIR__.'/tmp/tech/webpack.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::BUNDLER,
            ],
        ],
        self::EXPRESS_JS => [
            'name' => 'Express.js',
            'picture' => __DIR__.'/tmp/tech/express_js.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::BACK,
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
        self::DOCKER => [
            'name' => 'Docker',
            'picture' => __DIR__.'/tmp/tech/docker.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::UTILITY,
            ],
        ],
        self::AWS_LAMBDA => [
            'name' => 'AWS Lambda',
            'picture' => __DIR__.'/tmp/tech/aws_lambda.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::SERVER,
                CategoryFixtures::UTILITY,
            ],
        ],
        self::JQUERY => [
            'name' => 'jQuery',
            'picture' => __DIR__.'/tmp/tech/jquery.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Rejected,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
        self::REDUX => [
            'name' => 'Redux',
            'picture' => __DIR__.'/tmp/tech/redux.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Rejected,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
        self::GRAPHQL => [
            'name' => 'GraphQL',
            'picture' => __DIR__.'/tmp/tech/graphql.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::API,
            ],
        ],
        self::JEST => [
            'name' => 'Jest',
            'picture' => __DIR__.'/tmp/tech/jest.png',
            'type' => TechTypeEnum::Tool,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::JS_LIBRARY,
                CategoryFixtures::UTILITY,
                CategoryFixtures::TESTING,
            ],
        ],
        self::CYPRESS => [
            'name' => 'Cypress',
            'picture' => __DIR__.'/tmp/tech/cypress.png',
            'type' => TechTypeEnum::Tool,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::JS_LIBRARY,
                CategoryFixtures::UTILITY,
                CategoryFixtures::TESTING,
            ],
        ],
        self::MONGODB => [
            'name' => 'MongoDB',
            'picture' => __DIR__.'/tmp/tech/mongodb.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::DB,
            ],
        ],
        self::MYSQL => [
            'name' => 'MySQL',
            'picture' => __DIR__.'/tmp/tech/mysql.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::DB,
            ],
        ],
        self::REDIS => [
            'name' => 'Redis',
            'picture' => __DIR__.'/tmp/tech/redis.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::SERVER,
            ],
        ],
        self::NGINX => [
            'name' => 'Nginx',
            'picture' => __DIR__.'/tmp/tech/nginx.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::SERVER,
            ],
        ],
        self::DJANGO => [
            'name' => 'Django',
            'picture' => __DIR__.'/tmp/tech/django.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PYTHON,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::FULLSTACK,
            ],
        ],
        self::FLASK => [
            'name' => 'Flask',
            'picture' => __DIR__.'/tmp/tech/flask.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PYTHON,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::BACK,
            ],
        ],
        self::SYMFONY => [
            'name' => 'Symfony',
            'picture' => __DIR__.'/tmp/tech/symfony.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PHP,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::BACK,
            ],
        ],
        self::DOCTRINE => [
            'name' => 'Doctrine',
            'picture' => __DIR__.'/tmp/tech/doctrine.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PHP,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::ORM,
            ],
        ],
        self::ANGULAR => [
            'name' => 'Angular',
            'picture' => __DIR__.'/tmp/tech/angular.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
        self::DAISYUI => [
            'name' => 'DaisyUI',
            'picture' => __DIR__.'/tmp/tech/daisyui.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::TAILWIND_CSS,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::FRONT,
            ],
        ],
        self::SKELETON => [
            'name' => 'Skeleton',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::TAILWIND_CSS,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
    ];

    public const REFERENCE_IDENTIFIER = 'tech_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        foreach (self::FIXTURE_ITEMS as $key => $item) {
            $dependsOn = $item['depends_on'] ?? null;
            $links = $item['links'] ?? [];
            $categories = $item['categories'] ?? [];
            $status = $item['status'];
            $picture = $item['picture'] ?? 'null';
            $picturePath = explode('/', $picture);
            $voteCount = match ($status) {
                RequestStatusEnum::Accepted => $faker->numberBetween(200, 299),
                RequestStatusEnum::Pending => $faker->numberBetween(1, 199),
                RequestStatusEnum::Rejected => 1,
                default => 1,
            };

            $request = (new Request())
                ->setStatus($item['status'])
                ->setCreated(true)
            ;
            $techPicture = (new TechPicture())
                ->setFile(file_exists($picture) ? new UploadedFile(
                    $picture,
                    $picturePath[array_key_last($picturePath)],
                    test: true
                ) : null)
            ;
            $tech = (new Tech())
                ->setRequest($request)
                ->setName($item['name'])
                ->setDescription($faker->sentences(2, true))
                ->setLinks($links)
                ->setDependsOn($dependsOn ? $this->getReference(self::REFERENCE_IDENTIFIER.$dependsOn) : null)
                ->setType($item['type'])
                ->setPicture($techPicture ?: null)
            ;

            foreach ($categories as $category) {
                $tech->addCategory($this->getReference(CategoryFixtures::REFERENCE_IDENTIFIER.$category));
            }

            $manager->persist($tech);
            $manager->flush();
            $this->addReference(self::REFERENCE_IDENTIFIER.$key, $tech);

            foreach (range(1, $voteCount) as $i => $userId) {
                $vote = (new Vote())
                    ->setUpvote(RequestStatusEnum::Accepted === $status ? $faker->boolean(90) : $faker->boolean(50))
                    ->setProfile($this->getReference(UserFixtures::REFERENCE_IDENTIFIER.$userId)->getProfile())
                    ->setRequest($request)
                ;

                $manager->persist($vote);
            }

            $manager->flush();
            $manager->clear();
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PreparePicturesFixtures::class,
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
