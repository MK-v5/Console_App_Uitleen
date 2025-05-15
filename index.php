<?php

    $options = [ "Exit", "Docent", "lijst" ];

    function askInput()
    {
        global $options;
        $i = 0;
        echo "Please input an option\n";
        foreach ($options as $option){
            // EOL is End Of Line
            echo "[" . $i++ . "]" . $option . PHP_EOL;
        }
        echo ">> ";
        $input = readline();

        if ($input == 0)
        {
            exit(0);
        }
        if ($input == 1)
        {

        }
        if ($input == 2)
        {
            
        }
    }

