<?php

namespace App\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static MessageType Individual()
 * @method static MessageType Group()
 * @method static MessageType All()
 */
class MessageType extends Enum
{
    private const Individual = 'individual';
    private const Group = 'group';
    private const All = 'all';
}
