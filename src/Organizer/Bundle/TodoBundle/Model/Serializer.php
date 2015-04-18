<?php

namespace Organizer\Bundle\TodoBundle\Model;

interface Serializer
{
    /**
     * @param $data
     * @param array $options
     * @return string
     */
    public function serialize($data, $options=[]);

    /**
     * @param string
     * @param array $options
     * @param string $class - The namespaced class name indicating the type of object to return
     * @return mixed
     */
    public function deserialize($data, $class, $options=[]);
} 