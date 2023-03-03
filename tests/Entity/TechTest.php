<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Request;
use App\Entity\Tech;
use App\Enum\TechTypeEnum;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TechTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public static function techProvider(): array
    {
        return [
            'Valid' => [0, (new Tech())
                ->setName('Tech')
                ->setDescription('Tech desc')
                ->addCategory(new Category())
                ->setRequest(new Request()),
            ],
            'Invalid dependsOn (null but is required for type Library)' => [1, (new Tech())
                ->setName('Tech')
                ->setDescription('Tech desc')
                ->setType(TechTypeEnum::Library)
                ->addCategory(new Category())
                ->setRequest(new Request()),
            ],
            'Invalid GitHub URL' => [1, (new Tech())
                ->setName('Tech')
                ->setDescription('Tech desc')
                ->setLinks([Tech::LINK_GITHUB => 'https://not-github.com'])
                ->addCategory(new Category())
                ->setRequest(new Request()),
            ],
        ];
    }

    /**
     * @dataProvider techProvider
     */
    public function testTechs(int $numErrors, Tech $tech): void
    {
        $errors = $this->validator->validate($tech);
        $count = \count($errors);

        $this->assertEquals(
            $numErrors,
            $count,
            sprintf(
                'Expected %s error(s) but got %s error(s) instead. Errors : %s',
                $numErrors,
                $count,
                \PHP_EOL.implode(\PHP_EOL, array_map(static fn (ConstraintViolation $error): string => $error->getMessage(), [...$errors]))
            )
        );
    }
}
