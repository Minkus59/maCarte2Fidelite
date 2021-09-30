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

//categorie
$SelectCategorieExist=$cnx->prepare("SELECT * FROM ".$Prefix."categorie WHERE client=:client");
$SelectCategorieExist->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectCategorieExist->execute();     

//Generer un code barre EAN13
$RecupInfoClient=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:hash");
$RecupInfoClient->bindParam(':hash', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoClient->execute();
$InfoClient=$RecupInfoClient->fetch(PDO::FETCH_OBJ);

$CountNbProduit=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE client=:client");
$CountNbProduit->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$CountNbProduit->execute();
$NbProduit=$CountNbProduit->rowCount();

$CodePays="146";
$CodeEntreprise=$InfoClient->id;
$CodeEntreprise=trim(money_format('%=0(#4.0n', $CodeEntreprise));
$CodeProduit=$NbProduit + 1;
$CodeProduit=trim(money_format('%=0(#5.0n', $CodeProduit));
$Cle="";

$CodeBar12=$CodePays.$CodeEntreprise.$CodeProduit;

for ($i=0;$i<=11;$i++) {
    if($i%2==1) {
        $N[$i]=$CodeBar12[$i]*3;
    }
    else {
        $N[$i]=$CodeBar12[$i]*1;
    }
    $Total+=$N[$i]; 
}
$Reste=fmod($Total,10);
if ($Reste!=0) {
    $Cle=10-$Reste;
}
else {
    $Cle=$Reste;
}

$CodeBar13=$CodePays.$CodeEntreprise.$CodeProduit.$Cle;
        
//Ajout de nouveaux articles
if (isset($_POST['Ajouter1'])) {
    
    $Gencode=trim($_POST['gencode']);
    $Description=$_POST['description'];
    $Categorie=$_POST['categorie'];
    $Prix=trim($_POST['prix']);
    $Quantite=$_POST['quantite'];
    
    if (strlen($Gencode)!=13) {
        $Erreur="Le gencode semble incorecte !";
    }
    elseif(!preg_match("#[0-9.]#", $Prix)) {
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
        $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."produit (gencode, description, prix, categorie, quantite, client) VALUES (:gencode, :description, :prix, :categorie, :quantite, :client)");
        $InsertArticle->bindParam(':gencode', $Gencode, PDO::PARAM_STR);
        $InsertArticle->bindParam(':description', $Description, PDO::PARAM_STR);
        $InsertArticle->bindParam(':categorie', $Categorie, PDO::PARAM_STR);
        $InsertArticle->bindParam(':prix', $Prix, PDO::PARAM_STR);
        $InsertArticle->bindParam(':quantite', $Quantite, PDO::PARAM_STR);
        $InsertArticle->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $InsertArticle->execute();

        $Valid="Article ajouté avec succès";
        header("location:".$Home."/DashBoard/Stock/Nouveau/?valid=".urlencode($Valid));
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
<H1>Nouveau produit</H1>

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

<form name="form_ajout_exist" action="" method="POST">
<TR>
    <td class="TableRouge">
        <input class="moyen" type="text" name="gencode" value="<?php echo $CodeBar13; ?>" required="required"/>
    </TD>
    <td class="TableRouge">
        <textarea class="description" name="description" required="required" autofocus></textarea>
    </TD>
    <td class="TableRouge">
        <select name="categorie">
            <option value="">-- --</option><?php
            while ($Categorie=$SelectCategorieExist->fetch(PDO::FETCH_OBJ)) { 
            echo "<option value='".$Categorie->nom."'>".$Categorie->nom."</option>";
            } ?>
        </select>
    </TD>
    <td class="TableRouge">
        <input class="mini" type="text" name="prix" required="required"/>
    </TD>
    <td class="TableRouge">
        <input class="mini" type="text" name="quantite" required="required"/>
    </TD>
    <td class="TableRouge">
        <input type="submit" class="ButtonRouge" name="Ajouter1" value="Ajouter"/>
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