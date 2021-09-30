<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 

$Client=trim($_GET['id']);
$Valided=trim($_GET['Valid']);
$Temps=time();

if ((isset($Client))&&(!empty($Client))&&(isset($Valided))&&(!empty($Valided))) {

	$VerifClient=$cnx->prepare("SELECT (hash) FROM ".$Prefix."compte WHERE hash=:client");
	$VerifClient->bindParam(':client', $Client, PDO::PARAM_STR);
	$VerifClient->execute();
	$NbRowsClient=$VerifClient->rowCount();

	$VerifValid=$cnx->prepare("SELECT (valided) FROM ".$Prefix."compte WHERE valided=:valid AND hash=:client");
	$VerifValid->bindParam(':valid', $Valided, PDO::PARAM_STR);
	$VerifValid->bindParam(':client', $Client, PDO::PARAM_STR);
	$VerifValid->execute();
	$NbRowsValid=$VerifValid->rowCount();
		
	if (strlen($Client)!=32) {
		$Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !</p>";
	}

	elseif ($Valided!=1) {
		$Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !</p>";
	}

	elseif ($NbRowsClient!=1) {
		$Erreur="Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !</p>";
	}

	elseif ($NbRowsValid==1) {
		$Erreur="Votre compte est déjà actif vous pouvez dès à présent vous connecter !</p>";
	}

	else {   
		$InsertValided=$cnx->prepare("UPDATE ".$Prefix."compte SET valided=1 WHERE hash=:client");
		$InsertValided->bindParam(':client', $Client, PDO::PARAM_STR);
		$InsertValided->execute();

		$InsertTemps=$cnx->prepare("UPDATE ".$Prefix."compte SET essai=:temps WHERE hash=:client");
		$InsertTemps->bindParam(':client', $Client, PDO::PARAM_STR);
		$InsertTemps->bindParam(':temps', $Temps, PDO::PARAM_STR);
		$InsertTemps->execute();

		if ((!$VerifValid)||(!$VerifClient)||(!$InsertValided)) {
			$SupprValided=$cnx->prepare("UPDATE ".$Prefix."compte SET valided=0 WHERE hash=:client");
			$SupprValided->bindParam(':client', $Client, PDO::PARAM_STR);
			$SupprValided->execute();

			$SupprTemps=$cnx->prepare("UPDATE ".$Prefix."compte SET essai=:temps WHERE hash=:client");
			$SupprTemps->bindParam(':client', $Client, PDO::PARAM_STR);
			$SupprTemps->bindParam(':temps', $Temps, PDO::PARAM_STR);
			$SupprTemps->execute();

			$Erreur="L'enregistrement des données à échouée, veuillez réessayer ultérieurement !</p>";
		}

		else {
			$Valid= "Merci d'avoir validé votre compte.<br />";
			$Valid.= "Vous pouvez dès à présent vous connecter !</p>";
		}	
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