<?php
require_once __DIR__ . "/database/db.inc.php";
require_once __DIR__ . "/index.php";

$options_docent[] = new Option("Uitlenen", 'uitleen');
$options_docent[] = new Option("Inleveren", 'inleveren');
$options_docent[] = new Option("Voeg Categorie toe", 'voegtoe_categorie');
$options_docent[] = new Option("Lijst", 'showList');


$access_granted = false;

$sql_doc_users = "SELECT * FROM `user`";
$result_doc_users = $conn->query($sql_doc_users);
$rows = $result_doc_users->fetchAll(PDO::FETCH_ASSOC);

$sql_doc_list_uitgeleend = <<<sql
    SELECT
    l.id,
    p.Product_naam AS naam,
    c.Categorie_naam AS categorie,
    l.inlever_datum,
    s.status_naam AS beschikbaarheid,
    st.student_naam
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
    l.beschikbaarheid = s.id
    INNER JOIN `student` AS st
    ON
    l.student_id = st.id;
sql;
$result_doc_list = $conn->query($sql_doc_list_uitgeleend);
$rows_doc_li = $result_doc_list->fetchAll(PDO::FETCH_ASSOC);

$sql_doc_list_beschikbaar = <<<sql
    SELECT
    l.id,
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
    l.beschikbaarheid = s.id WHERE s.id = 1;
sql;
$result_doc_list2 = $conn->query($sql_doc_list_beschikbaar);
$rows_doc_li2 = $result_doc_list2->fetchAll(PDO::FETCH_ASSOC);


function login_user()
{
    global $rows;
    $login_passed = false;
    echo "vult uw gebuikers naam in:" . PHP_EOL;
    $input = readline(">> ");
    echo PHP_EOL;

    foreach ($rows as $row) 
    {
        if ($input == $row['user_name']) 
        {
            login_pass($row);
            $login_passed = true;
        }
    }
    if (!$login_passed) 
    {
        echo "input invalid";
        exit;
    }
}

function login_pass($user)
{
    global $access_granted;
    global $options_docent;

    echo "vul Uw wachtwoord in:" . PHP_EOL;
    $input = readline(">> ");
    echo PHP_EOL;

    if ($input == $user['password']) 
    {
        $access_granted = true;
    }
    if ($access_granted) 
    {
        askInput($options_docent);
    }
}

function showList_doc_uitgeleend()
{
    global $rows_doc_li;
        echo PHP_EOL;
        echo "Producten:" . PHP_EOL;
        echo PHP_EOL;
        foreach ($rows_doc_li as $item)
        {
            echo "  Id: " . $item['id'] . PHP_EOL; 
            echo "  Naam: " . $item['naam'] . PHP_EOL;
            echo "  Categorie: " . $item['categorie'] . PHP_EOL;
            echo "  Inlever Datum: " . $item['inlever_datum'] . PHP_EOL;
            echo "  Student: " . $item['student_naam']. PHP_EOL;
            echo PHP_EOL;
        }
}

function showList_doc_beschikbaar()
{
        global $rows_doc_li2;
        echo PHP_EOL;
        echo "Producten:" . PHP_EOL;
        echo PHP_EOL;
        foreach ($rows_doc_li2 as $item)
        {
            echo "  id: " . $item['id'] . PHP_EOL;
            echo "  Naam: " . $item['naam'] . PHP_EOL;
            echo "  Categorie: " . $item['categorie'] . PHP_EOL;
            echo PHP_EOL;
        }
}

function uitleen() 
{
    global $conn;
    
    showList_doc_beschikbaar();
    echo "vul a.u.b. een datum in: " .  PHP_EOL;
    $input_item = readline("Product>> ") . PHP_EOL;
    $input_datum = readline("<<YYYY-MM-DD>> ") . PHP_EOL;
    $input_sql = "UPDATE `leenlijst` SET `inlever_datum` = '$input_datum', `beschikbaarheid` = 2 WHERE `beschikbaarheid` = 1 AND `product_id` = '$input_item'";
    $input_result = $conn->query($input_sql);
    
    if ($input_result)
    {
        echo "uitgeleend!";
    }
    else 
    {
        echo "Error: niet uit kunnen lenen";
    }
    global $options_docent;
    askInput($options_docent);
}

function inleveren() 
{
    global $conn;
    showList_doc_uitgeleend();
    
    echo "vul a.u.b. een datum in: " .  PHP_EOL;
    $input_item = readline("Product>> ") . PHP_EOL;
    $input_sql = "UPDATE `leenlijst` SET `inlever_datum` = NULL, `beschikbaarheid` = 1 WHERE `beschikbaarheid` = 2 AND `product_id` = '$input_item'";
    $input_result = $conn->query($input_sql);
    
    if ($input_result)
    {
        echo "ingeleverd!";
    }
    else 
    {
        echo "Error: niet kunnen inleveren";
    }
    global $options_docent;
    askInput($options_docent);
}

function voegtoe_categorie()
{
    global $conn;
    global $options_docent;

    echo "voer a.u.b. een categorie in" . PHP_EOL;
    $add_input = readline("} ");
    $add_sql = "INSERT INTO `categorie` (`Categorie_naam`) VALUES ('$add_input');";
    $insert = $conn->query($add_sql);

    if ($insert)
    {
        echo PHP_EOL;
        echo "Categorie ingevoerd";
        echo PHP_EOL;
    }
    else
    {
        echo PHP_EOL;
        echo "Error: Data is niet ingevoerd";
        echo PHP_EOL;
    }

    askInput($options_docent);
}