<?php
// Ez egy próba, hogyan meg más tárolóból az átemelés...
require_once "autoloader.php";

new Autoloader();

$h = new Html();

$h->header("HEllÓ");

$pg = Pg::getPg();
print_r($pg);
$sql = "select * from jarmu_alap";
$res = $pg->query($sql);
$row = $res->fetch(PDO::FETCH_NUM);
print_r($row);

$h->footer();

?>
