<?php

namespace EventStore;

use ValueObjects\Enum\Enum;

/**
 * Class StreamDeletion
 * @package EventStore
 * @static @method StreamDeletion SOFT()
 * @static @method StreamDeletion HARD()
 */
final class StreamDeletion extends Enum
{
    const SOFT = 'soft';
    const HARD = 'hard';
}
