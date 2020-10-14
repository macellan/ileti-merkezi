<?php

namespace Macellan\IletiMerkezi\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when we're unable to communicate with ileti merkezi.
     *
     * @param Exception $exception
     * @param string $message
     * @return CouldNotSendNotification
     */
    public static function couldNotCommunicateWithEndPoint(Exception $exception, string $message): self
    {
        return new static("The communication with endpoint failed. Reason: {$message}", $exception->getCode(), $exception);
    }
}
