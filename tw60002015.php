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
$h->header("2015.  - TW6000");

$h->msgOk("2015. évi TW6000  dátumok");

$h->btnMenu(array("Átfutási idők - diagram"=>"tw60002015diagram.php",
                   "Vissza a főmenühöz"=>"index.php"));

$sql =  "select id, psz, ";
$sql .= "sorszam, erkezett, allapotfelvetel, reszatvetel, vegatvetel, hazaadas, megjegyzes ";
$sql .= "from jarmu_alap where ev=2015 and jarmutipus ilike 'tw6000 j1' order by sorszam";
$res = $pg->query($sql);
// Van-e visszaadott sor
$count = $res->rowCount();
if ($res) {
  if ($count) {
    echo "<table class='jmu-info-table' border=1 style='border-collapse:collapse'>";
    $h->infoTableCaption("2015. évi TW6000 kocsik");
    echo "<tr>
    <th>Pályaszám</th>
    <th>Sorszám</th>
    <th>Érkezett</th>
    <th>Állapotfelvétel</th>
    <th>Részátvétel</th>
    <th>Végátvétel</th>
    <th>Hazaadás</th>
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
