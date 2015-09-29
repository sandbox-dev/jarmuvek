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
$h->header("2015. szeptember - Volvo");

$h->msgOk("Volvo dátumok");

$h->btnMenu(array("Átfutási idők - diagram"=>"volvo201509diagram.php"));

$sql =  "select substring(psz,1,6) as rsz, substring(psz,7,4) as asz, ";
$sql .= "sorszam, erkezett, allapotfelvetel, reszatvetel, vegatvetel, hazaadas ";
$sql .= "from jarmu_alap where psz ilike'ncz%' and ev=2015 order by sorszam";
$res = $pg->query($sql);
// Van-e visszaadott sor
$count = $res->rowCount();
if ($res) {
  if ($count) {
    echo "<table class='jmu-info-table' border=1 style='border-collapse:collapse'>";
    echo "<tr>
    <th>Rendszám</th>
    <th>Egyedi szám</th>
    <th>Sorszám</th>
    <th>Érkezett</th>
    <th>Állapotfelvétel</th>
    <th>Végátvétel</th>
    <th>Hazaadás</th>
    <th>Számlázás</th>
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
      <td class='tdc5p'>$row[7]</td>
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
