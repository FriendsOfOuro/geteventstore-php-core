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
     * @param array          $jsonFeed
     * @param EntryEmbedMode $embedMode
     */
    public function __construct(array $jsonFeed, EntryEmbedMode $embedMode = null)
    {
        if ($embedMode === null) {
            $embedMode = EntryEmbedMode::NONE();
        }

        $this->entryEmbedMode = $embedMode;
        $this->json           = $jsonFeed;
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
