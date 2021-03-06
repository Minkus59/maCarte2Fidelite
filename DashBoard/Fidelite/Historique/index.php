<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");     

if ($Cnx_CompteClient==false) { 
    header("location:".$Home."/DashBoard/");
}  
elseif ($CompteExpirer==true) {
    $Erreur="Votre compte n'est plus actif, merci de prolonger votre abonnement pour continuer ? b?n?ficier du service";
}    

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Id=$_GET['id'];
$Now=time();
  
//Generer un code barre EAN13
$RecupInfoClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE id=:id AND client=:client");
$RecupInfoClient->bindParam(':id', $Id, PDO::PARAM_STR);
$RecupInfoClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoClient->execute();
$InfoClient=$RecupInfoClient->fetch(PDO::FETCH_OBJ);

if (isset($Id)) {
    $RecupHistoriqueClient=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE client=:client AND hash_client=:hash_client AND activate='1'");
    $RecupHistoriqueClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $RecupHistoriqueClient->bindParam(':hash_client', $InfoClient->hash, PDO::PARAM_STR);
    $RecupHistoriqueClient->execute();
}
else {
    $RecupHistoriqueClient=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE client=:client AND activate='1'");
    $RecupHistoriqueClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $RecupHistoriqueClient->execute(); 
}

$ParamFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");    
$ParamFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$ParamFidelite->execute(); 
$Param=$ParamFidelite->fetch(PDO::FETCH_OBJ);

$RecupPoint=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE client=:client AND hash_client=:hash_client AND activate='1' ORDER by id DESC");
$RecupPoint->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupPoint->bindParam(':hash_client', $InfoClient->hash, PDO::PARAM_STR);
$RecupPoint->execute();
$InfoPoint=$RecupPoint->fetch(PDO::FETCH_OBJ);

$Reste = $Param->tranche - $InfoPoint->reste;

if (isset($_POST['Supprimer'])) {
    $SelectDernier=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE client=:client AND activate='1' ORDER by id DESC");
    $SelectDernier->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $SelectDernier->execute();
    $InfoDernier=$SelectDernier->fetch(PDO::FETCH_OBJ);
     
    $DeleteHisto=$cnx->prepare("DELETE FROM ".$Prefix."Historique WHERE client=:client AND id=:id");
    $DeleteHisto->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $DeleteHisto->bindParam(':id', $InfoDernier->id, PDO::PARAM_STR);
    $DeleteHisto->execute();
    
    $DeleteHistoProduit=$cnx->prepare("DELETE FROM ".$Prefix."Historique_produit WHERE client=:client AND hash=:hash");
    $DeleteHistoProduit->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $DeleteHistoProduit->bindParam(':hash', $InfoDernier->hash, PDO::PARAM_STR);
    $DeleteHisto->execute();
    
    if (($InfoDernier->fidelite==1)||($InfoDernier->fidelite==2)||($InfoDernier->fidelite==3)) {
        $DeleteBon=$cnx->prepare("DELETE FROM ".$Prefix."Bon_Fidelite WHERE client=:client AND hash_transac=:hash");
        $DeleteBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $DeleteBon->bindParam(':hash', $InfoDernier->hash, PDO::PARAM_STR);
        $DeleteBon->execute();
    }
    header('location:'.$Home.'/DashBoard/Fidelite/Historique/');
}

if (isset($_POST['Supprimer2'])) {
    $SelectDernier=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE client=:client AND hash_client=:hash_client AND activate='1' ORDER by id DESC");
    $SelectDernier->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $SelectDernier->bindParam(':hash_client', $InfoClient->hash, PDO::PARAM_STR);
    $SelectDernier->execute();
    $InfoDernier=$SelectDernier->fetch(PDO::FETCH_OBJ);
     
    $DeleteHisto=$cnx->prepare("DELETE FROM ".$Prefix."Historique WHERE client=:client AND id=:id");
    $DeleteHisto->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $DeleteHisto->bindParam(':id', $InfoDernier->id, PDO::PARAM_STR);
    $DeleteHisto->execute();
    
    $DeleteHistoProduit=$cnx->prepare("DELETE FROM ".$Prefix."Historique_produit WHERE client=:client AND hash=:hash");
    $DeleteHistoProduit->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $DeleteHistoProduit->bindParam(':hash', $InfoDernier->hash, PDO::PARAM_STR);
    $DeleteHisto->execute();
    
    if ($InfoDernier->fidelite==1) {
        $DeleteBon=$cnx->prepare("UPDATE ".$Prefix."Bon_Fidelite SET fidelite='3' WHERE client=:client AND hash_transac=:hash");
        $DeleteBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $DeleteBon->bindParam(':hash', $InfoDernier->hash, PDO::PARAM_STR);
        $DeleteBon->execute();
    }
    header('location:'.$Home.'/DashBoard/Fidelite/Historique/?id='.$Id);
}

