<?php

class Option
{
    private string $name;
    private $command;

    public function __construct(string $name, callable $command)
    {
        $this->name = $name;
        $this->command = $command;

    }

    public function getName()
    {
        return $this->name;
    }
    
    public function getCommand()
    {
        return $this->command;
    }

    public function execute() {

            call_user_func($this->command);
        
    }
}