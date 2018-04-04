<?php

namespace App\Serializer;

use App\Annotation\DeserializeEntity;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DoctrineEntityDeserializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ManagerRegistry
     */
    private $doctrineRegistry;

    public function __construct(Reader $reader, ManagerRegistry $doctrineRegistry)
    {
        $this->reader = $reader;
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'format' => 'json'
            ],
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
                'format' => 'json'
            ]
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $deserializeType = $event->getType()['name'];
        if(!class_exists($deserializeType)) {
            return;
        }
        $data = $event ->getData();
        $class = new \ReflectionClass($deserializeType);

        foreach ($class->getProperties() as $property) {
            if (!isset($data[$property->name])) {
                continue;
            }

            $annotation = $this->reader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );
            if (null === $annotation || !class_exists($annotation->type)) {
                continue;
            }
            
            $data[$property->name] = [
                $annotation->idField => $data[$property->name]
            ];
        }
        $event->setData($data);
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        $deserializeType = $event->getType()['name'];

        if(!class_exists($deserializeType)) {
            return;
        }
        $object = $event->getObject();
        $reflection = new \ReflectionObject($object);

        foreach ($reflection->getProperties() as $property) {

            $annotation = $this->reader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );
            if (null === $annotation || !class_exists($annotation->type)) {
                continue;
            }

            if(!$reflection->hasMethod($annotation->setter)) {
                throw new \LogicException(
                    "Object {$reflection->getName()} does not have the {$annotation->setter} method" 
                );
            }

            $property->setAccessible(true);
            $deserializedEntity = $property->getValue($object);
            
            if (null === $deserializedEntity) {
                return;
            }

            $entityId = $deserializedEntity->{$annotation->idGetter}();
            $repository = $this->doctrineRegistry->getRepository($annotation->type);

            $entity = $repository->find($entityId);
            if (null === $entity) {
                throw new NotFoundHttpException(
                    "Resource {$reflection->getShortName()}/$entityId"
                );
            }

            $object->{$annotation->setter}($entity);
        }
    }
}