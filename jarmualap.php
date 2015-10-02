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
$h->header("Járművek alapadatai");

// Ha volt GET kérés
if(isset($_GET['inf']) && trim($_GET['inf']) != '') {
    $id = $_GET['inf'];
    // GET számot kapott
    if(is_numeric($id)) {
        $sql =  "select jarmutipus,ev,sorszam,psz,sd_rendeles,pp_rendeles,";
        $sql .= "erkezett,munkabavetel,allapotfelvetel,reszatvetel,vegatvetel,hazaadas,";
        $sql .= "vegatvetel-erkezett as atf1, hazaadas-erkezett as atf2 from jarmu_alap where id=$id";
        $res = $pg->query($sql);
        // Van-e visszaadott sor
        $count = $res->rowCount();
        if ($res) {
            if ($count) {
                $row = $res->fetch(PDO::FETCH_BOTH);
                $h->msgOk("$row[3]&nbsp;&mdash;&nbsp;$row[0] alapadatok:");
                echo "<table class='jmu-info-table' border=1 style='border-collapse:collapse'>
                <tr><td class='tdr5p-b'>Járműtípus:</td><td class='tdc5p'>$row[0]</td></tr>
                <tr><td class='tdr5p-b'>Év:</td><td class='tdc5p'>$row[1]</td></tr>
                <tr><td class='tdr5p-b'>Sorszám:</td><td class='tdc5p'>$row[2]</td></tr>
                <tr><td class='tdr5p-b'>Pályaszám:</td><td class='tdc5p'>$row[3]</td></tr>
                <tr><td class='tdr5p-b'>SD rendelés:</td><td class='tdc5p'>$row[4]</td></tr>
                <tr><td class='tdr5p-b'>PP rendelés:</td><td class='tdc5p'>$row[5]</td></tr>
                <tr><td class='tdr5p-b'>Beérkezés:</td><td class='tdc5p'>$row[6]</td></tr>
                <tr><td class='tdr5p-b'>Munkábavétel:</td><td class='tdc5p'>$row[7]</td></tr>
                <tr><td class='tdr5p-b'>Állapotfelvétel:</td><td class='tdc5p'>$row[8]</td></tr>
                <tr><td class='tdr5p-b'>Részátvétel<code class='code14'> (2015. évi Volvo - átvétel)</code>:</td><td class='tdc5p'>$row[9]</td></tr>
                <tr><td class='tdr5p-b'>Végátvétel<code class='code14'> (2015. évi Volvo - hazaadás)</code>:</td><td class='tdc5p'>$row[10]</td></tr>
                <tr><td class='tdr5p-b'>Hazaadás<code class='code14'> (2015. évi Volvo - számla kelte)</code>:</td><td class='tdc5p'>$row[11]</td></tr>
                <tr><td class='tdr5p-b'>Érkezéstől végátvételig [nap]:</td><td class='tdc5p'>$row[12]</td></tr>
                <tr><td class='tdr5p-b'>Érkezéstől hazaadásig [nap]:</td><td class='tdc5p'>$row[13]</td></tr>
                </table>";
                echo "<p>";
                  $h->btnMenu(array("Új jármű választás"=>"jarmualap.php"));
                echo "</p>";
            }
            else {
                $h->msgWarn("A kérés nem hozott eredményt!");
                
            }
        }
    }
    else {
        $h->msgErr("Érvénytelen kérés!");
    }
} // volt küldött kérés
else {
  $h->msgWarn("Válasszon járművet!");
  $sql = "select id,ev,jarmutipus,sorszam,psz from jarmu_alap order by 2,3,4";
  $res = $pg->query($sql);
  echo "<form method='get'>";
  echo "<label for='inf'><b>Válasszon ki egy járművet: </b>";
  echo "<select name='inf' onchange='this.form.submit();'>";
  while($row=$res->fetch(PDO::FETCH_BOTH)) {
      echo "<option value=$row[0]>$row[1].&nbsp;$row[2]&nbsp;$row[3].&nbsp;$row[4]</option>";
  }
  echo "</select>";
  echo "&nbsp;<input type='submit' name='ok' value='Rendben' />";
  echo "</label>";
  echo "</form>";
  
}
// Oldal alja gombok:
$h->separator();
$h->btnBack();
$h->btnExit();
$h->btnPrint();
$h->footer();
?>
