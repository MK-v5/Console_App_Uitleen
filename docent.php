<?php
require_once __DIR__ . "/database/db.inc.php";
require_once __DIR__ . "/index.php";

$options_docent[] = new Option("Uitlenen", 'uitleen');
$options_docent[] = new Option("Inleveren", 'inleveren');
$options_docent[] = new Option("Voeg Categorie toe", 'voegtoe_categorie');
$options_docent[] = new Option("Voeg Docent toe", 'voegtoe_docent');
$options_docent[] = new Option("Verwijder Docent", 'verwijder_docent');
$options_docent[] = new Option("Lijst", 'showList');
$options_docent[] = new Option("Sluiten", 'stop');


$access_granted = false;



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

$sql_doc_list_studenten = "SELECT * FROM `student`";
$result_doc_list3 = $conn->query($sql_doc_list_studenten);
$rows_doc_li3 = $result_doc_list3->fetchAll(PDO::FETCH_ASSOC);

function fetch_user($user_name)
{
    global $conn;

    $sql_doc_users = "SELECT * FROM `user` WHERE user_name = :uname";
    $result_doc_users = $conn->prepare($sql_doc_users);
    $result_doc_users->bindParam("uname", $user_name);
    $result_doc_users->execute();
    return $result_doc_users->fetch(PDO::FETCH_ASSOC);
}

function login_user()
{
    $login_passed = false;
    echo "vult uw gebuikers naam in:" . PHP_EOL;
    $input = trim(readline(">> "));
    echo PHP_EOL;
    $user = fetch_user($input);

    if ($input == $user['user_name']) 
    {
        login_pass($user);
        $login_passed = true;
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
    $input = trim(readline(">> "));
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

function showList_doc_stud()
{
    global $rows_doc_li3;
    echo PHP_EOL;
    echo "Producten:" . PHP_EOL;
    echo PHP_EOL;
    foreach ($rows_doc_li3 as $item)
    {
        echo "  id: " . $item['id'] . PHP_EOL;
        echo "  Naam: " . $item['student_naam'] . PHP_EOL;
        echo PHP_EOL;
    }
}

function uitleen() 
{
    global $conn;
    
    showList_doc_beschikbaar();
    showList_doc_stud();
    echo "vul a.u.b. een product id in: " . PHP_EOL;
    $input_item = readline("Product ID>> ") . PHP_EOL;
    echo "vul a.u.b. een datum in: " . PHP_EOL;
    $input_datum = readline("YYYY-MM-DD>> ") . PHP_EOL;
    echo "vul a.u.b. een student id in: " . PHP_EOL;
    $input_stud = readline("Student ID>> ") . PHP_EOL;

    $input_sql = "UPDATE `leenlijst` SET `student_id` = '$input_stud', `inlever_datum` = '$input_datum', `beschikbaarheid` = 2 WHERE `beschikbaarheid` = 1 AND `id` = '$input_item'";
    $input_result = $conn->query($input_sql);
    
    if ($input_result)
    {
        echo PHP_EOL;
        echo "uitgeleend!";
        echo PHP_EOL;
    }
    else 
    {
        echo PHP_EOL;
        echo "Error: niet uit kunnen lenen";
        echo PHP_EOL;
    }
    global $options_docent;
    askInput($options_docent);
}

function inleveren()
{
    global $conn;
    showList_doc_uitgeleend();
    
    echo "vul a.u.b. een product in: " .  PHP_EOL;
    $input_item2 = readline("Product>> ") . PHP_EOL;
    $input_sql2 = "UPDATE `leenlijst` SET `student_id` = NULL, `inlever_datum` = NULL, `beschikbaarheid` = 1 WHERE `beschikbaarheid` = 2 AND `id` = '$input_item2'";
    $input_result2 = $conn->query($input_sql2);
    
    if ($input_result2)
    {
        echo PHP_EOL;
        echo "ingeleverd!";
        echo PHP_EOL;
    }
    else 
    {
        echo PHP_EOL;
        echo "Error: niet kunnen inleveren";
        echo PHP_EOL;
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

function voegtoe_docent()
{
    global $conn;
    
    echo "voer naam in" . PHP_EOL;
    $un_input = trim(readline("} ")) . PHP_EOL;
    echo "voer wachtwoord in" . PHP_EOL;
    $pass_input = trim(readline("} ")) . PHP_EOL;
    $user_sql = "INSERT INTO `user` (`user_name`, `password`) VALUES ('$un_input', '$pass_input');";
    $insert = $conn->query($user_sql);
    
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
    
    global $options_docent;
    askInput($options_docent);
}

//je kan docenten nog niet verwijderen

// function verwijder_docent()
// {
//     global $conn;
//     global $options_docent;
//     echo "vul gebuikers naam in:" . PHP_EOL;
//     $input_Del1 = trim(readline(">> ")) . PHP_EOL;
//     echo "vul wachtwoord in:" . PHP_EOL;
//     $input_Del2 = trim(readline(">> ")) . PHP_EOL;
//     $sql_del_users = "DELETE FROM `user` WHERE `user_name` = '$input_Del1' AND `password` = '$input_Del2'";
//     //$delete = $conn->query($sql_del_users);
//     $conn->exec($sql_del_users);
//     echo "succesfully deleted user.";

//     // var_dump($delete);
//     // exit;

//     // if ($delete) 
//     // {
//     //     echo PHP_EOL;
//     //     echo PHP_EOL;
//     // }
//     // else
//     // {
//     //     echo PHP_EOL;
//     //     echo "input invalid";
//     //     echo PHP_EOL;
//     // }
//     askInput($options_docent);
// }