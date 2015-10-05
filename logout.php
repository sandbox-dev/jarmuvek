<?php
require_once 'autoloader.php';
new Autoloader();
$pg = Pg::getPg();

// Html példány:
$h = new Html();

// Html fejléc
$h->header("Járművek - Kijelentkezés");

// Kijelentkezés megerősítése rendben?
if (isset($_GET['a'])) {
  session_destroy();
  $h->msgOk("A kijelentkezés megtörtént, a munkamenet lezárásra került.");
  $h->btnBack();
  $h->btnExit();
  $h->abortFooter();
}


$h->msgWarn("Kijelentkezés megerősítése");
echo "<p class='btn'>
<a class='btn-exit' href='logout.php?a=1'>Kijelentkezés</a>
&nbsp;
<a class='btn-back' href='index.php'>Mégsem jelentkezem ki, vissza a kezdő oldalra</a>
</p>";


// Html lábléc
$h->separator();
$h->btnBack();
$h->footer();

// Kimenet kiírása

?>
