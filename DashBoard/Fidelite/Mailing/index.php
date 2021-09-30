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
<form action="<?php echo $Home; ?>/DashBoard/Fidelite/Mailing/Envoyer/" method="POST">
    
 <H1>Mailing</H1>   
     
<table width=900>
<tr>
    <th class="TableRose">
        Code Carte
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
        Email
    </th>
    <th class="TableRose">
        Action
    </th>
</tr>
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
            <?php echo stripslashes($Client->email); ?>
        </td>
        <td class="TableRose">
            <input type="checkbox" name="selection[]" value="<?php echo $Client->email; ?>"/>
        </td>
    </tr>
<?php
}
?>
</table>
Pour la selection : <input type="submit" class="ButtonRose" name="Envoyer" value="Envoyer un e-mail"/>
</form>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>