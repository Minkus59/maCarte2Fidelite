<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Id=$_GET['id'];

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {
    
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE id=:id AND statue!='2'");
    $Select->BindParam(":id", $Id, PDO::PARAM_STR);
    $Select->execute();
    $Actu=$Select->fetch(PDO::FETCH_OBJ);
    $NbCount=$Select->rowCount();
    
    if ($NbCount!=0) {
        if(!unlink($_SERVER['DOCUMENT_ROOT']."/".$Actu->lien."/index.php")) {
            $Erreur="Echec lors de la suppression";
        }
        elseif(!rmdir($_SERVER['DOCUMENT_ROOT']."/".$Actu->lien)) {
            $Erreur="Echec lors de la suppression";
        } 
        else {
            $deleteActu=$cnx->prepare("DELETE FROM ".$Prefix."neuro_Page WHERE id=:id AND statue!='2'");
            $deleteActu->bindParam(':id', $Id, PDO::PARAM_INT);
            $deleteActu->execute();

            header('location:'.$Home.'/Admin/Page/');
        }
    }
    else {
        header('location:'.$Home.'/Admin/Page/');
    }
}

if ((!empty($_GET['id']))&&(isset($_POST['non']))) {  
    header('location:'.$Home.'/Admin/Page/');
}
?>  


<!-- ************************************
*** Script réalisé par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>


<title>NeuroSoft Team - Accès PRO</title>
  
<meta name="robots" content="noindex, nofollow">

<meta name="author".content="NeuroSoft Team">
<meta name="publisher".content="Helinckx Michael">
<meta name="reply-to" content="contact@neuro-soft.fr">

<meta name="viewport" content="width=device-width" >                                                            

<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico">

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpapc.css" >
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

Etes-vous sur de vouloir supprimer cette Page ? </p>

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