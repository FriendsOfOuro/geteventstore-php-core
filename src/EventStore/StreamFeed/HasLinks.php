<?php

namespace EventStore\StreamFeed;

trait HasLinks
{
    abstract protected function getLinks();

    public function getLinkUrl(LinkRelation $relation)
    {
        $links = $this->getLinks();

        foreach ($links as $link) {
            if ($link['relation'] == $relation->toNative()) {
                return $link['uri'];
            }
        }

        return null;
    }
}
