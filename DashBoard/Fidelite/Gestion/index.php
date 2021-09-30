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

$SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client ORDER BY nom ASC");
$SelectClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectClient->execute();

//Moteur de recherche
if (isset($_POST['MoteurRecherche'])) {
    if (!empty($_POST['RechercheGencode'])) {
        $RechercheGencode=trim($_POST['RechercheGencode']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE carte=:carte AND client=:client");
        $SelectClient->execute(array(':client'=> $SessionCompteClient,':carte'=> $RechercheGencode)); 
    }
    if (!empty($_POST['RechercheNom'])) {
        $RechercheNom=trim($_POST['RechercheNom']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE nom LIKE :nom AND client=:client");
        $SelectClient->execute(array(':nom' => $RechercheNom."%",':client'=> $SessionCompteClient)); 
    }
    if (!empty($_POST['RecherchePrenom'])) {
        $RecherchePrenom=trim($_POST['RecherchePrenom']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE prenom LIKE :prenom AND client=:client");
        $SelectClient->execute(array(':client'=> $SessionCompteClient,':prenom' =>$RecherchePrenom."%")); 
    }
    if (!empty($_POST['RechercheAdresse'])) {
        $RechercheAdresse=trim($_POST['RechercheAdresse']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE adresse LIKE :adresse AND client=:client");
        $SelectClient->execute(array(':client'=> $SessionCompteClient,':adresse' => "%".$RechercheAdresse."%")); 
    }
    if (!empty($_POST['RechercheCp'])) {
        $RechercheCp=trim($_POST['RechercheCp']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE cp=:cp AND client=:client");
        $SelectClient->execute(array(':client'=> $SessionCompteClient,':cp' => $RechercheCp)); 
    }
    if (!empty($_POST['RechercheVille'])) {
        $RechercheVille=trim($_POST['RechercheVille']);
        $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE ville LIKE :ville AND client=:client");
        $SelectClient->execute(array(':client'=> $SessionCompteClient,':ville' => $RechercheVille."%")); 
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
<H1>Gestion client</H1>

<table>
<tr>
    <th class="TableRose">
        Gencode
    </th>
    <th class="TableRose">
        Nom
    </th>
    <th class="TableRose">
        Prenom
    </th>
    <th class="TableRose">
        Adresse
    </th>
    <th class="TableRose">
        Code postal
    </th>
    <th class="TableRose">
        Ville
    </th>
    <th class="TableRose">
        Action
    </th>
</tr>

<form name="form_recherche" action="" method="POST">
<TR>
    <td class="TableRose">
        <input class="moyen" type="text" name="RechercheGencode" autofocus/>
    </td>
    <td class="TableRose">
        <input class="moyen" type="text" name="RechercheNom"/>
    </td>
    <td class="TableRose">
        <input class="moyen" type="text" name="RecherchePrenom"/>
    </td>
    <td class="TableRose">
        <input class="description" type="text" name="RechercheAdresse"/>
    </td>
    <td class="TableRose">
        <input class="mini" type="text" name="RechercheCp"/>
    </td>
    <td class="TableRose">
        <input class="mini" type="text" name="RechercheVille"/>
    </td>
    <td class="TableRose">
        <input type="submit" class="ButtonRose" name="MoteurRecherche" value="Rechercher"/>
    </td>
</TR>
</form>

<?php
while($Client=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>

    <tr>
        <td class="TableRose">
            <?php echo $Client->carte; ?>
        </td>
        <td class="TableRose">
            <?php echo stripslashes($Client->nom); ?>
        </td>
        <td class="TableRose">
            <?php echo stripslashes($Client->prenom); ?>
        </td>
        <td class="TableRose">
            <?php echo nl2br(stripslashes($Client->adresse)); ?>
        </td>
        <td class="TableRose">
            <?php echo $Client->cp; ?>
        </td>
        <td class="TableRose">
            <?php echo stripslashes($Client->ville); ?>
        </td>
        <td class="TableRose">
            <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Historique/?id=<?php echo $Client->id; ?>"><acronym title="Historique"><img src="<?php echo $Home; ?>/lib/img/Button/apercçu-rose.png"/></acronym></a>
            <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Gestion/Modifier/?id=<?php echo $Client->id; ?>"><acronym title="Modifier"><img src="<?php echo $Home; ?>/lib/img/Button/modifier-rose.png"/></acronym></a>
        </td>
    </tr>

<?php
}
?>
</table>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>