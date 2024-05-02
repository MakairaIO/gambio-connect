<?php

namespace Makaira\HttpClient;

class Response extends \Kore\DataObject\DataObject
{
    public $totalTime;
    public $status;
    public $headers;
    public $body;
}
