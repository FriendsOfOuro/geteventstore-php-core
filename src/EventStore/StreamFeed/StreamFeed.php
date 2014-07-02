<?php

namespace EventStore\StreamFeed;

final class StreamFeed
{
    use HasLinks;

    private $json;
    private $entryEmbedMode;

    public function __construct(array $json_feed, EntryEmbedMode $embed_mode = null)
    {
        if ($embed_mode === null) {
            $embed_mode = EntryEmbedMode::NONE();
        }

        $this->entryEmbedMode = $embed_mode;
        $this->json           = $json_feed;
    }

    public function getEntries()
    {
        $entries     = [];
        $jsonEntries = $this->json['entries'];

        foreach ($jsonEntries as $jsonEntry) {
            $entries[] = new Entry($jsonEntry);
        }

        return $entries;
    }

    public function getEntryEmbedMode()
    {
        return $this->entryEmbedMode;
    }

    public function getJson()
    {
        return $this->json;
    }

    protected function getLinks()
    {
        return $this->json['links'];
    }
}
