<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 

$Email=trim($_POST['email']);
$Hash=md5(uniqid(rand(), true));

if (isset($_POST['Recevoir'])) {

	$VerifEmail=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE email=:email");
	$VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
	$VerifEmail->execute();
	$NbRowsEmail=$VerifEmail->rowCount();
	$Data=$VerifEmail->fetch(PDO::FETCH_OBJ);

	$Client=$Data->hash;

	if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $Email)) { 
		$Erreur="L'adresse e-mail n'est pas conforme !</p>";
	}
	
	elseif ($NbRowsEmail!=1) { 			
		$Erreur="Cette adresse n'existe pas !</p>";
	}

	else {
    $VerifProcedure=$cnx->prepare("SELECT * FROM ".$Prefix."secu_mdp WHERE hash=:client");
  	$VerifProcedure->bindParam(':client', $Client, PDO::PARAM_STR);
  	$VerifProcedure->execute();
  	$NbRowsProcedur=$VerifProcedure->rowCount();
    
    if ($NbRowsProcedur==1) { 			
		  $Erreur="Une procudure est déja en cour !<BR />";
		  $Erreur.="Veuillez verifier vos e-mails et suivre la procédure deja envoyé !<BR />";
  	}
    else {
   		$InsertHash=$cnx->prepare("INSERT INTO ".$Prefix."secu_mdp (code, hash) VALUES (:hash, :client)");
  		$InsertHash->bindParam(':hash', $Hash, PDO::PARAM_STR);
  		$InsertHash->bindParam(':client', $Client, PDO::PARAM_STR);
  		$InsertHash->execute();
  
  		$Entete ='From: "no-reply@neuro-soft.fr"<postmaster@neuro-soft.fr>'."\r\n"; 
  		$Entete .= 'MIME-Version: 1.0' . "\r\n";						
  		$Entete .='Content-Type: text/html; charset="iso-8859-15"'."\r\n"; 						
  		$Entete .='Content-Transfer-Encoding: 8bit'; 
  		$Message ="<html><head><title>Changement de mot de passe</title>
  			</head><body>
  			<font color='#9e2053'><H1>Procédure de changement de mot de passe</H1></font>			
  			Veuillez cliquer sur le lien suivant pour changer votre mot de passe sur devis.neuro-soft.fr .</p>						
  			<a href='".$Home."/Validation/Mdp/?id=$Client&hash=$Hash'>Cliquez ici</a></p>					
  			____________________________________________________</p>
  			Cordialement<br />
  			".$Home."</p>
  			<font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>					
  			</body></html>";
  
  		if (!mail($Email, "Changement de mot de passe", $Message, $Entete)) { 							
  			$Erreur="L'e-mail de confirmation n'a pu etre envoyé, vérifiez que vous l'avez entré correctement !</p>";
  		}
  		else {
  			$Erreur="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."</p>";
  		}
    }
	}
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
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; } ?>

<H1>Procédure de changement de mot de passe</H1>

<form id="form_email" action="" method="POST">

<label class="col_1">Adresse E-mail :</label>
<input type="email" name="email"required="required"/>
<br />

<span class="col_1"></span>
<input type="submit" name="Recevoir" value="Recevoir"/>
</form>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>