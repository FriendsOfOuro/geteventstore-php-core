<?php

namespace EventStore;

use ValueObjects\Enum\Enum;

final class StreamFeedLinkRelation extends Enum
{
    const FIRST    = 'first';
    const LAST     = 'last';
    const PREVIOUS = 'previous';
    const NEXT     = 'next';
    const METADATA = 'metadata';
}