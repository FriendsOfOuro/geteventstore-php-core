<?php

namespace EventStore\StreamFeed;

/**
 * Class StreamFeed
 * @package EventStore\StreamFeed
 */
final class StreamFeed
{
    use HasLinks;

    /**
     * @var array
     */
    private $json;

    /**
     * @var EntryEmbedMode
     */
    private $entryEmbedMode;

    /**
     * @param array          $json_feed
     * @param EntryEmbedMode $embed_mode
     */
    public function __construct(array $json_feed, EntryEmbedMode $embed_mode = null)
    {
        if ($embed_mode === null) {
            $embed_mode = EntryEmbedMode::NONE();
        }

        $this->entryEmbedMode = $embed_mode;
        $this->json           = $json_feed;
    }

    /**
     * @return Entry[]
     */
    public function getEntries()
    {
        return array_map(
            function (array $jsonEntry) {
                return new Entry($jsonEntry);
            },
            $this->json['entries']
        );
    }

    /**
     * @return EntryEmbedMode
     */
    public function getEntryEmbedMode()
    {
        return $this->entryEmbedMode;
    }

    /**
     * @return array
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @return array
     */
    protected function getLinks()
    {
        return $this->json['links'];
    }
}
