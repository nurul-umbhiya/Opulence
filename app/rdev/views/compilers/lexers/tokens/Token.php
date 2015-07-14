<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a view token
 */
namespace RDev\Views\Compilers\Lexers\Tokens;

class Token
{
    /** @var int The token type */
    private $type = null;
    /** @var mixed The value of the token */
    private $value = null;
    /** @var int The line the token is on */
    private $line = 0;

    /**
     * @param int $type The token type
     * @param mixed $value The value of the token
     * @param int $line The line the token is on
     */
    public function __construct($type, $value, $line)
    {
        $this->type = $type;
        $this->value = $value;
        $this->line = $line;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}