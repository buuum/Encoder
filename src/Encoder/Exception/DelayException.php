<?php
namespace Buuum\Encoder\Exception;

class DelayException extends \Exception
{
    protected $date;

    public function __construct($message, $date)
    {
        parent::__construct($message);
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }
}
