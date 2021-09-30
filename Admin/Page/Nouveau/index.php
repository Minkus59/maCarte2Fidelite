<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/log.inc.php"); 

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];
$Now=time();

$SelectPage=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page");
$SelectPage->execute();

if (isset($_GET['id'])) { 
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE id=:id");
    $Select->BindParam(":id", $Id, PDO::PARAM_STR);
    $Select->execute();
    $Actu=$Select->fetch(PDO::FETCH_OBJ);
}

if ((isset($_POST['Modifier']))&&(isset($_GET['id']))) {
     
    $Titre=$_POST['titre'];
    $Description=$_POST['description'];
    $Position=$_POST['position'];
    $Libele=$_POST['libele'];

    $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_Page SET position=:position, libele=:libele ,titre=:titre, description=:description WHERE id=:id");
    $Insert->BindParam(":id", $Id, PDO::PARAM_STR);
    $Insert->BindParam(":position", $Position, PDO::PARAM_STR);
    $Insert->BindParam(":libele", $Libele, PDO::PARAM_STR);
    $Insert->BindParam(":titre", $Titre, PDO::PARAM_STR);
    $Insert->BindParam(":description", $Description, PDO::PARAM_STR);   
    $Insert->execute();

    if (!$Insert) {
        $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
        ErreurLog($Erreur);
    }
    else  {     
        $Valid="Page modifier avec succès";
        header('location:'.$Home.'/Admin/Page/Nouveau/?id='.$Id.'&valid='.urlencode($Valid));
    }
} 

if ((isset($_POST['Ajouter']))&&(!isset($_GET['id']))) {
    $Libele=$_POST['libele'];
    $Page=$_POST['page'];
    
    $Lien = preg_replace('#Ç#', 'C', $Libele);
    $Lien = preg_replace('#ç#', 'c', $Lien);
    $Lien = preg_replace('#è|é|ê|ë#', 'e', $Lien);
    $Lien = preg_replace('#È|É|Ê|Ë#', 'E', $Lien);
    $Lien = preg_replace('#à|á|â|ã|ä|å#', 'a', $Lien);
    $Lien = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $Lien);
    $Lien = preg_replace('#ì|í|î|ï#', 'i', $Lien);
    $Lien = preg_replace('#Ì|Í|Î|Ï#', 'I', $Lien);
    $Lien = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $Lien);
    $Lien = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $Lien);
    $Lien = preg_replace('#ù|ú|û|ü#', 'u', $Lien);
    $Lien = preg_replace('#Ù|Ú|Û|Ü#', 'U', $Lien);
    $Lien = preg_replace('#ý|ÿ#', 'y', $Lien);
    $Lien = preg_replace('#Ý#', 'Y', $Lien);
    $Lien = preg_replace('# #', '-', $Lien);
       
    if (strlen(trim($Libele))<=2) {
        $Erreur="Veuillez saisir un nom de page !";
        ErreurLog($Erreur);
    }
    else {
         //verifier si 1er Page sinon position +1
         $Fichier=$_SERVER['DOCUMENT_ROOT']."/index.zip";
         
         if ($Page!="") {
             $Destination=$_SERVER['DOCUMENT_ROOT'].$Page.$Lien;
             
             $Verif=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu=='1'");
             $Verif->BindParam(":parrin", $Page, PDO::PARAM_STR);  
             $Verif->execute();
             $NbPage=$Verif->rowCount();
         }
         else {
             //$Lien2 = preg_replace("/\//", "", $Lien);
             $Destination=$_SERVER['DOCUMENT_ROOT']."/".$Lien;
             
             $Verif=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE statue!='2' AND sous_menu!='1'");
             $Verif->execute();
             $NbPage=$Verif->rowCount();
         }
        
        if (!mkdir($Destination, 0777, true)) {
            $Erreur="Echec lors de la création du répertoire";
            ErreurLog($Erreur);
        }
        else {
            $zip = new ZipArchive;
            
            if ($zip->open($Fichier) === TRUE) {
                $zip->extractTo($Destination);
                $zip->close();
                
                if ($NbPage!=0) {
                    //Si autre page on incremente la position de la page
                    $Position=$NbPage+1;
                    
                    if ($Page!="") {
                        $Lien=$Page.$Lien."/";
                        //Dans un sous dossier
                        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Page (sous_menu, parrin, position, libele, lien, created) VALUES('1', :parrin, :position, :libele, :lien, :created)");
                        $Insert->BindParam(":position", $Position, PDO::PARAM_STR);
                        $Insert->BindParam(":libele", $Libele, PDO::PARAM_STR);  
                        $Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);    
                        $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                        $Insert->BindParam(":parrin", $Page, PDO::PARAM_STR);
                        $Insert->execute();
                    }
                    else {
                        //A la racine
                        $Lien="/".$Lien."/";
                        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Page (position, libele, lien, created) VALUES(:position, :libele, :lien, :created)");
                        $Insert->BindParam(":position", $Position, PDO::PARAM_STR);
                        $Insert->BindParam(":libele", $Libele, PDO::PARAM_STR);     
                        $Insert->BindParam(":lien", $Lien, PDO::PARAM_STR); 
                        $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                        $Insert->execute();
                    }
                }
                else {
                    //Si 1ere page
                    if ($Page!="") {
                        //Dans un sous dossier
                        $Lien=$Page.$Lien."/";
                        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Page (sous_menu, parrin, libele, lien, created) VALUES('1', :parrin, :libele, :lien, :created)");
                        $Insert->BindParam(":libele", $Libele, PDO::PARAM_STR); 
                        $Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);     
                        $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                        $Insert->BindParam(":parrin", $Page, PDO::PARAM_STR);
                        $Insert->execute();
                    }
                    else {
                        //A la racine
                        $Lien="/".$Lien."/";
                        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Page (libele, lien, created) VALUES(:libele, :lien, :created)");
                        $Insert->BindParam(":libele", $Libele, PDO::PARAM_STR); 
                        $Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);    
                        $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                        $Insert->execute();
                    }
                }

                if ($Insert==false) {
                    $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
                    ErreurLog($Erreur);
                }
                else  {
                    $Valid="Page ajouter avec succès";
                    header('location:'.$Home.'/Admin/Page/?valid='.urlencode($Valid));
                }
            }
            else {
                rmdir($Destination);
                $Erreur="Echec lors de la création du fichier";
                ErreurLog($Erreur);
            }
        }
    }
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

