<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");

$Id=trim($_GET['id']);
$Client=$_GET['client'];
$Ip=$_SERVER['REMOTE_ADDR'];

if ((isset($Client))&&(!empty($Client))&&(isset($Id))&&(!empty($Id))) {

	$VerifClient=$cnx->prepare("SELECT (hash) FROM ".$Prefix."compte WHERE hash=:client");
	$VerifClient->bindParam(':client', $Client, PDO::PARAM_STR);
	$VerifClient->execute();
	$NbRowsClient=$VerifClient->rowCount();

	$VerifValid=$cnx->prepare("SELECT * FROM ".$Prefix."Boutique WHERE ip=:ip AND client=:client");
	$VerifValid->bindParam(':ip', $Ip, PDO::PARAM_STR);
	$VerifValid->bindParam(':client', $Client, PDO::PARAM_STR);
	$VerifValid->execute();
	$NbRowsValid=$VerifValid->rowCount();
		
	if (strlen($Client)!=32) {
		$Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !</p>";
	}
	elseif ($NbRowsClient!=1) {
		$Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !</p>";
	}
	elseif ($NbRowsValid==1) {
		$Erreur="Votre compte est déjà actif vous pouvez dès à présent vous connecter !</p>";
	}
	else {   
		$InsertValided=$cnx->prepare("UPDATE ".$Prefix."Boutique SET ip=:ip WHERE client=:client");
		$InsertValided->bindParam(':client', $Client, PDO::PARAM_STR);
		$InsertValided->bindParam(':ip', $Ip, PDO::PARAM_STR);
		$InsertValided->execute();

		$Valid= "Merci d'avoir validé votre boutique.<br />";
		$Valid.= "Vous pouvez dès à présent créer vos comptes !</p>";
	}
}
else {
	$Erreur="Erreur !";
}
?>

<!-- **************************************
*** Script réalisé par Helinckx Michael ***
*********** www.neuro-soft.fr *************
****************************************-->

<!DOCTYPE HTML>
<html>

<head>   
<meta charset="ISO-8859-15"/>
<title><?php echo $Societe; ?></title>
<meta name="category" content="Accueil" />
<meta name="description" content="<?php echo $Societe; ?>" />
<meta name="robots" content="index, follow"/>
<meta name="author" content="NeuroSoft Team"/>
<meta name="publisher" content="<?php echo $Publisher; ?>"/>
<meta name="reply-to" content="<?php echo $Destinataire; ?>"/>
<meta name="viewport" content="width=device-width" />

<link rel="shortcut icon" href="<?php echo $Home; ?>/lib/img/icone.ico" >
<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpapc.css" />

<script type="text/javascript" src="<?php echo $Home; ?>/lib/js/analys.js"></script>
</head>

<body>
<center>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/header.inc.php"); ?>

<section>
<div id="Center">
    
<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>