<?php
session_start();


$allowedPages = ['home', 'carrinho', 'sobre', 'login'];
$page = $_GET['page'] ?? 'home';

if (in_array($page, $allowedPages)) {
    include "$page.php";
} else {
    include "home.php";
}
?>

?>
