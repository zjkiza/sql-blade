<?php

declare(strict_types=1);

namespace Zjk\SqlBlade\Exception;

use Zjk\SqlBlade\Contract\ExceptionInterface;
use Zjk\SqlBlade\Exception\Message\ExceptionMessage;

class SyntaxErrorException extends \Exception implements ExceptionInterface
{
    use ExceptionMessage;
}
