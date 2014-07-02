<?php

namespace EventStore;

use ValueObjects\Enum\Enum;

/**
 * Class EventEmbedMode
 * @package EventStore
 * @static @method string NONE()
 * @static @method string RICH()
 * @static @method string BODY()
 */
final class EventEmbedMode extends Enum
{
    const NONE = 'none';
    const RICH = 'rich';
    const BODY = 'body';
}
