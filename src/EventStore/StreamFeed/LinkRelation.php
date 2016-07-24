<?php
namespace EventStore\StreamFeed;

use EventStore\ValueObjects\Enum\Enum;

/**
 * Class LinkRelation
 * @package EventStore\StreamFeed
 *
 * @static @method LinkRelation FIRST()
 * @static @method LinkRelation LAST()
 * @static @method LinkRelation PREVIOUS()
 * @static @method LinkRelation NEXT()
 * @static @method LinkRelation METADATA()
 * @static @method LinkRelation ALTERNATE()
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
