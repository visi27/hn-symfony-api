<?php

namespace AppBundle\Serializer;


use Hateoas\Serializer\JsonHalSerializer;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;

class CustomHATEOASJsonSerializer extends JsonHalSerializer
{
    /**
     * {@inheritdoc}
     */
    public function serializeLinks(array $links, JsonSerializationVisitor $visitor, SerializationContext $context)
    {
        $serializedLinks = array();
        foreach ($links as $link) {
            $serializedLinks[$link->getRel()] = $link->getHref();
        }

        $visitor->setData('_links', $serializedLinks);
    }
}