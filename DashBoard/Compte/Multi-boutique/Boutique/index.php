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
$Now=time();

$ListBoutique=$cnx->prepare("SELECT * FROM ".$Prefix."Boutique WHERE client=:client");
$ListBoutique->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$ListBoutique->execute();

if (isset($_POST['Valider'])) {

$Boutique=FiltreText('boutique');

  if ($Boutique===false) {
    $Erreur="Erreur !";
  }
    else {
    $Insert=$cnx->prepare("INSERT INTO ".$Prefix."Boutique (nom, client, created) VALUES (:nom, :client, :created)");
    $Insert->bindParam(':nom', $Boutique, PDO::PARAM_STR);
    $Insert->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $Insert->bindParam(':created', $Now, PDO::PARAM_STR);
    $Insert->execute();

    $Valid="Boutique créée avec sucèes";
    header("location:".$Home."/DashBoard/Compte/Multi-boutique/Boutique/?valid=".urlencode($Erreur));
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
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/navBoutique.inc.php"); ?>
</div>
<div id="ColoneRight">
<H1>Multi-Boutique</H1>

<font color='#FF0000'>Attention : Le compte de la boutique doit être activé depuis la boutique en question</font><BR /><BR />

<div id="Form_Middle">
<H1 class="TitreOrange">Créer une boutique</H1>

<form name="form_ajout" action="" method="POST">
<input type="text" name="boutique" placeholder="Nom de la boutique" required="required"/></p>

<input type="submit" class="ButtonOrange" name="Valider" value="Ajouter"/><BR /><BR />

<font color='#FF0000'>*</font> : Informations requises  <BR />

</form>
</div>

<BR />
<H1>Mes boutiques</H1>

<table>
<TR>
    <th class="TableOrange">
        Boutique
    </TH>
    <th class="TableOrange">
        Information
    </TH>
    <th class="TableOrange">
        Action
    </TH>
</TR>

<?php
while($List=$ListBoutique->fetch(PDO::FETCH_OBJ)) { ?>
    <TR>
        <td class="TableOrange">
            <?php echo $List->nom; ?>
        </TD>
        <td class="TableOrange">
            <?php if(!empty($List->ip)) {
            echo "Validé"; } else { echo "Non Validé"; } ?>
        </TD>
        <td class="TableOrange">
            <?php if(empty($List->ip)) { ?>
                <a href="<?php echo $Home; ?>/Validation/Boutique/?id=<?php echo $List->id; ?>&client=<?php echo $List->client; ?>">Activer la boutique</a><?php
            }
            else {
                ?><a href="<?php echo $Home; ?>/DashBoard/Compte/Multi-boutique/Boutique/Suppr.php?id=<?php echo $List->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Button/supprimer-violet.png"/></acronym></a><?php
            } ?>
        </TD>
    </TR>
    </form>
    <?php
} ?>
</table>
</div>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>