<?php if (isset($_GET['id'])) { ?>
      <H1>Modifier une page</H1><BR /> <?php
} else { ?>
  <H1>Ajouter une nouvelle page</H1><BR /> <?php
} ?>

<div id="Gauche">

<form name="form_actu" action="" method="POST" enctype="multipart/form-data">

<?php if (!isset($_GET['id'])) { ?>
<select name="page">     
<option value="" <?php if ($_SESSION['StatuePage']==$Home."/") { echo "selected"; } ?>>Racine</option>

<?php while ($Page=$SelectPage->fetch(PDO::FETCH_OBJ)) { ?>
<option value='<?php echo $Page->lien; ?>' ><?php echo $Page->libele; ?></option>
<?php } ?>
</select><BR /><BR />

<input type="text" placeholder="Libelé du bouton" name="libele" require="required" value="<?php echo $Actu->libele; ?>"><BR /><BR />

<?php }
if (isset($_GET['id'])) { ?>
      <input type="text" placeholder="Position dans le menu" name="position" require="required" value="<?php echo $Actu->position; ?>"><BR /><BR />
      <input type="text" placeholder="Libelé du bouton" name="libele" require="required" value="<?php echo $Actu->libele; ?>"><BR /><BR />
      <input type="text" maxlength="70" placeholder="Titre de la page" name="titre" value="<?php echo $Actu->titre; ?>"><BR /><BR />
      <input type="text" maxlength="170" placeholder="Description de la page" name="description"value="<?php echo $Actu->description; ?>"><BR /><BR />
      
      <input type="submit" name="Modifier" value="Modifier"/>
<?php } 
else { ?>
    <input type="submit" name="Ajouter" value="Ajouter"/>
    <?php } ?>
</form>
<p><font color='#FF0000'>*</font> Champ de saisie requis<BR /><BR />

</div>
<div id="Droite">
<div id="TitreGoogle"> 
    <?php echo $Actu->titre; ?>
</div>
<div id="SiteGoogle"> 
    <?php echo $Home."/".$Actu->nom."/"; ?>
</div>
<div id="DescriptionGoogle"> 
    <?php echo $Actu->description; ?>
</div>

</div>

</article>
</section>
</div>
</CENTER>
</body>

</html>