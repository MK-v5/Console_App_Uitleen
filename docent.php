<?php
require_once __DIR__ . "/database/db.inc.php";
require_once __DIR__ . "/index.php";

$options[] = new Option("Uitlenen", 'uitleen');

$access_granted = false;

$sql_doc_users = "SELECT * FROM `user`";
$result_doc_users = $conn->query($sql_doc_users);
$rows = $result_doc_users->fetchAll(PDO::FETCH_ASSOC);

$sql_doc_list = <<<sql
    SELECT
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
$result_doc_list = $conn->query($sql_doc_list);
$rows_doc_li = $result_doc_list->fetchAll(PDO::FETCH_ASSOC);


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

    echo "vul Uw wachtwoord in:" . PHP_EOL;
    $input = readline(">> ");
    echo PHP_EOL;

    if ($input == $user['password']) 
    {
        $access_granted = true;
    }
    if ($access_granted) 
    {
        showList_doc();
    }
}

function showList_doc()
{
    global $rows_doc_li;
        echo PHP_EOL;
        echo "Producten:" . PHP_EOL;
        echo PHP_EOL;
        foreach ($rows_doc_li as $item)
        {
            echo "  Naam: " . $item['naam'] . PHP_EOL;
            echo "  Beschikbaarheid: " . $item['beschikbaarheid'] . PHP_EOL;
            echo "  Inlever Datum: " . $item['inlever_datum'] . PHP_EOL;
            echo "  Student: " . $item['student_naam']. PHP_EOL;
            echo PHP_EOL;
        }
}

function uitleen() {}
