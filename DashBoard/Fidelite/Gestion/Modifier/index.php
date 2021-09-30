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

$Id=$_GET['id'];
  
$RecupInfoClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE id=:id");
$RecupInfoClient->bindParam(':id', $Id, PDO::PARAM_STR);
$RecupInfoClient->execute();
$InfoClient=$RecupInfoClient->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Valider'])) {
    
    $Carte=$_POST['carte'];
    $Civilite=$_POST['civilite'];
    $Nom=$_POST['nom'];
    $Prenom=$_POST['prenom'];
    $Adresse=$_POST['adresse'];
    $Cp=$_POST['cp'];
    $Ville=$_POST['ville'];
    $Tel=trim($_POST['tel']);
    $Email=FiltreEmail('email');
    
    if ($Email[0]==false) { 
        $Erreur=$Email[1];
    }
    else {
        $UpdateUser=$cnx->prepare("UPDATE ".$Prefix."Fidelite_Client SET carte=:carte, civilite=:civilite, nom=:nom, prenom=:prenom, adresse=:adresse, cp=:cp, ville=:ville, tel=:tel, email=:email WHERE id=:id"); 
        $UpdateUser->bindParam(':carte', $Carte, PDO::PARAM_STR);
        $UpdateUser->bindParam(':civilite', $Civilite, PDO::PARAM_STR);
        $UpdateUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $UpdateUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
        $UpdateUser->bindParam(':adresse', $Adresse, PDO::PARAM_STR);
        $UpdateUser->bindParam(':cp', $Cp, PDO::PARAM_STR);
        $UpdateUser->bindParam(':ville', $Ville, PDO::PARAM_STR);
        $UpdateUser->bindParam(':tel', $Tel, PDO::PARAM_STR);
        $UpdateUser->bindParam(':email', $Email, PDO::PARAM_STR);
        $UpdateUser->bindParam(':id', $Id, PDO::PARAM_STR);
        $UpdateUser->execute();

        $Valid="Enregistrement effectué avec succés !</p>";
        header("location:".$Home."/DashBoard/Fidelite/Gestion/?valid=".urlencode($Valid));
    }
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
<div id="Form_Middle">
<H1 class="TitreRose">Modification client</H1>

<form action="" method="POST">
    
<label class="col_1">Numéro de carte</label>
<input type="text" name="carte" value="<?php echo $InfoClient->carte; ?>"/>
<br />
    
<label class="col_1" for="type">Civilité</label>
<select name="civilite">
<option value="Mr" <?php if ($InfoClient->civilite=="Mr") { echo "selected"; } ?> >Mr</sub></option>
<option value="Mme" <?php if ($InfoClient->civilite=="Mme") { echo "selected"; } ?> >Mme</option>
<option value="Mme" <?php if ($InfoClient->civilite=="Mme") { echo "selected"; } ?> >Mlle</option>
<option value="Sarl" <?php if ($InfoClient->civilite=="Sarl") { echo "selected"; } ?> >Sarl</option>
<option value="SAS" <?php if ($InfoClient->civilite=="SAS") { echo "selected"; } ?> >SAS</option>
<option value="SASU" <?php if ($InfoClient->civilite=="SASU") { echo "selected"; } ?> >SASU</option>
<option value="Ets" <?php if ($InfoClient->civilite=="Ets") { echo "selected"; } ?> >Ets</option>
</select>
<br />
<label class="col_1">Nom :</label>
<input type="text" name="nom" value="<?php echo $InfoClient->nom; ?>"/>
<br />
<label class="col_1">Prénom :</label>
<input type="text" name="prenom" value="<?php echo $InfoClient->prenom; ?>"/>
<br />
<label class="col_1">Adresse :</label>
<textarea name="adresse"><?php echo $InfoClient->adresse; ?></textarea>
<br />
<label class="col_1">Code postal :</label>
<input type="text" name="cp" value="<?php echo $InfoClient->cp; ?>"/>
<br />
<label class="col_1">Ville :</label>
<input type="text" name="ville" value="<?php echo $InfoClient->ville; ?>"/>
<br />
<label class="col_1">Numéro de téléphone :</label>
<input type="text" name="tel" value="<?php echo $InfoClient->tel; ?>"/>
<br />
<label class="col_1">Adresse E-mail<font color='#FF0000'>*</font> :</label>
<input type="email" name="email" value="<?php echo $InfoClient->email; ?>" required="required"/>
</p>
<span class="col_1"></span>
<input type="submit" class="ButtonRose" name="Valider" value="Modifier"/>

</form>
</p>
<font color='#FF0000'>*</font> : Informations requises
</div>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>