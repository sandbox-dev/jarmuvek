<?php
session_start();
require_once 'autoloader.php';
new Autoloader();
$pg = Pg::getPg();

// Kimenet tárazása
ob_start();



// Html példány:
$h = new Html();

// Html fejléc
$h->header("2015. szeptember Volvo diagram");

$h->msgOk("2015. szeptemberi Volvo buszok átfutási idő diagramja");

$h->btnMenu(array("Vissza a 2015. szeptemberi Volvo dátumokhoz"=>"volvo201509.php",
                   "Vissza a főmenühöz"=>"index.php"));

// átlag átfutási idő meghatározása
$sql  = "select (avg(vegatvetel-erkezett)::int) as atlag from jarmu_alap ";
$sql .= "where psz ilike 'ncz%' and sorszam < 2000;";

$res = $pg->query($sql);
$row = $res->fetch(PDO::FETCH_BOTH);
$avgTime = $row[0];
// Tervezett Átfutási idő:
$plannedTime = 35;

$sql  = "select sorszam, substring(psz,1,6) as rendszam, erkezett, ";
$sql .= "vegatvetel, vegatvetel-erkezett as nap from jarmu_alap ";
$sql .= "where psz ilike'ncz%' and sorszam<2000 order by 1;";

$res = $pg->query($sql);

echo "<table border='1' style='border-collapse:collapse;'>";
$h->infoTableCaptionDiagram("2015. év szeptemberi Volvo buszok átfutási idő diagramja");
echo "<tr>
      <th>Sorszám</th>
      <th>Rendszám</th>
      <th>Dátumok</th>
      <th>Átfutási idő (1 &diams; = 1 nap, átlag átfutási idő: $avgTime nap, tervezett átfutási idő 35 nap.)</th>
      </tr>";

while ($row = $res->fetch(PDO::FETCH_BOTH)) {
  echo "<tr>
        <td>$row[0]</td>
        <td>$row[1]</td> 
        <td>$row[2] &dash; $row[3] &dash; $row[4] nap</td>
        <td>";
          for ($i=0; $i<$row[4]; $i++) {
            if ($i < $plannedTime) {
              echo "<b style='color:#007D00;font-size:75%;'>&diams;</b>";
            }
            if ($i >= $plannedTime and $i <= $avgTime) {
              echo "<b style='color:#f80;font-size:75%;'>&diams;</b>";
            }
            if ($i > $avgTime) {
              echo "<b style='color:#CA0000;font-size:75%;'>&diams;</b>";
            }
          }  // pontok ciklus vége

  echo "</td>
        </tr>";
}
echo "</table>";


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

