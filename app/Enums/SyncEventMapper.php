<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static SyncEventMapper Create()
 * @method static SyncEventMapper Update()
 * @method static SyncEventMapper Delete()
 */
final class SyncEventMapper extends Enum
{
    const Create = 'onCreate';
    const Update = 'onUpdate';
    const Delete = 'onDelete';
}
