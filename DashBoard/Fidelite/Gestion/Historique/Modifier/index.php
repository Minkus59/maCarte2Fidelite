<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");     

if ($Cnx_CompteClient==false) { 
    header("location:".$Home);
}  
elseif ($CompteExpirer==true) {
    $Erreur="Votre compte n'est plus actif, merci de prolonger votre abonnement pour continuer à bénéficier du service";
}      

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];     

$Id=$_GET['id'];
  
//Generer un code barre EAN13
$RecupInfoClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE id=:id");
$RecupInfoClient->bindParam(':id', $Id, PDO::PARAM_STR);
$RecupInfoClient->execute();
$InfoClient=$RecupInfoClient->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Valider'])) {
    
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
        $UpdateUser=$cnx->prepare("UPDATE ".$Prefix."Fidelite_Client SET civilite=:civilite, nom=:nom, prenom=:prenom, adresse=:adresse, cp=:cp, ville=:ville, tel=:tel, email=:email WHERE id=:id"); 
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

        $Erreur="Enregistrement effectué avec succés !</p>";
        header("location:".$Home."/DashBoard/Fidelite/Gestion/?erreur=".urlencode($Erreur));
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
<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpapc.css" />

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

<H1>Modifier la transaction N° : </H1>


</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>