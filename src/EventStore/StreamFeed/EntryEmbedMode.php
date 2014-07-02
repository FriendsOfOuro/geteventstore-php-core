<?php

namespace EventStore\StreamFeed;

use ValueObjects\Enum\Enum;

/**
 * Class EntryEmbedMode
 * @package EventStore\StreamFeed
 * @static @method string NONE()
 * @static @method string RICH()
 * @static @method string BODY()
 */
final class EntryEmbedMode extends Enum
{
    const NONE = 'none';
    const RICH = 'rich';
    const BODY = 'body';
}
