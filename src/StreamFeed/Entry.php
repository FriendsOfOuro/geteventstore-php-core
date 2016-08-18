<?php
namespace EventStore\StreamFeed;

/**
 * Class Entry
 * @package EventStore\StreamFeed
 */
final class Entry
{
    use HasLinks;

    /**
     * @var array
     */
    private $json;

    /**
     * @param array $json
     */
    public function __construct(array $json)
    {
        $this->json = $json;
    }

    /**
     * @return null|string
     */
    public function getEventUrl()
    {
        $alternate = $this->getLinkUrl(LinkRelation::ALTERNATE());

        return $alternate;
    }

    public function getTitle()
    {
        return $this->json['title'];
    }

    /**
     * @return array
     */
    protected function getLinks()
    {
        return $this->json['links'];
    }
}
