<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");    

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid']; 
?>

<!-- **************************************
*** Script r�alis� par Helinckx Michael ***
*********** www.neuro-soft.fr *************
****************************************-->

<!DOCTYPE HTML>
<html>

<head>   
<meta charset="ISO-8859-15"/>
<title><?php echo $SOEPage->titre ?></title>
<meta name="category" content="<?php if ($SOEPage->nom=="/") { echo "Accueil"; } else { echo $SOEPage->nom; } ?>" />
<meta name="description" content="<?php echo $SOEPage->description ?>" />
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
<?php
if (isset($Erreur)) { echo "<p><font color='#FF0000'>".$Erreur."</font></p>"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".$Valid."</font></p>"; }
?>

<div id="cadre">
<img src="<?php echo $Home; ?>/lib/img/3_mois.png">

<H2>45�</H2>

Paiement en 1 mensualit� de 45�<BR />
 (15 � x 3mois = 45 �)
 
<p><input type="button" name="Choix1" value="Choisir cette offre" onclick="self.location.href='<?php echo $Home; ?>/DashBoard/'" />   </p>
</div>

<div id="cadre">
<img src="<?php echo $Home; ?>/lib/img/6_mois.png">

<H2>60�</H2>
Paiement en 1 mensualit� de 60�<BR />
 (10 � x 6mois = 60 �)
<p><input type="button" name="Choix2" value="Choisir cette offre" onclick="self.location.href='<?php echo $Home; ?>/DashBoard/'" />    </p>
</div>

<div id="cadre">
<img src="<?php echo $Home; ?>/lib/img/1-an.png">

<H2>96��</H2>
Paiement en 1 mensualit� de 96�<BR />
 (8 � x 12mois = 96 �)
 
<p><input type="button" name="Choix3" value="Choisir cette offre" onclick="self.location.href='<?php echo $Home; ?>/DashBoard/'" />    </p>
</div>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>