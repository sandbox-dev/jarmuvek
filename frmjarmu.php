<?php
session_start()
require_once "autoloader.php";
new Autoloader();
$pg = Pg::getPg();

// Kimenet tárazása
ob_start();


// Html példány:
$h = new Html();

// Html fejléc
$h->header("Járművek");

// Elválasztó
$h->separator();
// Oldal alja gombok:
$h->btnBack();
$h->btnExit();
$h->btnPrint();

// Html lábléc
$h->footer();

// Kimenet kiírása
ob_flush();
?>