?>
<!-- ************************************
*** Script r?alis? par NeuroSoft Team ***
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

<H1>Historique des transactions</H1>

<?php if (!isset($Id)) { ?>
<form name="FormBack" action="" method="POST">
<input class="Terminer" type="submit" name="Supprimer" value="Supprimer la derni?re transaction" onclick="return(confirm('?tes vous sur de vouloir supprimer la derni?re transaction ?'));"/>
</form>
<?php } 
else { ?>
<form name="FormBack" action="" method="POST">
<input class="Terminer" type="submit" name="Supprimer2" value="Supprimer la derni?re transaction du client" onclick="return(confirm('?tes vous sur de vouloir supprimer la derni?re transaction ?'));"/>
</form>
<?php } ?>

<?php
if (isset($Id)) { ?>
<H2><?php echo $InfoClient->nom." ".$InfoClient->prenom; ?></H2>

Total des Points : <?php echo $InfoPoint->total_point; ?><BR />
<?php echo $Reste; ?> points avant le prochain bon d'achat<BR /><BR /><?php
} ?>
<table>
<tr>
    <th class="TableRose">
        Code Transaction
    </th>
    <th class="TableRose">
        Date
    </th>
    <th class="TableRose">
        Montant
    </th>
    <th class="TableRose">
        Point cumuler
    </th>
    <th class="TableRose">
        Bon de fid?lit?
    </th>
    <th class="TableRose">
        Action
    </th>
</tr>
<?php 
while($HistoriqueClient=$RecupHistoriqueClient->fetch(PDO::FETCH_OBJ)) { ?>
    <tr>
        <td class="TableRose">
            <?php echo $HistoriqueClient->hash; ?>
        </td>
        <td class="TableRose">
            <?php echo date("d-m-y / G:i:s", $HistoriqueClient->created); ?>
        </td>
        <td class="TableRose">
            <?php echo $HistoriqueClient->prix; ?>
        </td>
        <td class="TableRose">
            <?php echo $HistoriqueClient->point_j; ?>
        </td>
        <td class="TableRose">
            <?php if ($HistoriqueClient->fidelite==1) { echo "Editer"; }
            elseif ($HistoriqueClient->fidelite==2) { echo "Consommer"; }
            elseif ($HistoriqueClient->fidelite==3) { echo "Annuler"; }
            else { echo "Non"; } ?>
        </td>
        <td class="TableRose">
            <?php if ($HistoriqueClient->fidelite==1) { ?>
            <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Historique/Apercu/?id=<?php echo $HistoriqueClient->id; ?>" target="_blank"><acronym title="Aper?u"><img src="<?php echo $Home; ?>/lib/img/Button/aperc?u-rose.png"/></acronym></a>
            <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Historique/Apercu/?id=<?php echo $HistoriqueClient->id; ?>&type=mail"><acronym title="Envoyer au client par E-mail"><img src="<?php echo $Home; ?>/lib/img/Button/mailing-rose.png"/></acronym></a>
            <?php } ?>
            <?php if ($HistoriqueClient->fidelite==3) { ?>
            <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Historique/activer.php?id=<?php echo $HistoriqueClient->id; ?>&retour=<?php echo $Id; ?>"><acronym title="Activ? le bon"><img src="<?php echo $Home; ?>/lib/img/Button/d?sactiver-rose.png"/></acronym></a>
            <?php } elseif ($HistoriqueClient->fidelite==0) { ?>
            
            <?php } else { ?>
            <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Historique/desactiver.php?id=<?php echo $HistoriqueClient->id; ?>&retour=<?php echo $Id; ?>"><acronym title="D?sactiv? le bon"><img src="<?php echo $Home; ?>/lib/img/Button/Activer-rose.png"/></acronym></a>
            <?php } ?>
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