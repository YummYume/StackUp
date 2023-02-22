<?php

namespace App\DataFixtures;

use App\Entity\Tech;
use App\Enum\RequestStatusEnum;
use App\Enum\TechTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class TechFixtures extends Fixture
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

    public const FIXTURE_ITEMS = [
        self::JAVASCRIPT => [
            'name' => 'JavaScript',
            'image' => __DIR__.'/tmp/tech/javascript.png',
            'type' => TechTypeEnum::Language,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
        self::PHP => [
            'name' => 'PHP',
            'image' => __DIR__.'/tmp/tech/php.png',
            'type' => TechTypeEnum::Language,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::BACK,
            ],
        ],
        self::PYTHON => [
            'name' => 'Python',
            'image' => __DIR__.'/tmp/tech/python.png',
            'type' => TechTypeEnum::Language,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::UTILITY,
            ],
        ],
        self::CSS => [
            'name' => 'CSS',
            'image' => __DIR__.'/tmp/tech/css.png',
            'type' => TechTypeEnum::Language,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::UTILITY,
            ],
        ],
        self::SVELTE => [
            'name' => 'Svelte',
            'image' => __DIR__.'/tmp/tech/svelte.png',
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
            'image' => __DIR__.'/tmp/tech/sveltekit.png',
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
            'image' => __DIR__.'/tmp/tech/stimulus.png',
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
            'image' => __DIR__.'/tmp/tech/turbo.png',
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
            'image' => __DIR__.'/tmp/tech/figma.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::DESIGN,
            ],
        ],
        self::REACT => [
            'name' => 'React',
            'image' => __DIR__.'/tmp/tech/react.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::FRONT,
            ],
        ],
        self::LARAVEL => [
            'name' => 'Laravel',
            'image' => __DIR__.'/tmp/tech/laravel.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PHP,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::BACK,
            ],
        ],
        self::POSTGRESQL => [
            'name' => 'PostgreSQL',
            'image' => __DIR__.'/tmp/tech/postgresql.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::BACK,
                CategoryFixtures::DB,
            ],
        ],
        self::TAILWIND_CSS => [
            'name' => 'Tailwind CSS',
            'image' => __DIR__.'/tmp/tech/tailwind.png',
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
            'image' => __DIR__.'/tmp/tech/git.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::MISCELLANEOUS,
                CategoryFixtures::UTILITY,
            ],
        ],
        self::VUE_JS => [
            'name' => 'Vue.js',
            'image' => __DIR__.'/tmp/tech/vue.png',
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
            'image' => __DIR__.'/tmp/tech/bootstrap.png',
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
            'image' => __DIR__.'/tmp/tech/nextjs.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::REACT,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::FULLSTACK,
            ],
        ],
        self::WEBPACK => [
            'name' => 'Webpack',
            'image' => __DIR__.'/tmp/tech/webpack.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::BUNDLER,
            ],
        ],
        self::EXPRESS_JS => [
            'name' => 'Express.js',
            'image' => __DIR__.'/tmp/tech/express.png',
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
            'image' => __DIR__.'/tmp/tech/docker.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::UTILITY,
            ],
        ],
        self::AWS_LAMBDA => [
            'name' => 'AWS Lambda',
            'image' => __DIR__.'/tmp/tech/aws-lambda.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::SERVER,
                CategoryFixtures::UTILITY,
            ],
        ],
        self::JQUERY => [
            'name' => 'jQuery',
            'image' => __DIR__.'/tmp/tech/jquery.png',
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
            'image' => __DIR__.'/tmp/tech/redux.png',
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
            'image' => __DIR__.'/tmp/tech/graphql.png',
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
            'image' => __DIR__.'/tmp/tech/jest.png',
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
            'image' => __DIR__.'/tmp/tech/cypress.png',
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
            'image' => __DIR__.'/tmp/tech/mongodb.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::DB,
            ],
        ],
        self::MYSQL => [
            'name' => 'MySQL',
            'image' => __DIR__.'/tmp/tech/mysql.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::DB,
            ],
        ],
        self::REDIS => [
            'name' => 'Redis',
            'image' => __DIR__.'/tmp/tech/redis.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::SERVER,
            ],
        ],
        self::NGINX => [
            'name' => 'Nginx',
            'image' => __DIR__.'/tmp/tech/nginx.png',
            'type' => TechTypeEnum::Tool,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::SERVER,
            ],
        ],
        self::DJANGO => [
            'name' => 'Django',
            'image' => __DIR__.'/tmp/tech/django.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PYTHON,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::FULLSTACK,
            ],
        ],
        self::FLASK => [
            'name' => 'Flask',
            'image' => __DIR__.'/tmp/tech/flask.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PYTHON,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::BACK,
            ],
        ],
        self::SYMFONY => [
            'name' => 'Symfony',
            'image' => __DIR__.'/tmp/tech/symfony.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PHP,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::BACK,
            ],
        ],
        self::DOCTRINE => [
            'name' => 'Doctrine',
            'image' => __DIR__.'/tmp/tech/doctrine.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::PHP,
            'status' => RequestStatusEnum::Accepted,
            'categories' => [
                CategoryFixtures::ORM,
            ],
        ],
        self::ANGULAR => [
            'name' => 'Angular',
            'image' => __DIR__.'/tmp/tech/angular.png',
            'type' => TechTypeEnum::Library,
            'depends_on' => self::JAVASCRIPT,
            'status' => RequestStatusEnum::Pending,
            'categories' => [
                CategoryFixtures::FRONT,
                CategoryFixtures::JS_LIBRARY,
            ],
        ],
    ];

    public const REFERENCE_IDENTIFIER = 'tech_';

    public function load(ObjectManager $manager): void
    {
        foreach (self::FIXTURE_ITEMS as $item) {
            $tech = (new Tech())

            ;
        }

        $manager->flush();
    }
}
