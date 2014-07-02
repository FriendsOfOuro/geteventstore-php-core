<?php

namespace EventStore;

use ValueObjects\Enum\Enum;

/**
 * Class StreamFeedLinkRelation
 * @package EventStore
 *
 * @static @method string FIRST()
 * @static @method string LAST()
 * @static @method string PREVIOUS()
 * @static @method string NEXT()
 * @static @method string METADATA()
 */
final class StreamFeedLinkRelation extends Enum
{
    const FIRST    = 'first';
    const LAST     = 'last';
    const PREVIOUS = 'previous';
    const NEXT     = 'next';
    const METADATA = 'metadata';
}
