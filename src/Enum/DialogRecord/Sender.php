<?php declare(strict_types=1);

namespace App\Enum\DialogRecord;

use Elao\Enum\SimpleChoiceEnum;

/**
 * @method static self CUSTOMER()
 * @method static self OPERATOR()
 * @method static self SYSTEM()
 */
class Sender extends SimpleChoiceEnum
{
    const CUSTOMER = 'customer';
    const OPERATOR = 'operator';
    const SYSTEM = 'system';
}
