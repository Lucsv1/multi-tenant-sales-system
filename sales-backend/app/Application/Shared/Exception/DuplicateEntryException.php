<?php

namespace App\Application\Shared\Exception;

use Exception;

class DuplicateEntryException extends Exception
{
    protected string $field;
    protected string $table;

    public function __construct(string $message = '', string $field = '', string $table = '')
    {
        parent::__construct($message);
        $this->field = $field;
        $this->table = $table;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
