<?php

namespace EventStore\StreamFeed;

trait HasLinks
{
    abstract public function getJson();

    public function getLinkUrl(LinkRelation $relation)
    {
        $json  = $this->getJson();
        $links = $json['links'];

        foreach ($links as $link) {
            if ($link['relation'] == $relation->toNative()) {
                return $link['uri'];
            }
        }

        return null;
    }
}
