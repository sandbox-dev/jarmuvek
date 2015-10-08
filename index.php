<?php
session_start();
require_once "autoloader.php";
new Autoloader();
$pg = Pg::getPg();

// Html példány:
$h = new Html();

// Html fejléc
$h->header("Járművek");

// Fömenü
// 1. sor menü
$menu = array (
'Járművek alapadatai'=>'jarmualap.php',
'2015. évi szeptemberi Volvok'=>'volvo201509.php',
'2015. évi T5C5 kocsik'=>'t5c52015.php',
'2015. évi TW6000 kocsik'=>'tw60002015.php'
);
$h->btnMenu($menu);
$h->separator();
$menu = array (
'2014. évi T5C5 kocsik'=>'t5c52014.php'
);
$h->btnMenu($menu);
$h->separator();
if (isset($_SESSION['userAuth']) && $_SESSION['userAuth'] >=8) {
// 2. sor menü
$menu = array (
'Új jármű rögzítése'=>'ujjarmu.php',
);
$h->btnMenu($menu);
}
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
