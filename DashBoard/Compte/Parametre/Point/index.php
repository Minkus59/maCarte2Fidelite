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
    $Conversion=trim($_POST['conversion']); 
    $Tranche=trim($_POST['tranche']); 
    $Cadeau=trim($_POST['cadeau']); 
    $Validite=$_POST['validite']; 
    
    if(!preg_match("#[0-9.]#", $Conversion)) {
        $Erreur="Le Prix Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    elseif(!preg_match("#[0-9.]#", $Tranche)) {
        $Erreur="La quantité n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    elseif(!preg_match("#[0-9.]#", $Cadeau)) {
        $Erreur="La quantité n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    elseif ($Conversion==0) {
        $Erreur="Le montant de conversion est incorrect !";
    }
    elseif ($Tranche==0) {
        $Erreur="La tranche d'achat est incorrect !";
    }
    elseif ($Cadeau==0) {
        $Erreur="Le montant du bon d'achat est incorrect !";
    }
    else {
        $UpdateParam=$cnx->prepare("UPDATE ".$Prefix."Fidelite_Param SET conversion=:conversion, tranche=:tranche, cadeau=:cadeau, validite=:validite WHERE client=:client");
        $UpdateParam->bindParam(':conversion', $Conversion, PDO::PARAM_STR);
        $UpdateParam->bindParam(':tranche', $Tranche, PDO::PARAM_STR);
        $UpdateParam->bindParam(':cadeau', $Cadeau, PDO::PARAM_STR);
        $UpdateParam->bindParam(':validite', $Validite, PDO::PARAM_STR);
        $UpdateParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $UpdateParam->execute();

        $Valid="Paramètre modifié avec succès";
        header("location:".$Home."/DashBoard/Compte/Parametre/Point/?valid=".urlencode($Valid));
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

<div id="ColoneLeft">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/navParam.inc.php"); ?>
</div>
<div id="ColoneRight">
<div id="Form_Middle5">
<H1 class="TitreOrange">Règle des points</H1>

<table>
<tr>
    <th class="TableOrange">
        Point / Euro dépensé (exemple : 1¤ = 1 point)
    </th>
    <th class="TableOrange">
        Montant déclenchant un bon d'achat (point)
    </th>
    <th class="TableOrange">
        Montant du bon d'achat (Euro)
    </th>
    <th class="TableOrange">
        Validité du bon d'achat
    </th>
    <th class="TableOrange">
        Mode d'encaissement
    </th>
    <th class="TableOrange">
        Action
    </th>
</tr>

<form action="" method="POST">
<TR>
    <td class="TableOrange">
        <input class="mini" type="text" name="conversion" value="<?php echo $Param->conversion; ?>"/>
    </td>
    <td class="TableOrange">
        <input class="mini" type="text" name="tranche" value="<?php echo $Param->tranche; ?>"/>
    </td>
    <td class="TableOrange">
        <input class="mini" type="text" name="cadeau" value="<?php echo $Param->cadeau; ?>"/>
    </td>
    <td class="TableOrange">
        <select name="validite" >
            <option value="1" <?php if ($Param->validite=="1") { echo "selected"; } ?> >1 Mois</option>
            <option value="3" <?php if ($Param->validite=="3") { echo "selected"; } ?> >3 Mois</option>
            <option value="6" <?php if ($Param->validite=="6") { echo "selected"; } ?> >6 Mois</option>
            <option value="12" <?php if ($Param->validite=="12") { echo "selected"; } ?> >12 Mois</option>
            <option value="18" <?php if ($Param->validite=="18") { echo "selected"; } ?> >18 Mois</option>
            <option value="24" <?php if ($Param->validite=="24") { echo "selected"; } ?> >24 Mois</option>
            <option value="36" <?php if ($Param->validite=="36") { echo "selected"; } ?> >36 Mois</option>
        </select>
    </td>
    <td class="TableOrange">
        <select name="mode">
            <option value="0" <?php if ($Param->mode==0) { echo "selected"; } ?>>Normal</option>
            <?php if ($Option->stock==1) { ?>
            <option value="1" <?php if ($Param->mode==1) { echo "selected"; } ?>>Stock</option>
            <?php } ?>
        </select>
    </td>
    <td class="TableOrange">
        <input type="submit" class="ButtonOrange" name="Enregistrer1" value="Enregistrer"/>
    </td>
</TR>
</table>

</form>
</div>

</div>
</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>