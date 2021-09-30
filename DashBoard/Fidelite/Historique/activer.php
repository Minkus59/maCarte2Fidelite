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
$Retour=$_GET['retour'];
$Now=time();

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {
    
    $Verif=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE id=:id AND client=:client");
    $Verif->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $Verif->bindParam(':id', $Id, PDO::PARAM_INT);
    $Verif->execute();
    $Fideliter=$Verif->fetch(PDO::FETCH_OBJ);
    
    $VerifBon=$cnx->prepare("SELECT * FROM ".$Prefix."Bon_Fidelite WHERE hash_transac=:hash AND hash_client=:hash_client AND client=:client");
    $VerifBon->bindParam(':hash', $Fideliter->hash, PDO::PARAM_STR);
    $VerifBon->bindParam(':hash_client', $Fideliter->hash_client, PDO::PARAM_STR);
    $VerifBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $VerifBon->execute();
    $BonFideliter=$VerifBon->fetch(PDO::FETCH_OBJ);
    
    if ($BonFideliter->consomed >= $Now) {
        $Update=$cnx->prepare("UPDATE ".$Prefix."Historique SET fidelite='1' WHERE id=:id AND client=:client");
        $Update->bindParam(':id', $Id, PDO::PARAM_INT);
        $Update->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $Update->execute();
        
        $Update=$cnx->prepare("UPDATE ".$Prefix."Bon_Fidelite SET fidelite='1' WHERE hash_transac=:hash AND hash_client=:hash_client AND client=:client");
        $Update->bindParam(':hash', $Fideliter->hash, PDO::PARAM_STR);
        $Update->bindParam(':hash_client', $Fideliter->hash_client, PDO::PARAM_STR);
        $Update->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $Update->execute();
        
        if (!empty($Retour)) {
            header('Location:'.$Home.'/DashBoard/Fidelite/Historique/?id='.$Retour);
        }
        else {
            header('Location:'.$Home.'/DashBoard/Fidelite/Historique/');
        }
    }
    else {
        $Erreur="Impossible d'activer le bon d'achat<BR />";
        $Erreur.="La date de validité est dépassé";
        
        if (!empty($Retour)) {
            header('Location:'.$Home.'/DashBoard/Fidelite/Historique/?id='.$Retour.'&erreur='.urlencode($Erreur));
        }
        else {
            header('Location:'.$Home.'/DashBoard/Fidelite/Historique/?erreur='.urlencode($Erreur));
        }
    }
}

if ((!empty($_GET['id']))&&(isset($_POST['non']))) {  
    if (!empty($Retour)) {
        header('Location:'.$Home.'/DashBoard/Fidelite/Historique/?id='.$Retour);
    }
    else {
        header('Location:'.$Home.'/DashBoard/Fidelite/Historique/');
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

Etes-vous sur de vouloir activer ce bon d'achat ? </p>

<TABLE width="300">
<form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR>
</form></TABLE>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>