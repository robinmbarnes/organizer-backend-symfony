<?php

namespace Organizer\Bundle\TodoBundle\Service;

use JMS\Serializer\SerializationContext;
use Organizer\Bundle\TodoBundle\Model\Serializer;
use JMS\Serializer\Serializer as JmsSerializer;

/**
 * Class Serializer
 * @package Organizer\Bundle\TodoBundle\Service
 *
 * Serializes entities to Json
 */
class JsonSerializer implements Serializer
{
    /**
     * @var JmsSerializer $jmsSerializer
     */
    private $jmsSerializer;

    public function __construct(JmsSerializer $jmsSerializer)
    {
        $this->jmsSerializer = $jmsSerializer;
    }

    /**
     * @see Serializer::serialize
     */
    public function serialize($data, $options=[])
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return $this->jmsSerializer->serialize($data, 'json', $context);
    }

    /**
     * @see Serializer::deserialize
     */
    public function deserialize($data, $class, $options=[])
    {
        return $this->jmsSerializer->deserialize($data, $class, 'json');
    }
} 