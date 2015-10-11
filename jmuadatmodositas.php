<?php
session_start();
require_once 'autoloader.php';
new Autoloader();
$pg = Pg::getPg();

// Kimenet tárazása
ob_start();

if(!isset($_SESSION['backRef'])) {
  $_SESSION['backRef'] = "index.php";
}

// Html példány:
$h = new Html();
// vissza link
//$h->setBackRef();


// Html fejléc
$h->header("Új jármű rögzítése");

$h->msgOk("Új jármű rögzítése");
if (!isset($_SESSION['userAuth']) || $_SESSION['userAuth'] < 8) {
  $h->msgErr("A funkció eléréséhez be kell jelentkeznie, vagy a jogosultsága nem elegendő a használathoz!");
  $h->btnBack();
  echo "<p class='btn'><a class='btn-login' href='login.php'>Bejelentkezés</a></p>";
  $h->abortFooter();
}

$id=null;

// get kérés feldolgozása
if(isset($_GET['id']) && $_GET['id'] != ''){
  $id = $_GET['id'];
  if(is_numeric($id)) {
    $sql = "select * from jarmu_alap where id=$id";
    $res = $pg->query($sql);
    $count = $res->rowCount();
    if ($res) {
      if($count) {
        $row = $res->fetch(PDO::FETCH_BOTH);
        //print_r($row);
      } // volt eredmény     
    } // jó volt a kérés
  } // a get kérés szám volt
} // volt get kérés
//else {
  //$h->msgWarn("A kérés nem hozott eredményt!");
  //$h->abortFooter();
  
//}

// Úrlap feldolgozása
if (isset($_POST['submit']) && $_POST['submit'] != '') {
  $sql  = "update jarmu_alap set ";
  $sql .= "jarmutipus = '$_POST[jarmutipus]', ev=$_POST[ev], sorszam=$_POST[sorszam],psz='$_POST[psz]',";
  ($_POST['esz'] == '') ? $sql .= "esz=NULL," : $sql .= "esz='$_POST[esz]',";
  ($_POST['sd_al'] == '') ? $sql .= "sd_al=NULL," : $sql .= "sd_al='$_POST[sd_al]',";
  ($_POST['sd_op'] == '') ? $sql .= "sd_op=NULL," : $sql .= "sd_op='$_POST[sd_op]',";
  ($_POST['pp_al'] == '') ? $sql .= "pp_al=NULL," : $sql .= "pp_al='$_POST[pp_al]',";
  ($_POST['pp_op'] == '') ? $sql .= "pp_op=NULL," : $sql .= "pp_op='$_POST[pp_op]',";
  ($_POST['erkezett'] == '') ? $sql .= "erkezett=NULL," : $sql .= "erkezett='$_POST[erkezett]',";
  ($_POST['munkabavetel'] == '') ? $sql .= "munkabavetel=NULL," : $sql .= "munkabavetel='$_POST[munkabavetel]',";
  ($_POST['allapotfelvetel'] == '') ? $sql .= "allapotfelvetel=NULL," : $sql .= "allapotfelvetel='$_POST[allapotfelvetel]',";
  ($_POST['reszatvetel'] == '') ? $sql .= "reszatvetel=NULL," : $sql .= "reszatvetel='$_POST[reszatvetel]',";
  ($_POST['vegatvetel'] == '') ? $sql .= "vegatvetel=NULL," : $sql .= "vegatvetel='$_POST[vegatvetel]',";
  ($_POST['hazaadas'] == '') ? $sql .= "hazaadas=NULL," : $sql .= "hazaadas='$_POST[hazaadas]',";
  ($_POST['szamlazas'] == '') ? $sql .= "szamlazas=NULL," : $sql .= "szamlazas='$_POST[szamlazas]',";
  ($_POST['megjegyzes'] == '') ? $sql .= "megjegyzes=NULL," : $sql .= "megjegyzes='$_POST[megjegyzes]',";
  ($_POST['tervido'] == '') ? $sql .= "terv_atfutasi_ido=NULL" : $sql .= "terv_atfutasi_ido=$_POST[tervido]";
  $sql .= " where id=$_POST[id];";
  
  $res = $pg->exec($sql);
  if ($res) {
    $h->msgOk("$_POST[psz] pályaszámú/rendszámú jármű adatainak módosítása sikerült.");
  }
  else {
    $h->msgErr("$_POST[psz] pályaszámú/rendszámú jármű adatainak módosítása nem sikerült!");
  }
  // 
  $menu = array (
  'Vissza'=>"$_SESSION[backRef]"
  );
  $h->btnMenu($menu);
  $h->btnMenu(array("Vissza a főmenübe"=>"index.php"));
  $h->abortFooter();
}


