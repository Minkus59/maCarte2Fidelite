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

$RecupInfoClient=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:client");
$RecupInfoClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoClient->execute();
$InfoClient=$RecupInfoClient->fetch(PDO::FETCH_OBJ);
 
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
    
<?php
if (isset($Erreur)) { echo "<p><font color='#FF0000'>".$Erreur."</font></p>"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".$Valid."</font></p>"; }
?>
<article>
<H1 class="login">Informations personnelles</H1>

<div id="Form_Middle">
<H1 class="TitreOrange">Informations de connexion</H1></p>
Adresse e-mail : <b><?php echo $InfoClient->email; ?></b><br />
Mot de passe : <b>Vous seul le connaissez !</b></p>
<input type="button" class="ButtonOrange" name="Modifier" value="Modifier" onclick="self.location.href='<?php echo $Home; ?>/DashBoard/Compte/Informations/Modification?genre=connexion'" />
</div>

<div id="Form_Middle">
<H1 class="TitreOrange">Informations personnelles</H1></p>
<span class="col1">Nom : </span><b><?php echo stripslashes($InfoClient->nom); ?></b><br />
Prénom : <b><?php echo stripslashes($InfoClient->prenom); ?></b><br />
Téléphone : <b><?php echo $InfoClient->tel; ?></b><br />
Adresse : <b><?php echo stripslashes($InfoClient->adresse); ?></b><br />
Code postal : <b><?php echo $InfoClient->cp; ?></b><br />
Ville : <b><?php echo stripslashes($InfoClient->ville); ?></b></p>
<input type="button" class="ButtonOrange" name="Modifier" value="Modifier" onclick="self.location.href='<?php echo $Home; ?>/DashBoard/Compte/Informations/Modification?genre=personnelles'" />
</div>

<div id="Form_Middle">
<H1 class="TitreOrange">Mon abonnement</H1></p>
Type d'abonnement : <b><?php echo $InfoClient->abo; ?></b><br />
<?php if (($InfoClient->actif=="2")||($InfoClient->actif=="3")) { ?>
Depuis le :<b> <?php echo date("d/m/y", $InfoClient->debut); ?></b><br />
Jusqu'au : <b><?php echo date("d/m/y", $InfoClient->fin); ?></b>
<?php } 
 if ($InfoClient->actif=="1") { ?>
Jusqu'au : <b><?php echo date("d/m/y", $InfoClient->essai + 1209600); ?></b>
<?php } ?>
</p>
<input type="button" class="ButtonOrange" name="Prolonger" value="Prolonger" onclick="self.location.href='<?php echo $Home; ?>/DashBoard/Compte/Boutique/Abonnement/'" />
</div>

</article>  
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>