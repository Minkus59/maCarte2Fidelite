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

$ParamFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");    
$ParamFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$ParamFidelite->execute(); 
$Param=$ParamFidelite->fetch(PDO::FETCH_OBJ);

$OptionFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Option WHERE client=:client");    
$OptionFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$OptionFidelite->execute(); 
$Option=$OptionFidelite->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Enregistrer1'])) {
    $Model=$_POST['model'];

    $UpdateParam=$cnx->prepare("UPDATE ".$Prefix."Fidelite_Param SET model=:model WHERE client=:client");
    $UpdateParam->bindParam(':model', $Model, PDO::PARAM_STR);
    $UpdateParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $UpdateParam->execute();

    $Valid="Paramètre modifié avec succès";
    header("location:".$Home."/DashBoard/Compte/Parametre/Cheque/?valid=".urlencode($Valid));
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

<div id="ColoneLeft">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/navParam.inc.php"); ?>
</div>
<div id="ColoneRight">
<div id="Form_Middle2">
<H1 class="TitreOrange">Model</H1>
<form action="" method="POST">
<select name="model">
    <option value="1" <?php if ($Param->model==1) { echo "selected"; } ?>>1</option>
    <option value="2" <?php if ($Param->model==2) { echo "selected"; } ?>>2</option>
    <option value="3" <?php if ($Param->model==3) { echo "selected"; } ?>>3</option>
    <option value="4" <?php if ($Param->model==4) { echo "selected"; } ?>>4</option>
    <option value="5" <?php if ($Param->model==5) { echo "selected"; } ?>>5</option>
    <option value="6" <?php if ($Param->model==6) { echo "selected"; } ?>>6</option>
    <option value="7" <?php if ($Param->model==7) { echo "selected"; } ?>>7</option>
    <option value="8" <?php if ($Param->model==8) { echo "selected"; } ?>>8</option>
    <option value="9" <?php if ($Param->model==9) { echo "selected"; } ?>>9</option>
    <option value="10" <?php if ($Param->model==10) { echo "selected"; } ?>>10</option>
    <option value="11" <?php if ($Param->model==11) { echo "selected"; } ?>>11</option>
    <option value="12" <?php if ($Param->model==12) { echo "selected"; } ?>>12</option>
</select><BR /><BR />

<input type="submit" class="ButtonOrange" name="Enregistrer1" value="Enregistrer"/>
</form>
</div>
<div id="Image">
<H1>Liste des modèles</H1>
<div id="Model">
    <H2>Model 1</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_1.jpg"/>
</div>
<div id="Model">
    <H2>Model 2</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_2.jpg"/>
</div>
<div id="Model">
    <H2>Model 3</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_3.jpg"/>
</div>
<div id="Model">
    <H2>Model 4</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_4.jpg"/>
</div>
<div id="Model">
    <H2>Model 5</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_5.jpg"/>
</div>
<div id="Model">
    <H2>Model 6</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_6.jpg"/>
</div>
<div id="Model">
    <H2>Model 7</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_7.jpg"/>
</div>
<div id="Model">
    <H2>Model 8</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_8.jpg"/>
</div>
<div id="Model">
    <H2>Model 9</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_9.jpg"/>
</div>
<div id="Model">
    <H2>Model 10</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_10.jpg"/>
</div>
<div id="Model">
    <H2>Model 11</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_11.jpg"/>
</div>
<div id="Model">
    <H2>Model 12</H2>
<img src="<?php echo $Home; ?>/lib/img/Model/Cheque/Model_12.jpg"/>
</div>
</div>

</div>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>