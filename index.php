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
$h->header("Járművek");

// Fömenü
// 1. sor menü
$menu = array (
'Járművek alapadatai'=>'jarmualap.php',
'2015. évi szeptemberi Volvok'=>'volvo201509.php'
);
$h->btnMenu($menu);
$h->separator();
if (isset($_SESSION['userAuth']) && $_SESSION['userAuth'] >=8) {
// 2. sor menü
$menu = array (
'Új jármű rögzítése'=>'ujjarmu.php',
);
$h->btnMenu($menu);
}
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
