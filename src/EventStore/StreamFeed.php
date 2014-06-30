<?php

namespace EventStore;

final class StreamFeed
{
    private $json;
    private $eventEmbedMode;

    public function __construct(array $json_feed, EventEmbedMode $embed_mode = null)
    {
        if ($embed_mode === null) {
            $embed_mode = EventEmbedMode::NONE();
        }

        $this->eventEmbedMode = $embed_mode;
        $this->json           = $json_feed;
    }

    public function getEventEmbedMode()
    {
        return $this->eventEmbedMode;
    }

    public function getJson()
    {
        return $this->json;
    }

    public function getLinkUrl(StreamFeedLinkRelation $relation)
    {
        $links = $this->json['links'];

        foreach ($links as $link) {
            if ($link['relation'] == $relation->toNative()) {
                return $link['uri'];
            }
        }

        return null;
    }
}
