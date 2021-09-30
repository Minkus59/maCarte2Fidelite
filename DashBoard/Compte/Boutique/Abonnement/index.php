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
<div id="Form_Middle3">
<H1 class="TitreOrange">Abonnement</H1>

<div id="cadre">
<img src="<?php echo $Home; ?>/lib/img/3_mois.png">

<H2>59€ HT / Mois </H2>
Paiement en 1 mensualité de 177€<BR />
 (59€  x 3mois = 177€ )
 
<p><input type="button" class="ButtonOrange" name="Choix1" value="Choisir cette offre" onclick="self.location.href='<?php echo $Home; ?>/Paiement/paiement_3mois.php'" />   </p>
</div>

<div id="cadre">
<img src="<?php echo $Home; ?>/lib/img/6_mois.png">

<H2>54€ HT / Mois</H2>
Paiement en 1 mensualité de 324€<BR />
 (54€  x 6mois = 324€ )
 
<p><input type="button" class="ButtonOrange" name="Choix2" value="Choisir cette offre" onclick="self.location.href='<?php echo $Home; ?>/Paiement/paiement_6mois.php'" />    </p>
</div>

<div id="cadre">
<img src="<?php echo $Home; ?>/lib/img/1-an.png">

<H2>49€ HT / Mois</H2>
Paiement en 1 mensualité de 588€<BR />
 (49€  x 12mois = 588€ )
 
<p><input type="button" class="ButtonOrange" name="Choix3" value="Choisir cette offre" onclick="self.location.href='<?php echo $Home; ?>/Paiement/paiement_12mois.php'" />    </p>
</div>

</div>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>