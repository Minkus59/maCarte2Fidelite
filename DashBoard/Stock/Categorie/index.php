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

$ListCategorie=$cnx->prepare("SELECT * FROM ".$Prefix."categorie WHERE client=:client");
$ListCategorie->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$ListCategorie->execute();

if (isset($_POST['Valider'])) {

$Categorie=FiltreText('categorie');

  if ($Categorie===false) {
    $Erreur="Erreur !";
  }
    else {
    $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."categorie (nom, client) VALUES (:nom, :client)");
    $InsertArticle->bindParam(':nom', $Categorie, PDO::PARAM_STR);
    $InsertArticle->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $InsertArticle->execute();

    $Erreur="Catégorie ajouter avec sucèes";
    header("location:".$Home."/DashBoard/Stock/Categorie/?erreur=".urlencode($Erreur));
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

<div id="Form_Middle">
<H1 class="TitreRouge">Catégorie</H1>

<form name="form_ajout" action="" method="POST">
<input class="login" type="text" name="categorie" placeholder="Nom de la catégorie" required="required"/></p>

<input type="submit" class="ButtonRouge" name="Valider" value="Ajouter"/><BR /><BR />

<font color='#FF0000'>*</font> : Informations requises  <BR />

</form>
</div>
<BR />
<H1>Liste des catégories existante</H1>

<table>
    <TR>
        <th class="TableRouge">
             Categorie
        </TH>
        <th class="TableRouge">
             Action
        </TH>
    </TR>
<?php while ($List=$ListCategorie->fetch(PDO::FETCH_OBJ)) {  ?>
    <TR>
        <td class="TableRouge">
             <?php echo $List->nom;  ?>
        </TD>
        <td class="TableRouge">
             <a href="<?php echo $Home; ?>/DashBoard/Stock/Categorie/SupprCategorie.php?id=<?php echo $List->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Button/supprimer-rouge.png"/></acronym></a>
        </TD>
    </TR>
    <?php }  ?>
</table>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>