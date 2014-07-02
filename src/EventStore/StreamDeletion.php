<?php

namespace EventStore;

use ValueObjects\Enum\Enum;

/**
 * Class StreamDeletion
 * @package EventStore
 * @static @method SOFT()
 * @static @method HARD()
 */
final class StreamDeletion extends Enum
{
    const SOFT = 'soft';
    const HARD = 'hard';
}
