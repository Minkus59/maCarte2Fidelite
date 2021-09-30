<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET[erreur];
$Valid=$_GET['valid'];

$SelectFichier=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Signature ORDER BY id DESC");
$SelectFichier->execute();

$repInt=$_SERVER['DOCUMENT_ROOT']."/lib/Document/Original/";
$repExt=$Home."/lib/Document/Original/";
$repIntSigner=$_SERVER['DOCUMENT_ROOT']."/lib/Document/Signer/";
$repExtSigner=$Home."/lib/Document/Signer/";

?>

<!-- ************************************
*** Script réalisé par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>
<meta charset="ISO-8859-15">

<title>NeuroSoft Team - Accès PRO</title>

<META name="robots" content="noindex, nofollow" />

<meta name="author" content="NeuroSoft Team" />
<meta name="publisher" content="Helinckx Michael" />
<meta name="reply-to" content="contact@neuro-soft.fr" />

<META name="viewport" content="width=device-width" />                                                            

<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico" />

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpapc.css" />
</head>

<body>
<header>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>
</header>

<section>
    
<nav>
<div id="MenuGauche">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>
</div>
</nav>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

<H1>Signer un document</H1></p>

<form name="form_fichier" action="<?php echo $Home; ?>/Admin/Signature/Ajustement/" method="POST" enctype="multipart/form-data">

<input type="file" placeholder="fichier" name="fichier" required="required"/><span class="col_3"><img src="<?php echo $Home; ?>/Admin/lib/img/intero.png" alt="Information" title="Fichier au format (.pdf)" /></span>
</p>
<span class="col_1" >Type :  </span>
<select name="type" required="required"><option value="">-- Type --</option>
<option value="Signature">Signature</option>
<option value="Tampon">Tampon</option>
</select>
</p>
<span class="col_1" >Page à signer :  </span>
<select name="page" required="required"><option value="">-- Page --</option>
<option value="All">Tous</option>
<option value="Last">Dernière</option>
</select>
</p>
<span class="col_1" >Horizontal : </span>
<input type="range" name="horizontal" min="0" max="130" value="<?php echo $Horizontal; ?>" />
</p>
<span class="col_1" >Vertical :  </span>
<input type="range" orient="vertical" name="vertical" min="0" max="255" value="<?php echo $Vertical; ?>" />
</p>
<p><input type="submit" name="Signer" value="Signer"/>
</form></p>

<H1>Liste des documents traité</H1></p>

<table>
<tr><th>Original</th><th>Signer</th><th>Date</th><th>Action</th></tr>

<?php
while ($Fichier=$SelectFichier->fetch(PDO::FETCH_OBJ)) {
?>
   <tr>
   <td><a target="_blank" href="<?php echo $repExt.$Fichier->fichier; ?>"><?php echo $Fichier->fichier; ?></a></td>
   <td><a target="_blank" href="<?php echo $repExtSigner.$Fichier->fichier; ?>"><?php echo $Fichier->fichier; ?></a></td>
   <td><?php echo date("d-m-Y / G:i:s", $Fichier->created); ?></td>
   <td><a href="<?php echo $Home; ?>/Admin/Signature/supprimer.php?id=<?php echo $Fichier->id; ?>"><img src="<?php echo $Home; ?>/Admin/lib/img/supprimer.png"></a></td>
   </tr>
<?php
}
?>
</table>

</article>
</section>
</div>
</CENTER>
</body>

</html>