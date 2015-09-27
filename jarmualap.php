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
