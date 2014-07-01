<?php

namespace EventStore;

use ValueObjects\Enum\Enum;

/**
 * Class StreamFeedLinkRelation
 * @package EventStore
 *
 * @method string FIRST()
 * @method string LAST()
 * @method string PREVIOUS()
 * @method string NEXT()
 * @method string METADATA()
 */
final class StreamFeedLinkRelation extends Enum
{
    const FIRST    = 'first';
    const LAST     = 'last';
    const PREVIOUS = 'previous';
    const NEXT     = 'next';
    const METADATA = 'metadata';
}
