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

$SelectCompte=$cnx->prepare("SELECT * FROM ".$Prefix."compte_Multi-boutique WHERE client=:client");
$SelectCompte->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectCompte->execute();             
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
<H1>Mes comptes multi-boutique</H1>

<a href="<?php echo $Home; ?>/DashBoard/Compte/Multi-boutique/Nouveau/"><acronym title="Créer un compte"><img src="<?php echo $Home; ?>/lib/img/button-ajout.png"/></acronym></a>

        <table>
        <TR>
            <th class="TableOrange">
                Compte N°
            </TH>
            <th class="TableOrange">
                Boutique
            </TH>
            <th class="TableOrange">
                Action
            </TH>
        </TR>

        <?php
        while($ArticleExist=$SelectArticleExist->fetch(PDO::FETCH_OBJ)) { ?>
            <TR>
                <td class="TableOrange">
                    
                </TD>
                <td class="TableOrange">
                    
                </TD>
                <td class="TableOrange">
                    
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