<?php
namespace EventStore;

use EventStore\ValueObjects\Enum\Enum;

/**
 * Class StreamDeletion.
 *
 * @static @method StreamDeletion SOFT()
 * @static @method StreamDeletion HARD()
 */
final class StreamDeletion extends Enum
{
    const SOFT = 'soft';
    const HARD = 'hard';
}
