<?php

namespace App\Lib;

class Request
{
    public $reqMethod;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->reqMethod = trim($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @return array|string
     */
    public function getBody()
    {
        if ($this->reqMethod !== 'POST') {
            return '';
        }

        $body = [];
        foreach ($_POST as $key => $value) {
            $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $body;
    }
}