<?php declare(strict_types=1);

namespace App\Enum\Dialog;

use Elao\Enum\SimpleChoiceEnum;

/**
 * @method static self CALL()
 * @method static self CHAT()
 */
class Type extends SimpleChoiceEnum
{
    const CALL = 'call';
    const CHAT = 'chat';
}
