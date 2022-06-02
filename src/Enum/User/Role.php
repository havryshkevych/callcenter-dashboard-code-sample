<?php declare(strict_types=1);

namespace App\Enum\User;

use Elao\Enum\SimpleChoiceEnum;

/**
 * @method static self OPERATOR()
 * @method static self SUPERVISOR()
 */
class Role extends SimpleChoiceEnum
{
    const OPERATOR = 'ROLE_OPERATOR';
    const SUPERVISOR = 'ROLE_SUPERVISOR';
}
