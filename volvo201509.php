<?php

try {
  if(file_exists('inc/pg-init.php')) {
    require_once 'inc/pg-init.php';
  }
  else {
    throw new Exception("Adatbázis konfigurácós fájl hiányzik!");
  }
  if (file_exists('inc/session-timeout.php')) {
      require_once 'inc/session-timeout.php';
      session_start();
  }
  else {
    throw new Exception("Munkamenet konfigurácós fájl hiányzik!");
  }
  if (file_exists('class/class.Html.php')) {
    require_once 'class/class.Html.php';
  }
  else {
    throw new Exception ("A megjelenítés konfigurációs fájl hiányzik!");
  }
  
}
catch (Exception $e) {
  echo "<!doctype html>
  <!doctype html>
  <html lang=hu>
  <head>
    <meta charset='utf-8' />
    <link rel='stylesheet' href='css/jarmuvek.css' media='screen, print'/>
    <link rel='shortcut icon' href='icon/favicon.ico' type='image/x-icon'>
    <link rel='icon' href='icon/favicon.ico' type='image/x-icon'>
    <meta name=viewport content='width=device-width, initial-scale=1'>
    <title>Hiba!</title>
    <body>
      <h1 style='background-color:#FF928D;color:red;width:50%;text-align:center;margin:5em auto;'>";
        echo $e->getMessage();
echo "
      </h1>
    </body>
    </html>
  ";
  exit;
}
 


// Kimenet tárazása
ob_start();

// Csatlakozás az adatbázishoz.


// Html példány:
$h = new Html();

// Html fejléc
$h->header("2015. szeptember - Volvo");

$h->msgOk("Volvo dátumok");

$h->btnMenu(array("Átfutási idők - diagram"=>"volvo201509diagram.php",
                   "Vissza a főmenühöz"=>"index.php"));

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
