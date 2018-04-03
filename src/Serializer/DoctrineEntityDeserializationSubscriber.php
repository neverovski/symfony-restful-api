<?php

namespace App\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

class DoctrineEntityDeserializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var AnnotationReader
     */
    public $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
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

            $annotation = $this->annotationReader->getPropertyAnnotation(
                $property,
                DeserializeEntity::class
            );

            if (null === $annotation || !class_exists($annotation->type)) {
                continue;
            }
            
            $data[$property->name] = [
                $annotation->idFiled => $data[$property->name]
            ];
        }

        $event->setData($data);
    }

    public function onPostDeserialize(ObjectEvent $event)
    {

    }
}