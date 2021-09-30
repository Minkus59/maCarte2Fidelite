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
$Now=time();

$chemin = $_FILES['photo']['name'];
$ext = array('.jpeg', '.JPEG', '.jpg', '.JPG', '.png', '.PNG');
$ext1 = array('.jpeg', '.JPEG', '.jpg', '.JPG');
$ext2 = array('.png', '.PNG');
$ext_origin = strchr($chemin, '.');

if ((isset($_POST['Enregistrer']))&&(in_array($ext_origin, $ext))) {

// Upload d'image
$rep = $_SERVER['DOCUMENT_ROOT']."/lib/logo/";
$fichier = basename($chemin);
$taille_origin = filesize($_FILES['photo']['tmp_name']);
$hash = md5(uniqid(rand(), true));
$Chemin_upload = $Home."/lib/logo/".$hash.$fichier."";
$TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
$taille_max = 10000000;
$Default=$Home."/lib/logo/logoType.png";

    if($taille_origin>$taille_max){
        $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 2800px sur 1800px de hauteur";
    }
    if (!isset($Erreur)){       
      //si largeur + grande

      $NouvelleLargeur_photo = 190;
      $NouvelleHauteur_photo = ( ($TailleImageChoisie[1] * (($NouvelleLargeur_photo)/$TailleImageChoisie[0])) );     


  if (in_array($ext_origin, $ext1)) {
 
        $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE WHERE id='1'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($rep.basename($Param->logo));
                }
                
                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='1'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !</p>";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }   
        else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
    if (in_array($ext_origin, $ext2)) {   
        $ImageChoisie_photo = imagecreatefrompng($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagealphablending($NouvelleImage_photo, false);
        imagesavealpha($NouvelleImage_photo, true);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagepng($NouvelleImage_photo , $rep.$hash.$fichier, 0)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='1'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($rep.basename($Param->logo));
                }

                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='1'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !</p>";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }
    else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
  }
}

if (isset($_POST['Reset'])) {
  
    $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='1'");
    $SelectParam->execute();
    $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

    if($Param->logo!=$Home."/lib/logo/logoType.png") {
      unlink($rep.basename($Param->logo));
    }
    
    $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='1'");
    $Insertlogo->bindParam(':photo', $Default, PDO::PARAM_STR);
    $Insertlogo->execute();

    $Valid="Logo ajouté avec succès !</p>";
    header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
}

if ((isset($_POST['Enregistrer2']))&&(in_array($ext_origin, $ext))) {

// Upload d'image
$rep = $_SERVER['DOCUMENT_ROOT']."/lib/header/";
$fichier = basename($chemin);
$taille_origin = filesize($_FILES['photo']['tmp_name']);
$hash = md5(uniqid(rand(), true));
$Chemin_upload = $Home."/lib/header/".$hash.$fichier."";
$TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
$taille_max = 10000000;
$Default=$Home."/lib/header/headerType.png";

    if($taille_origin>$taille_max){
        $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 2800px sur 1800px de hauteur";
        ErreurLog($Erreur);
    }
    if (!isset($Erreur)){       
      //si largeur + grande

      $NouvelleHauteur_photo = 80;
      $NouvelleLargeur_photo = ( ($TailleImageChoisie[0] * (($NouvelleHauteur_photo)/$TailleImageChoisie[1])) );   

  if (in_array($ext_origin, $ext1)) {
 
        $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE WHERE id='2'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($rep.basename($Param->logo));
                }
                
                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='2'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !</p>";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }   
        else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
    if (in_array($ext_origin, $ext2)) {   
        $ImageChoisie_photo = imagecreatefrompng($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagealphablending($NouvelleImage_photo, false);
        imagesavealpha($NouvelleImage_photo, true);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagepng($NouvelleImage_photo , $rep.$hash.$fichier, 0)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='2'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($rep.basename($Param->logo));
                }

                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='2'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !</p>";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }
    else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
  }
}

if (isset($_POST['Reset2'])) {
  
    $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='2'");
    $SelectParam->execute();
    $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

    if($Param->logo!=$Home."/lib/header/logoType.png") {
      unlink($rep.basename($Param->logo));
    }
    
    $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='2'");
    $Insertlogo->bindParam(':photo', $Default, PDO::PARAM_STR);
    $Insertlogo->execute();

    $Valid="Logo ajouté avec succès !</p>";
    header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid)); 
}

?>

<!-- ************************************
*** Script realise par NeuroSoft Team ***
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

<H1>En-tête</H1>
<form action="" method="POST" enctype="multipart/form-data">

<img src="<?php echo $ParamLogoHeader->logo; ?>"/></p>

<input type="file" name="photo" placeholder="Votre logo"/><BR /><BR />

<input type="submit" name="Enregistrer2" value="Enregistrer"/>
<input type="submit" name="Reset2" value="Réinitialisé"/>
</form>

<H1>Logo (pied de page)</H1>
<form action="" method="POST" enctype="multipart/form-data">

<img src="<?php echo $ParamLogoFooter->logo; ?>"/></p>

<input type="file" name="photo" placeholder="Votre logo"/><BR /><BR />

<input type="submit" name="Enregistrer" value="Enregistrer"/>
<input type="submit" name="Reset" value="Réinitialisé"/>
</form>


</article>
</section>
</div>
</CENTER>
</body>

</html>