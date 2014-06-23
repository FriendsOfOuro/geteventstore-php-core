<?php

namespace EventStore;

use ValueObjects\Enum\Enum;

final class StreamDeletion extends Enum
{
    const SOFT = 'soft';
    const HARD = 'hard';
}