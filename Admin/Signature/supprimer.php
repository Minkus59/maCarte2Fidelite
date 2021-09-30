<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Id=$_GET['id'];

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {
    
    $SelectArchive = $cnx -> prepare("SELECT * FROM ".$Prefix."neuro_Signature WHERE id=:id");
    $SelectArchive-> BindParam(":id", $Id, PDO::PARAM_STR);
    $SelectArchive-> execute(); 
    $ArchiveInfo=$SelectArchive->fetch(PDO::FETCH_OBJ);
    
    unlink($_SERVER['DOCUMENT_ROOT']."/lib/Document/Signer/".$ArchiveInfo->fichier);
    unlink($_SERVER['DOCUMENT_ROOT']."/lib/Document/Original/".$ArchiveInfo->fichier);

    $Suppr=$cnx->prepare("DELETE FROM ".$Prefix."neuro_Signature WHERE id=:id");
    $Suppr->bindParam(':id', $Id, PDO::PARAM_INT);
    $Suppr->execute();

    header('Location:'.$Home.'/Admin/Signature/');
}

if ((!empty($_GET['id']))&&(isset($_POST['non']))) {  
    header('Location:'.$Home.'/Admin/Signature/');
}
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

Etes-vous sur de vouloir supprimer ces documents ? </p>

<TABLE width="300">
<form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR>
</form></TABLE>

</article>
</section>
</div>
</CENTER>
</body>

</html>