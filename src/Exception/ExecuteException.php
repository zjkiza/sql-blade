<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Exception;

use Zjk\SqlBlade\Exception\Message\ExceptionMessage;

class ExecuteException extends \RuntimeException
{
    use ExceptionMessage;
}
