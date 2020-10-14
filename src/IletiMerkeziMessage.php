<?php

namespace Macellan\IletiMerkezi;

use Carbon\Carbon;

class IletiMerkeziMessage
{
    public $body;

    public $sendTime;

    public $numbers;

    /**
     * @param string $body
     * @return static
     */
    public static function create($body = '')
    {
        return new static($body);
    }

    /**
     * @param string $body
     */
    public function __construct(string $body)
    {
        $this->body = $body;
        $this->numbers = [];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setBody(string $value)
    {
        $this->body = $value;

        return $this;
    }

    /**
     * @param Carbon $value
     * @return $this
     */
    public function setSendTime(Carbon $value)
    {
        $this->sendTime = $value->format('d/m/Y H:i');

        return $this;
    }

    /**
     * @param array $array
     * @return $this
     */
    public function addNumbers(array $array)
    {
        $this->numbers = $array;

        return $this;
    }
}
