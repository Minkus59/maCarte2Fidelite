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

//Produit
$SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE client=:client");
$SelectArticleExist->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectArticleExist->execute(); 

//categorie
$SelectCategorieExist=$cnx->prepare("SELECT * FROM ".$Prefix."categorie WHERE client=:client");
$SelectCategorieExist->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectCategorieExist->execute();                

//Moteur de recherche
if (isset($_POST['MoteurRecherche'])) {
    if (!empty($_POST['RechercheGencode'])) {
        $RechercheGencode=trim($_POST['RechercheGencode']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE gencode=:gencode AND client=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionCompteClient,':gencode'=> $RechercheGencode)); 
    }
    if (!empty($_POST['RechercheDescription'])) {
        $RechercheDescription=trim($_POST['RechercheDescription']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE description LIKE :description AND client=:client");
        $SelectArticleExist->execute(array(':description' => "%".$RechercheDescription."%",':client'=> $SessionCompteClient)); 
    }
    if (!empty($_POST['RechercheCategorie'])) {
        $RechercheCategorie=trim($_POST['RechercheCategorie']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE categorie=:categorie AND client=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionCompteClient,':categorie' =>$RechercheCategorie)); 
    }
    if (!empty($_POST['RecherchePrix'])) {
        $RecherchePrix=trim($_POST['RecherchePrix']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE prix=:prix AND client=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionCompteClient,':prix' => $RecherchePrix)); 
    }
    if (!empty($_POST['RechercheQuantite'])) {
        $RechercheQuantite=trim($_POST['RechercheQuantite']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE quantite=:quantite AND client=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionCompteClient,':quantite' => $RechercheQuantite)); 
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
<H1>Gestion des stocks</H1>
<H2>Produit existant</H2>

<table>
<TR>
    <th class="TableRouge">
        Gencode
    </TH>
    <th class="TableRouge">
        Description
    </TH>
    <th class="TableRouge">
        Categorie
    </TH>
    <th class="TableRouge">
        Prix
    </TH>
    <th class="TableRouge">
        Quantité
    </TH>
    <th class="TableRouge">
        Action
    </TH>
</TR>

<form name="form_recherche" action="" method="POST">
<TR>
    <td class="TableRouge">
        <input class="moyen" type="text" name="RechercheGencode" autofocus/>
    </td>
    <td class="TableRouge">
        <input class="description" type="text" name="RechercheDescription"/>
    </td>
    <td class="TableRouge">
        <select name="RechercheCategorie">
            <option value="">-- --</option><?php
            while ($Categorie=$SelectCategorieExist->fetch(PDO::FETCH_OBJ)) { 
            echo "<option value='".$Categorie->nom."'>".$Categorie->nom."</option>";
            } ?>
        </select>
    </td>
    <td class="TableRouge">
        <input class="mini" type="text" name="RecherchePrix"/>
    </td>
    <td class="TableRouge">
        <input class="mini" type="text" name="RechercheQuantite"/>
    </td>
    <td class="TableRouge">
        <input type="submit" class="ButtonRouge" name="MoteurRecherche" value="Rechercher"/>
    </td>
</TR>
</form>

<?php
while($ArticleExist=$SelectArticleExist->fetch(PDO::FETCH_OBJ)) { ?>
<form name="form_ajout_exist" action="" method="POST">
<TR>
    <td class="TableRouge">
        <?php echo $ArticleExist->gencode; ?>
    </TD>
    <td class="TableRouge" class="description">
        <?php echo stripslashes($ArticleExist->description); ?>
    </TD>
    <td class="TableRouge">
        <?php echo stripslashes($ArticleExist->categorie); ?>
    </TD>
    <td class="TableRouge">
        <?php echo number_format($ArticleExist->prix, 2,".", ""); ?>
    </TD>
    <td class="TableRouge">
        <?php echo $ArticleExist->quantite; ?>
    </TD>
    <td class="TableRouge">
        <a href="<?php echo $Home; ?>/DashBoard/Stock/Gestion/Modifier/?id=<?php echo $ArticleExist->id; ?>"><acronym title="Modifier"><img src="<?php echo $Home; ?>/lib/img/Button/modifier-rouge.png"/></acronym></a>
    </TD>
</TR>
</form>
<?php
} ?>
</table>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>