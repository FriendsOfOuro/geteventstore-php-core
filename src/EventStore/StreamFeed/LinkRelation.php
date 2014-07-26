<?php

namespace EventStore\StreamFeed;

use ValueObjects\Enum\Enum;

/**
 * Class LinkRelation
 * @package EventStore\StreamFeed
 *
 * @static @method string FIRST()
 * @static @method string LAST()
 * @static @method string PREVIOUS()
 * @static @method string NEXT()
 * @static @method string METADATA()
 * @static @method string ALTERNATE()
 */
final class LinkRelation extends Enum
{
    const FIRST     = 'first';
    const LAST      = 'last';
    const PREVIOUS  = 'previous';
    const NEXT      = 'next';
    const METADATA  = 'metadata';
    const ALTERNATE = 'alternate';
}
