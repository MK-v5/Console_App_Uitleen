<?php

    require_once __DIR__ . "/Option.php";
    require_once __DIR__ . "/docent.php";
    require_once __DIR__ . "/database/db.inc.php"; 

    $options = [];

    $options[] = new Option("Sluiten", 'stop');
    $options[] = new Option("Inloggen als docent", 'inlog_user');
    $options[] = new Option("Lijst", 'showList');

    $sql_lijst_student = <<<sql
    SELECT
        p.Product_naam AS naam,
        c.Categorie_naam AS categorie,
        l.inlever_datum,
        s.status_naam AS beschikbaarheid
    FROM
        `leenlijst` AS l
    INNER JOIN `product` AS p
    ON
        p.id = l.product_id
    INNER JOIN `categorie` AS c
    ON
        p.catergorie_id = c.id
    INNER JOIN `status` AS s
    ON
        l.beschikbaarheid = s.id;
    sql;

    $sql_lijst_result = $conn->query($sql_lijst_student);

    function stop()
    {
        exit;
    }

    function showList()
    {
        global $sql_lijst_result;
        $rows = $sql_lijst_result->fetchAll(PDO::FETCH_ASSOC);

        echo PHP_EOL;
        echo "Producten:" . PHP_EOL;
        echo PHP_EOL;
        foreach ($rows as $item)
        {
            echo "  Naam: " . $item['naam'] . PHP_EOL;
            echo "  Categorie: " . $item['categorie'] . PHP_EOL;
            echo "  Beschikbaarheid: " . $item['beschikbaarheid'] . PHP_EOL;
            echo "  Inlever Datum: " . $item ['inlever_datum'] . PHP_EOL;
            echo PHP_EOL;
        }
    }


    function askInput(array $options)
    {
        echo "Please input an option\n";
        foreach ($options as $key => $option)
        {
            // EOL is End Of Line
            echo "[" . $key + 1 . "] " . $option->getName() . PHP_EOL;
        }
        $input = readline(">> ");

        $selected_option = $options[$input - 1];

        $selected_option->execute();
    }

askInput($options);