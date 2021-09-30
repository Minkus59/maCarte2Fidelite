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

if ((!empty($_GET['id']))&&(isset($_POST['oui']))) {
     
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."categorie WHERE id=:id AND client=:client");
    $Select->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $Select->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
    $Select->execute();
    $Info=$Select->fetch(PDO::FETCH_OBJ);
     
    $Update=$cnx->prepare("UPDATE ".$Prefix."produit SET categorie='' WHERE categorie=:categorie AND client=:client");
    $Update->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $Update->bindParam(':categorie', $Info->nom, PDO::PARAM_STR);
    $Update->execute();
                    
    $delete=$cnx->prepare("DELETE FROM ".$Prefix."categorie WHERE id=:id AND client=:client");
    $delete->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $delete->bindParam(':id', $_GET['id'], PDO::PARAM_STR);
    $delete->execute();

    header('Location:'.$Home.'/DashBoard/Stock/Categorie/');
    
}

if ((!empty($_GET['id']))&&(isset($_POST['non']))) {  
    header('Location:'.$Home.'/DashBoard/Stock/Categorie/');
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
Etes-vous sur de vouloir supprimer cette catégorie ?

<TABLE width="300">
  <form action="" method="POST">
<TR><TD align="center"><input name="oui" type="submit" value="OUI"></TD><TD align="center"><input name="non" type="submit" value="NON"/></TD></TR></form>
</TABLE>
</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>