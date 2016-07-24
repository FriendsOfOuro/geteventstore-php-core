<?php
namespace EventStore\StreamFeed;

use EventStore\ValueObjects\Enum\Enum;

/**
 * Class EntryEmbedMode
 * @package EventStore\StreamFeed
 * @static @method EntryEmbedMode NONE()
 * @static @method EntryEmbedMode RICH()
 * @static @method EntryEmbedMode BODY()
 */
final class EntryEmbedMode extends Enum
{
    const NONE = 'none';
    const RICH = 'rich';
    const BODY = 'body';
}
