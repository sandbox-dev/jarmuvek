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
$h->header("2015. évi KCSV-7 kocsik");

$h->msgOk("2015. évi KCSV-7 kocsik átfutási idő diagramja");

$h->btnMenu(array("Vissza a 2015. évi KCSV-7 kocsikhoz"=>"kcsv72015.php",
                   "Vissza a főmenühöz"=>"index.php"));

// átlag átfutási idő meghatározása
$sql  = "select (avg(vegatvetel-erkezett)::int) as atlag from jarmu_alap ";
$sql .= "where ev=2015 and jarmutipus ilike 'kcsv%' ";

$res = $pg->query($sql);
$row = $res->fetch(PDO::FETCH_BOTH);
$avgTime = $row[0];

// Tervezett Átfutási idő:
$sql = "select avg(terv_atfutasi_ido)::int from jarmu_alap where ";
$sql .= "ev=2015 and jarmutipus ilike 'kcsv%' ";
$res = $pg->query($sql);
$plannedTime = $res->fetch(PDO::FETCH_BOTH)[0];

$sql  = "select sorszam, psz, erkezett, ";
$sql .= "vegatvetel, vegatvetel-erkezett as nap, terv_atfutasi_ido from jarmu_alap ";
$sql .= "where ev=2015 and jarmutipus ilike 'kcsv%' order by 1;";

$res = $pg->query($sql);

echo "<table border='1' style='border-collapse:collapse;'>";
$h->infoTableCaptionDiagram("2015. évi KVCS-7 kocsik átfutási idő diagramja");
echo "<tr>
      <th>Sorszám</th>
      <th>Rendszám</th>
      <th>Dátumok</th>
      <th>Átfutási idő (1 &diams; = 1 nap, átlag átfutási idő: $avgTime nap, tervezett átfutási idő $plannedTime nap.)</th>
      </tr>";

while ($row = $res->fetch(PDO::FETCH_BOTH)) {
  echo "<tr>
        <td>$row[0]</td>
        <td>$row[1]</td> 
        <td>$row[2] &dash; $row[3] &dash; $row[4] nap</td>
        <td>";
          for ($i=0; $i<$row[4]; $i++) {
            if ($i < $row[5]) {
              echo "<b style='color:#007D00;font-size:75%;'>&diams;</b>";
            }
            if ($i >= $row[5] and $i <= $avgTime) {
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

