<?php

namespace App\Entity;

use Doctrine\Common\Annotations\Reader;

class EntityMerger 
{
     /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param $entity
     * @param @changes
     */
    public function merge($entity, $changes): void
    {
        // Get $entity class name or false if it's not a class
        $entityClassName = get_class($entity);
        
        if (false === $entityClassName) {
            throw new \InvalidArgumentException('$entity is not a class');
        }

        // Get $changes class name or false if it's not a class
        $changesClassName = get_class($changes);

        if (false === $changesClassName) {
            throw new \InvalidArgumentException('$changes is not a class');
        }

        // If $changes object is of the same class as $entity
        if (!is_a($changes, $entityClassName)) {
            throw new \InvalidArgumentException('Cannot merge object of class $changesClassName with object of class $entityClassName');
        }

        $entityReflection = new \ReflectionObject($entity);
        $changesReflection = new \ReflectionObject($changes);

        foreach ($changesReflection->getProperties() as $changedProperty) {
            $changedProperty->setAccessible(true);
            $changedPropertyValue = $changedProperty->getValue($changes);

            // Ignore $changes property with null value
            if (null === $changedPropertyValue) {
                continue;
            }

            // Ignore $changes property if it's not present on $entity 
            if (!$entityReflection->hasProperty($changedProperty->getName())) {
                continue;
            }

            $entityProperty = $entityReflection->getProperty($changedProperty->getName());
            $annotation = $this->reader->getPropertyAnnotation($entityProperty, Id::class);

            // Ignore $changes property that has Doctrine @Id annotation
            if (null !== $annotation) {
                continue;
            }

            $entityProperty->setAccessible(true);
            $entityProperty->setValue($entity, $changedPropertyValue);
        }
    }
}