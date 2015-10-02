<?php
try {
  if(file_exists('inc/session-timeout.php')) {
    require_once 'inc/session-timeout.php';
    session_start();
  }
  else {
    throw new Exception("Munkamenet konfigurácós fájl hiányzik!");
  }
  if (file_exists('inc/pg-init.php')) {
    require_once 'inc/pg-init.php';
  }
  else {
    throw new Exception("Adatbázis konfigurácós fájl hiányzik!");
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
<title>Hiba!$title</title>
<body>
<h1 style='background-color:#FF928D;color:red;width:33%;text-align:center;margin:15em auto;'>
$e->getMessage();
</h1>
</body>
</html>
";
exit;
}

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
            if ($i <= $plannedTime) {
              echo "<b style='color:#007D00;font-size:75%;'>&diams;</b>";
            }
            if ($i > $plannedTime and $i <= $avgTime) {
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

