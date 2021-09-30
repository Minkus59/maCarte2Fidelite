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

$SelectBoutique=$cnx->prepare("SELECT * FROM ".$Prefix."Boutique WHERE client=:client");
$SelectBoutique->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectBoutique->execute(); 

$SelectCompte=$cnx->prepare("SELECT * FROM ".$Prefix."compte_Boutique WHERE client=:client");
$SelectCompte->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectCompte->execute(); 

if(isset($_POST['Valider'])) {
     $Boutique=$_POST['boutique'];
     $Code1 = md5(uniqid(rand()), false);
     $Code2 = md5(uniqid(rand()), false);
     $Compte = substr($Code1, 0, 6);
     $Mdp = substr($Code2, 0, 4);
     $Now=time();
     
     $Entete ="From: ".$Societe." <".$Serveur.'>'."\r\n";
     $Entete .= 'MIME-Version: 1.0' . "\r\n";
     $Entete .='Content-Type: text/html; charset="iso-8859-15"'."\r\n";
     $Entete .='Content-Transfer-Encoding: 8bit';
     $Message ="<html><head><title>Votre espace personnel</title>
         </head><body>
         <font color='#9e2053'><H1>Votre espace personnel</H1></font>
         Votre compte sur ".$Home." a été créé<BR /><BR />
         Voici vos identifiants de connexion, merci de conserver ce message<BR /><BR />

         Compte n°: ".$Compte."<BR />
         Mot de passe : ".$Mdp."<BR /><BR />
         ____________________________________________________<BR />
         Cordialement ".$Societe."<br /><BR />

         <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
         </body></html>";
         
    $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."compte_Boutique (compte, client, created, boutique) VALUES (:compte, :client, :created, :boutique)");
    $InsertUser->bindParam(':compte', $Compte, PDO::PARAM_STR);
    $InsertUser->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $InsertUser->bindParam(':created', $Now, PDO::PARAM_STR); 
    $InsertUser->bindParam(':boutique', $Boutique, PDO::PARAM_STR); 
    $InsertUser->execute();

    $RecupCreated=$cnx->prepare("SELECT (created) FROM ".$Prefix."compte_Boutique WHERE compte=:compte");
    $RecupCreated->bindParam(':compte', $Compte, PDO::PARAM_STR);
    $RecupCreated->execute();

    $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
    $Salt=md5($DateCrea->created);
    $Mdp=md5($Mdp);
    $MdpCrypt=crypt($Mdp, $Salt);

    $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."compte_Boutique SET mdp=:mdpcrypt WHERE compte=:compte");
    $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
    $InsertMdp->bindParam(':compte', $Compte, PDO::PARAM_STR);
    $InsertMdp->execute();
    
    $RecupEmail=$cnx->prepare("SELECT (email) FROM ".$Prefix."compte WHERE hash=:client");
    $RecupEmail->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $RecupEmail->execute();
    $EmailCompte=$RecupEmail->fetch(PDO::FETCH_OBJ);

    if ($InsertMdp!=false) {
        if (!mail($EmailCompte->email, "Votre espace personnel", $Message, $Entete)) {
            $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !<br />"; 
        }
        else {
            $Valid.="Un E-mail de confirmation a été envoyé à  l'adresse suivante : ".$EmailCompte->email."<br />";
            header("location:".$Home."/DashBoard/Compte/Multi-boutique/Compte/?valid=".urlencode($Valid));
        }
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
    
<div id="Form_Middle">
<H1 class="TitreOrange">Créer un compte</H1>

<form name="form_ajout" action="" method="POST">
<select name="boutique" required="required">
    <option value="">Selectionner la boutique</option>
    <?php while($Boutique=$SelectBoutique->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Boutique->nom; ?>"><?php echo $Boutique->nom; ?></option>
    <?php } ?>
</select><BR /><BR />

<input type="submit" class="ButtonOrange" name="Valider" value="Créer le compte"/><BR /><BR />

<font color='#FF0000'>*</font> : Informations requises  <BR />

</form>
</div>   
    
<H1>Mes comptes multi-boutique</H1>

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
while($ListCompte=$SelectCompte->fetch(PDO::FETCH_OBJ)) { ?>
    <TR>
        <td class="TableOrange">
            <?php echo $ListCompte->compte; ?>
        </TD>
        <td class="TableOrange">
            <?php echo $ListCompte->boutique; ?>
        </TD>
        <td class="TableOrange">
            <a href="<?php echo $Home; ?>/DashBoard/Compte/Multi-boutique/Compte/Suppr.php?id=<?php echo $ListCompte->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Button/supprimer-violet.png"/></acronym></a>
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