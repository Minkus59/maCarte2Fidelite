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

//categorie
$SelectCategorieExist=$cnx->prepare("SELECT * FROM ".$Prefix."categorie WHERE client=:client");
$SelectCategorieExist->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectCategorieExist->execute();  

//Produit
$SelectProduit=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE client=:client AND id=:id");
$SelectProduit->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectProduit->bindParam(':id', $Id, PDO::PARAM_STR);
$SelectProduit->execute();    
$Produit=$SelectProduit->fetch(PDO::FETCH_OBJ);
        
//Modif articles
if (isset($_POST['Modifier'])) {
    
    $Description=$_POST['description'];
    $Categorie=$_POST['categorie'];
    $Prix=trim($_POST['prix']);
    $Quantite=$_POST['quantite'];
    
    if(!preg_match("#[0-9.]#", $Prix)) {
        $Erreur="Le Prix Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    elseif(!preg_match("#[0-9.]#", $Quantite)) {
        $Erreur="La quantité n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    elseif (strlen($Description)<=2) {
        $Erreur="La description doit être saisie !<br />";
    }
    else {
        $ModifArticle=$cnx->prepare("UPDATE ".$Prefix."produit SET description=:description, prix=:prix, categorie=:categorie, quantite=:quantite WHERE client=:client AND id=:id");
        $ModifArticle->bindParam(':description', $Description, PDO::PARAM_STR);
        $ModifArticle->bindParam(':categorie', $Categorie, PDO::PARAM_STR);
        $ModifArticle->bindParam(':prix', $Prix, PDO::PARAM_STR);
        $ModifArticle->bindParam(':quantite', $Quantite, PDO::PARAM_STR);
        $ModifArticle->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $ModifArticle->bindParam(':id', $Id, PDO::PARAM_STR);
        $ModifArticle->execute();

        $Valid="Article modifier avec succès";
        header("location:".$Home."/DashBoard/Stock/Gestion/");
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
<H1>Modification produit</H1>

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
        Prix unitaire
    </TH>
    <th class="TableRouge">
        Quantité
    </TH>
    <th class="TableRouge">
        Action
    </TH>
</TR>

<form name="form_mofif" action="" method="POST">
<TR>
    <td class="TableRouge">
        <input class="moyen" type="text" name="gencode" value="<?php echo $Produit->gencode; ?>" required="required"/>
    </TD>
    <td class="TableRouge">
        <textarea class="description" name="description" required="required"><?php echo $Produit->description; ?></textarea>
    </TD>
    <td class="TableRouge">
        <select name="categorie">
            <option value="<?php echo $Produit->categorie; ?>"><?php echo $Produit->categorie; ?></option><?php
            while ($Categorie=$SelectCategorieExist->fetch(PDO::FETCH_OBJ)) { 
            echo "<option value='".$Categorie->nom."'>".$Categorie->nom."</option>";
            } ?>
        </select>
    </TD>
    <td class="TableRouge">
        <input class="mini" type="text" name="prix" value="<?php echo $Produit->prix; ?>" required="required"/>
    </TD>
    <td class="TableRouge">
        <input class="mini" type="text" name="quantite" value="<?php echo $Produit->quantite; ?>" required="required"/>
    </TD>
    <td class="TableRouge">
        <input type="submit" class="ButtonRouge" name="Modifier" value="Modifier"/>
    </TD>
</TR>
</form>
</table>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>