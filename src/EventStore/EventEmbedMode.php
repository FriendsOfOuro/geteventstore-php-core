<?php

namespace EventStore;

use ValueObjects\Enum\Enum;

/**
 * Class EventEmbedMode
 * @package EventStore
 * @method string NONE()
 * @method string RICH()
 * @method string BODY()
 */
final class EventEmbedMode extends Enum
{
    const NONE = 'none';
    const RICH = 'rich';
    const BODY = 'body';
}
