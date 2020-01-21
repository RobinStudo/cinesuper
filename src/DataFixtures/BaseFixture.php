<?php

namespace App\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class BaseFixture
 * @package App\DataFixtures
 */
abstract class BaseFixture extends Fixture
{
    /**
     * @var array
     */
    private $referencesIndex = [];

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param ObjectManager $manager
     * @return mixed
     */
    abstract protected function loadData(ObjectManager $manager);

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->loadData($manager);
    }

    /**
     * @param string $className
     * @param int $count
     * @param callable $factory
     */
    protected function createMany(string $className, int $count, callable $factory)
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);

            $this->manager->persist($entity);
            $this->addReference($className . '_' . $i, $entity);
        }
    }

    /**
     * @param string $className
     * @return object
     * @throws \Exception
     */
    protected function getRandomReference(string $className)
    {
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];

            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                if (strpos($key, $className.'_') === 0) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }

        if (empty($this->referencesIndex[$className]))
        {
            throw new \Exception(sprintf('Cannot find any references for class "%s"', $className));
        }

        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$className]);

        return $this->getReference($randomReferenceKey);
    }
}
