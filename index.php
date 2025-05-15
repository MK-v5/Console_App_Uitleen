<?php

    require_once __DIR__ . "/Option.php";
    require_once __DIR__ . "/docent.php";

    $options = [];

    function stop(){
        exit;
    }

    $options[] = new Option("Exit", 'stop');
    $options[] = new Option("Docent", "docent");
    // $options[] = new Option("Lijst", "Naar Lijst");

 
    function askInput(array $options)
    {

        echo "Please input an option\n";
        foreach ($options as $key => $option){
            // EOL is End Of Line
            echo "[" . $key + 1 . "] " . $option->getName() . PHP_EOL;
        }
        $input = readline(">> ");

        $selected_option = $options[$input - 1];

        $selected_option->execute();
    }

askInput($options);