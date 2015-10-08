<?php
session_start();
require_once 'autoloader.php';
new Autoloader();
$pg = Pg::getPg();


// Kimenet tárazása
ob_start();

// Csatlakozás az adatbázishoz.


// Html példány:
$h = new Html();
// vissza link
$h->setBackRef();

// Html fejléc
$h->header("2015. szeptember - Volvo");

$h->msgOk("Volvo dátumok");

$h->btnMenu(array("Átfutási idők - diagram"=>"volvo201509diagram.php",
                   "Vissza a főmenühöz"=>"index.php"));

$sql =  "select id, psz, esz, sorszam, erkezett, allapotfelvetel,";
$sql .= " vegatvetel, hazaadas, szamlazas, megjegyzes ";
$sql .= "from jarmu_alap where psz ilike'ncz%' and ev=2015 order by sorszam";
$res = $pg->query($sql);
// Van-e visszaadott sor
$count = $res->rowCount();
if ($res) {
  if ($count) {
    echo "<table class='jmu-info-table' border=1 style='border-collapse:collapse'>";
    $h->infoTableCaption("2015. év szeptemberi Volvo buszok");
    echo "<tr>
    <th>Rendszám</th>
    <th>Egyedi szám</th>
    <th>Sorszám</th>
    <th>Érkezett</th>
    <th>Állapotfelvétel</th>
    <th>Végátvétel</th>
    <th>Hazaadás</th>
    <th>Számlázás</th>
    <th>Megjegyzés</th>
    </tr>";
    while($row = $res->fetch(PDO::FETCH_BOTH)) {
      echo "<tr onclick=jmuInfo($row[0]);>
      <td class='tdc5p'>$row[1]</td>
      <td class='tdc5p'>$row[2]</td>
      <td class='tdc5p'>$row[3]</td>
      <td class='tdc5p'>$row[4]</td>
      <td class='tdc5p'>$row[5]</td>
      <td class='tdc5p'>$row[6]</td>
      <td class='tdc5p'>$row[7]</td>
      <td class='tdc5p'>$row[8]</td>
      <td class='tdc5p'>$row[9]</td>
      </tr>";
    }
    echo "</table>";
    
  }
  else {
    $h->msgWarn("A kérés nem hozott eredményt!");
  }
}
// Oldal alja gombok:
$h->separator();
$h->btnBack();
$h->btnExit();
$h->btnPrint();
$h->footer();    
  

?>
