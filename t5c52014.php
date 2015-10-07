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

// Html fejléc
$h->header("2014.  - T5C5");

$h->msgOk("2014. évi T5C5  dátumok");

$h->btnMenu(array("Átfutási idők - diagram"=>"t5c52014diagram.php",
                   "Vissza a főmenühöz"=>"index.php"));

$sql =  "select psz, ";
$sql .= "sorszam, erkezett, allapotfelvetel, reszatvetel, vegatvetel, hazaadas ";
$sql .= "from jarmu_alap where ev=2014 and jarmutipus ilike 't5c5k2mod' order by sorszam";
$res = $pg->query($sql);
// Van-e visszaadott sor
$count = $res->rowCount();
if ($res) {
  if ($count) {
    echo "<table class='jmu-info-table' border=1 style='border-collapse:collapse'>";
    $h->infoTableCaption("2014. évi T5C5 kocsik");
    echo "<tr>
    <th>Pályaszám</th>
    <th>Sorszám</th>
    <th>Érkezett</th>
    <th>Állapotfelvétel</th>
    <th>Részátvétel</th>
    <th>Végátvétel</th>
    <th>Hazaadás</th>
    </tr>";
    while($row = $res->fetch(PDO::FETCH_BOTH)) {
      echo "<tr>
      <td class='tdc5p'>$row[0]</td>
      <td class='tdc5p'>$row[1]</td>
      <td class='tdc5p'>$row[2]</td>
      <td class='tdc5p'>$row[3]</td>
      <td class='tdc5p'>$row[4]</td>
      <td class='tdc5p'>$row[5]</td>
      <td class='tdc5p'>$row[6]</td>
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
