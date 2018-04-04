<?php

namespace App\Entity;

class EntityMerger 
{
    /**
     * @param $entity
     * @param @changes
     */
    public function merge($entity, $changes): void
    {
        $entityClassName = get_class($entity);
    }
}