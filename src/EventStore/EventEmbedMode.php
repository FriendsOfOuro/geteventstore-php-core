<?php

namespace EventStore;

use ValueObjects\Enum\Enum;

final class EventEmbedMode extends Enum
{
    const NONE = 'none';
    const RICH = 'rich';
    const BODY = 'body';
}
