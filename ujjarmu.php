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
$h->header("Új jármű rögzítése");

$h->msgOk("Új jármű rögzítése");
if (!isset($_SESSION['userAuth']) || $_SESSION['userAuth'] < 8) {
  $h->msgErr("A funkció eléréséhez be kell jelentkeznie, vagy a jogosultsága nem elegendő a használathoz!");
  $h->btnBack();
  echo "<p class='btn'><a class='btn-login' href='login.php'>Bejelentkezés</a></p>";
  $h->abortFooter();
}

// Úrlap feldolgozása
if (isset($_POST['submit']) && $_POST['submit'] == 'Mentés') {
  $sql  = "insert into uj_jarmu_alap(jarmutipus,ev,sorszam,psz,esz,sd_al,sd_op,";
  $sql .= "pp_al,pp_op,erkezett,munkabavetel,allapotfelvetel,reszatvetel,";
  $sql .= "vegatvetel,hazaadas,szamlazas,megjegyzes)";
  $sql .= "values('$_POST[jarmutipus]',$_POST[ev],$_POST[sorszam],'$_POST[psz]',";
  ($_POST['esz'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[esz]',";
  ($_POST['sd_al'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[sd_al]',";
  ($_POST['sd_op'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[sd_op]',";
  ($_POST['pp_al'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[pp_al]',";
  ($_POST['pp_op'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[pp_op]',";
  ($_POST['erkezett'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[erkezett]',";
  ($_POST['munkabavetel'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[munkabavetel]',";
  ($_POST['allapotfelvetel'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[allapotfelvetel]',";
  ($_POST['reszatvetel'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[reszatvetel]',";
  ($_POST['vegatvetel'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[vegatvetel]',";
  ($_POST['hazaadas'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[hazaadas]',";
  ($_POST['szamlazas'] == '') ? $sql .= "NULL," : $sql .= "'$_POST[szamlazas]',";
  ($_POST['megjegyzes'] == '') ? $sql .= "NULL" : $sql .= "'$_POST[megjegyzes]'";
  $sql .= ")";
  $res = $pg->exec($sql);
  if ($res) {
    $h->msgOk("$_POST[psz] pályaszámú/rendszámú jármű rögzítve.");
  }
  else {
    $h->msgErr("$_POST[psz] pályaszámú/rendszámú jármű rögzítése nem sikerült!");
  }
  // 
  $menu = array (
  'Új jármű rögzítése'=>'ujjarmu.php',
  'Vissza'=>'index.php'
  );
  $h->btnMenu($menu);
  $h->abortFooter();
}


// Úrlap feldolgozás vége

echo "<datalist id='tipusok'>";
$sql = "select * from tipus order by 1";
$res = $pg->query($sql);
while ($row = $res->fetch(PDO::FETCH_BOTH)) {
    echo '<option value="'.$row[0].'">';
}
echo "</datalist>";

echo "
<form method='post' action='ujjarmu.php'>
<table class='tbl-ujjmu' border='1'>
<caption>Új jármű adatai</caption>
<tr>
<td class='tdr'><label for='jarmutipus'>Járműtípus:</td>
<td><input list='tipusok' name='jarmutipus' required placeholder='Válasszon a listából!' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='ev'>Év:</td>
<td><input type='number' min='2014' step='1' name='ev' required value='2015'/>
</label></td>
</tr>
<tr>
<td class='tdr'><label for='sorszam'>Éven és típuson belüli sorszám:</td>
<td><input type='number' min='1' step='1' name='sorszam' required />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='psz'>Pályaszám/rendszám:</td> 
<td><input type='text' name='psz' maxlength='10' required />
</label></td></td>
</tr>
<tr>
<td class='tdr'><label for='esz'>Egyedi szám:</td>
<td><input type='text' name='esz' maxlength='10' placeholder='Nem kötelező!' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='sd_al'>SD rendelés alapjavítás:</td>
<td><input type='text' name='sd_al' maxlength='8' placeholder='Nem kötelező!' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='sd_op'>SD rendelés opció:</td>
<td><input type='text' name='sd_op' maxlength='8' placeholder='Nem kötelező!' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='pp_al'>PP rendelés alapjavítás:</td>
<td><input type='text' name='pp_al' maxlength='8' placeholder='Nem kötelező!' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='pp_op'>PP rendelés opció:</td>
<td><input type='text' name='pp_op' maxlength='8' placeholder='Nem kötelező!' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='erkezett'>Beérkezés dátuma:</td>
<td><input type='date' name='erkezett' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='munkabavetel'>Munkábavétel dátuma:</td>
<td><input type='date' name='munkabavetel' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='allapotfelvetel'>Állapotfelvétel dátuma:</td>
<td><input type='date' name='allapotfelvetel' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='reszatvetel'>Részátvétel dátuma:</td>
<td><input type='date' name='reszatvetel' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='vegatvetel'>Végátvétel dátuma:</td>
<td><input type='date' name='vegatvetel' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='hazaadas'>Hazaadás dátuma:</td>
<td><input type='date' name='hazaadas' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='szamlazas'>Végszámla dátuma:</td>
<td><input type='date' name='szamlazas' />
</label></td>
</tr>
<tr>
<td class='tdr'><label for='megjegyzes'>Megjegyzés:</td>
<td><textarea name='megjegyzes' cols=40' rows='4'></textarea></td>
</label></td>
</tr>
<tr>
<td class='tdc'><input type='submit' name='submit' value='Mentés' /> &nbsp;</td>
<td class='tdc'><input type='reset' name='reset' value='Adatok törlése' /></td>
</tr>
</table>
</form>
";





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
