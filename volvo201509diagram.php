<?php
require_once 'inc/session-timeout.php';
session_start();

// Kimenet tárazása
ob_start();

// Csatlakozás az adatbázishoz.
require_once 'inc/pg-init.php';
require_once 'class/class.Html.php';

// Html példány:
$h = new Html();

// Html fejléc
$h->header("2015. szeptember Volvo diagram");

$h->msgOk("2015. szeptemberi Volvo buszok átfutási idő diagramja");

$sql  = "select sorszam, substring(psz,1,6) as rendszam, erkezett, ";
$sql .= "vegatvetel, vegatvetel-erkezett as nap from jarmu_alap ";
$sql .= "where psz ilike'ncz%' and sorszam<2000 order by 1;";

$res = $pg->query($sql);

echo "<table border='1' style='border-collapse:collapse;'>";
echo "<tr>
      <th>Sorszám</th>
      <th>Rendszám</th>
      <th>Dátumok</th>
      <th>Átfutási idő (1 &middot; = 1 nap)</th>
      </tr>";

while ($row = $res->fetch(PDO::FETCH_BOTH)) {
  echo "<tr>
        <td>$row[0]</td>
        <td>$row[1]</td> 
        <td>$row[2] &dash; $row[3] &mdash; $row[4] nap</td>
        <td>";
          for ($i=0; $i<$row[4]; $i++) {
            echo "<b>&middot;</b>";
          }
  echo "</td>
        </tr>";
}
echo "</table>";
$h->btnMenu(array("Vissza a 2015. szeptemberi Volvo dátumokhoz"=>"volvo201509.php"));

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

