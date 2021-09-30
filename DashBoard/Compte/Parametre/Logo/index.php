<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");     

if ($Cnx_CompteClient==false) { 
    header("location:".$Home."/DashBoard/");
}  
elseif ($CompteExpirer==true) {
    $Erreur="Votre compte n'est plus actif, merci de prolonger votre abonnement pour continuer à bénéficier du service";
}    

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid']; 

$ParamFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");    
$ParamFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$ParamFidelite->execute(); 
$Param=$ParamFidelite->fetch(PDO::FETCH_OBJ);

$OptionFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Option WHERE client=:client");    
$OptionFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$OptionFidelite->execute(); 
$Option=$OptionFidelite->fetch(PDO::FETCH_OBJ);

// Upload d'image
$chemin = $_FILES['photo']['name'];
$rep = $_SERVER['DOCUMENT_ROOT']."/lib/logo/";
$fichier = basename($chemin);
$taille_origin = filesize($_FILES['photo']['tmp_name']);
$ext = array('.jpeg', '.JPEG', '.jpg', '.JPG', '.png', '.PNG');
$ext1 = array('.jpeg', '.JPEG', '.jpg', '.JPG');
$ext2 = array('.png', '.PNG');
$ext_origin = strchr($chemin, '.');
$hash = md5(uniqid(rand(), true));
$Chemin_upload = $Home."/lib/logo/".$hash.$fichier."";
$TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
$taille_max = 10000000;
$Default=$Home."/lib/logo/logoType.png";

if ((isset($_POST['Enregistrer1']))&&(in_array($ext_origin, $ext))) {

    if($taille_origin>$taille_max){
        $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 2800px sur 1800px de hauteur";
    }
    if (!isset($Erreur)){       
      //si largeur + grande
      if ($TailleImageChoisie[0]>=$TailleImageChoisie[1]) {
        $NouvelleLargeur_photo = 150;
        $NouvelleHauteur_photo = ( ($TailleImageChoisie[1] * (($NouvelleLargeur_photo)/$TailleImageChoisie[0])) );     
      }
      else {
        $NouvelleHauteur_photo = 150;
        $NouvelleLargeur_photo = ( ($TailleImageChoisie[0] * (($NouvelleHauteur_photo)/$TailleImageChoisie[1])) );  
      }

  if (in_array($ext_origin, $ext1)) {
 
        $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");
                $SelectParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($rep.basename($Param->logo));
                }
                
                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."Fidelite_Param SET logo=:photo WHERE client=:client");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !</p>";
                header("location:".$Home."/DashBoard/Compte/Parametre/Logo/?valid=".urlencode($Valid));
        }   
        else { $Erreur="Erreur !"; }    
    }
    if (in_array($ext_origin, $ext2)) {   
        $ImageChoisie_photo = imagecreatefrompng($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagealphablending($NouvelleImage_photo, false);
        imagesavealpha($NouvelleImage_photo, true);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagepng($NouvelleImage_photo , $rep.$hash.$fichier, 0)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");
                $SelectParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($rep.basename($Param->logo));
                }

                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."Fidelite_Param SET logo=:photo WHERE client=:client");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !</p>";
                header("location:".$Home."/DashBoard/Compte/Parametre/Logo/?valid=".urlencode($Valid));
        }
    else { $Erreur="Erreur !"; }    
    }
  }
}

if (isset($_POST['Reset'])) {
  
    $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");
    $SelectParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $SelectParam->execute();
    $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

    if($Param->logo!="http://site5.neuro-soft.fr/lib/logo/logoType.png") {
      unlink($rep.basename($Param->logo));
    }
    
    $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."Fidelite_Param SET logo=:photo WHERE client=:client");
    $Insertlogo->bindParam(':photo', $Default, PDO::PARAM_STR);
    $Insertlogo->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $Insertlogo->execute();

    $Valid="Logo ajouté avec succès !</p>";
    header("location:".$Home."/DashBoard/Compte/Parametre/Logo/?valid=".urlencode($Valid));
}

?>

<!-- ************************************
*** Script réalisé par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!DOCTYPE HTML>
<html>

<head>   
<meta charset="ISO-8859-15"/>
<title><?php echo $Societe; ?></title>
<meta name="robots" content="index, follow"/>
<meta name="author" content="NeuroSoft Team"/>
<meta name="publisher" content="<?php echo $Publisher; ?>"/>
<meta name="reply-to" content="<?php echo $Destinataire; ?>"/>
<meta name="viewport" content="width=device-width" />

<link rel="shortcut icon" href="<?php echo $Home; ?>/lib/img/icone.ico" >
<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/lib/css/prive/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/lib/css/prive/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/lib/css/prive/misenpapc.css" />

<script type="text/javascript" src="<?php echo $Home; ?>/lib/js/analys.js"></script>

</head>

<body>
<center>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/headerCo.inc.php"); ?>

<section>
<div id="Center">
    
<article>
<?php
if (isset($Erreur)) { echo "<p><font color='#FF0000'>".$Erreur."</font></p>"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".$Valid."</font></p>"; }
?>

<div id="ColoneLeft">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/navParam.inc.php"); ?>
</div>
<div id="ColoneRight">
<div id="Form_Middle2">
<H1 class="TitreOrange">Logo</H1>
<form action="" method="POST" enctype="multipart/form-data">

<img src="<?php echo $Param->logo; ?>"/></p>

<input type="file" name="photo" placeholder="Votre logo"/><BR /><BR />

<input type="submit" class="ButtonOrange" name="Enregistrer1" value="Enregistrer"/>
<input type="submit" class="ButtonOrange" name="Reset" value="Réinitialisé"/>
</form>
</div>

</div>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>