// Úrlap feldolgozás vége

echo "<datalist id='tipusok'>";
$sql = "select * from tipus order by 1";
$res = $pg->query($sql);
while ($list = $res->fetch(PDO::FETCH_BOTH)) {
    echo '<option value="'.$list[0].'">';
}
echo "</datalist>";

echo "
<form method='post' action='jmuadatmodositas.php'>
<table class='tbl-ujjmu' border='1'>
<caption><b>A kiválasztott jármű adatainak módosítása</b><br>";
$h->btnMenu(array("Vissza"=>"$_SESSION[backRef]"));
echo "</caption>
<tr>
<td class='tdrb'><label for='jarmutipus'>Járműtípus</td>
<td><input list='tipusok' name='jarmutipus' required placeholder='Válasszon a listából!' value='$row[0]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='ev'>Év</td>
<td><input type='number' min='2014' step='1' name='ev' required value='$row[1]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='sorszam'>Éven és típuson belüli sorszám</td>
<td><input type='number' min='1' step='1' name='sorszam' required value='$row[2]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='psz'>Pályaszám/rendszám</td> 
<td><input type='text' name='psz' maxlength='10' required value='$row[3]' />
</label></td></td>
</tr>
<tr>
<td class='tdrb'><label for='esz'>Egyedi szám</td>
<td><input type='text' name='esz' maxlength='10' placeholder='Nem kötelező!' value='$row[4]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='sd_al'>SD rendelés alapjavítás</td>
<td><input type='text' name='sd_al' maxlength='8' placeholder='Nem kötelező!' value='$row[5]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='sd_op'>SD rendelés opció</td>
<td><input type='text' name='sd_op' maxlength='8' placeholder='Nem kötelező!' value='$row[6]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='pp_al'>PP rendelés alapjavítás</td>
<td><input type='text' name='pp_al' maxlength='8' placeholder='Nem kötelező!' value='$row[7]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='pp_op'>PP rendelés opció</td>
<td><input type='text' name='pp_op' maxlength='8' placeholder='Nem kötelező!' value='$row[8]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='erkezett'>Beérkezés dátuma</td>
<td><input type='date' name='erkezett' value='$row[9]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='munkabavetel'>Munkábavétel dátuma</td>
<td><input type='date' name='munkabavetel' value='$row[10]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='allapotfelvetel'>Állapotfelvétel dátuma</td>
<td><input type='date' name='allapotfelvetel' value='$row[11]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='reszatvetel'>Részátvétel dátuma</td>
<td><input type='date' name='reszatvetel' value='$row[12]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='vegatvetel'>Végátvétel dátuma</td>
<td><input type='date' name='vegatvetel' value='$row[13]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='hazaadas'>Hazaadás dátuma</td>
<td><input type='date' name='hazaadas' value='$row[14]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='szamlazas'>Végszámla dátuma</td>
<td><input type='date' name='szamlazas' value='$row[15]' />
</label></td>
</tr>
<tr>
<tr>
<td class='tdrb'><label for='tervido'>Tervezett átfutási idő [nap]</td>
<td><input type='number' name='tervido' min='1' value='$row[17]' />
</label></td>
</tr>
<tr>
<td class='tdrb'><label for='megjegyzes'>Megjegyzés</td>
<td><textarea name='megjegyzes' cols=40' rows='4'>$row[16]</textarea></td>
</label></td>
</tr>
<tr>
<td class='tdrb'>Adatok módosítása / Mégsem</td>
<td class='tdc'>
  <input style='font-size:1em;' type='submit' name='submit' value='Mentés' />
  <input style='font-size:1em;background-color:yellow;' type='button' name='back' 
    value='Mégsem' onclick='toIndex();' />
</td>
</tr>
</table>
  <input type='hidden' name='id' value='$id' />
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
