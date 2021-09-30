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

$Client=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:client");
$Client->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$Client->execute();
$InfoClient=$Client->fetch(PDO::FETCH_OBJ);

$Email=trim($_POST['email']);
$Mdp=trim($_POST['mdp']);
$Mdp2=trim($_POST['mdp2']);
$Nom=$_POST['nom'];
$Prenom=$_POST['prenom'];
$Tel=trim($_POST['tel']);
$Cp=trim($_POST['cp']);
$Adresse=$_POST['adresse'];
$Ville=$_POST['ville'];
$Societe=$_POST['societe'];
$Siren=$_POST['siren'];
$Tva=$_POST['tva'];
$Genre=$_GET['genre'];

$Entete ='From: "no-reply@neuro-soft.fr"<postmaster@neuro-soft.fr>'."\r\n"; 
$Entete .= 'MIME-Version: 1.0' . "\r\n";                        
$Entete .='Content-Type: text/html; charset="iso-8859-1"'."\r\n";                       
$Entete .='Content-Transfer-Encoding: 8bit'; 
$Message ="<html><head><title>Changement d'adresse e-mail</title>
    </head><body>
    <font color='#9e2053'><H1>Changement d'adresse e-mail</H1></font>           
    Veuillez cliquer sur le lien suivant pour valider votre inscription sur devis.neuro-soft.fr .</p>                       
    <a href='".$Home."/Validation/?id=$InfoClient->hash&Valid=1'>Cliquez ici</a></p>                   
    ____________________________________________________</p>
    Cordialement<br />
    ".$Home."</p>
    <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
    </body></html>";

if (isset($_POST['Modifier0'])) {

    $VerifEmail=$cnx->prepare("SELECT (email) FROM ".$Prefix."compte WHERE email=:email");
    $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifEmail->execute();
    $NbRowsEmail=$VerifEmail->rowCount();

    if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $Email)) { 
        $Erreur="L'adresse e-mail n'est pas conforme !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }

    elseif ($NbRowsEmail==1) {          
        $Erreur="Cette adresse E-mail existe déjà, veuillez en choisir une autre !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));       
    }
    else {
        $ModiftUser=$cnx->prepare("UPDATE ".$Prefix."compte SET email=:email, valided=0 WHERE hash=:hash");
        $ModiftUser->bindParam(':email', $Email, PDO::PARAM_STR);
        $ModiftUser->bindParam(':hash', $SessionCompteClient, PDO::PARAM_STR);
        $ModiftUser->execute();

        if (!mail($Email, "Changement d'adresse e-mail", $Message, $Entete)) {                          
            $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
            header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));       
        }
                        
        else {
            $Erreur="Enregistrement effectué avec succès !<br />";
            $Erreur.="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."</p>";
            header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));           
        }
    }
}
if (isset($_POST['Modifier1'])) {

    if (strlen($Mdp)<=7) { 
        $Erreur="Le mot de passe n'est pas conforme !<br />";
        $Erreur.="Le mot de passe doit contenir au moin 8 caractères !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }

    elseif ($Mdp!=$Mdp2) {
        $Erreur="Les mot de passe saisie doivent êtres identique !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }

    else {
        $RecupCreated=$cnx->prepare("SELECT created FROM ".$Prefix."compte WHERE hash=:hash");
        $RecupCreated->bindParam(':hash', $SessionCompteClient, PDO::PARAM_STR);
        $RecupCreated->execute();

        $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
        $Salt=md5($DateCrea->created);
        $MdpCrypt=crypt($Mdp2, $Salt);

        $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."compte SET mdp=:mdpcrypt WHERE hash=:hash");
        $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
        $InsertMdp->bindParam(':hash', $SessionCompteClient, PDO::PARAM_STR);
        $InsertMdp->execute();

        $Erreur="Enregistrement effectué avec succès !</p>";
    header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }
}

if (isset($_POST['Modifier3'])) {

    if (strlen($Nom)<=2) { 
        $Erreur="Le nom doit etre saisie !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }

    elseif (strlen($Prenom)<=2) { 
        $Erreur="Le prénom doit etre saisie !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }

    elseif (strlen($Tel)<=9) { 
        $Erreur="Le numéro de téléphone doit etre saisie !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }

    elseif (strlen($Adresse)<=2) { 
        $Erreur="l'adresse doit etre saisie !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }

    elseif (strlen($Cp)<=4) { 
        $Erreur="Le code postal doit etre saisie !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }

    elseif (strlen($Societe)<=2) { 
        $Erreur="Le nom de la société doit etre saisie !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }
    elseif (strlen($Siren)<=2) { 
        $Erreur="Le numéro de siren doit etre saisie !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }
    elseif (strlen($Tva)<=2) { 
        $Erreur="Le numéro de tva doit etre saisie !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }
    elseif (strlen($Ville)<=2) { 
        $Erreur="La ville doit etre saisie !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));   
    }
    else {

        $InsertInfo=$cnx->prepare("UPDATE ".$Prefix."compte SET societe=:societe, siren=:siren, tva=:tva ,nom=:nom, prenom=:prenom, tel=:tel, adresse=:adresse, cp=:cp, ville=:ville WHERE hash=:hash");
        $InsertInfo->bindParam(':societe', $Societe, PDO::PARAM_STR);
        $InsertInfo->bindParam(':siren', $Siren, PDO::PARAM_STR);
        $InsertInfo->bindParam(':tva', $Tva, PDO::PARAM_STR);
        $InsertInfo->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $InsertInfo->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
        $InsertInfo->bindParam(':tel', $Tel, PDO::PARAM_STR);
        $InsertInfo->bindParam(':adresse', $Adresse, PDO::PARAM_STR);
        $InsertInfo->bindParam(':cp', $Cp, PDO::PARAM_STR);
        $InsertInfo->bindParam(':ville', $Ville, PDO::PARAM_STR);
        $InsertInfo->bindParam(':hash', $SessionCompteClient, PDO::PARAM_STR);
        $InsertInfo->execute();
    
    $Erreur="Enregistrement effectué avec succès !</p>";
        header("location:".$Home."/DashBoard/Compte/Informations/?erreur=".urlencode($Erreur));       
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
<?php
if ($Genre=="connexion") {
    ?>
<div id="Form_Middle">
<H1 class="TitreOrange">E-mail</H1></p>
    <form method="POST" action="">
    <label class="col_2">Adresse E-mail<font color='#FF0000'>*</font></label>
    <input type="email" name="email" value="<?php echo $InfoClient->email; ?>" required="required"/>
    <br /><br />
    <input type="submit" class="ButtonOrange" name="Modifier0" value="Modifier"/>
    </form>
    </div>
    
<div id="Form_Middle">
<H1 class="TitreOrange">Mot de passe</H1></p>
    <form method="POST" action="">
    <label class="col_2">Créer un mot de passe<font color='#FF0000'>*</font></label>
    <input type="password" name="mdp" required="required"/>
    <br />
    <label class="col_2">Confirmer le mot de passe<font color='#FF0000'>*</font></label>
    <input type="password" name="mdp2" required="required"/>
    <br /><br />
    <input type="submit" class="ButtonOrange" name="Modifier1" value="Modifier"/>
    </form>
    </div>
<?php
}
if ($Genre=="personnelles") {
    ?>
<div id="Form_Middle">
<H1 class="TitreOrange">Informations personnelles</H1></p>
    <form method="POST" action="">
    <label class="col_1">Nom de Société<font color='#FF0000'>*</font></label>
    <input type="text" name="societe" value="<?php echo stripslashes($InfoClient->societe); ?>" required="required"/>
    <br />
    <label class="col_1">Numéro de SIREN<font color='#FF0000'>*</font></label>
    <input type="text" name="siren" value="<?php echo stripslashes($InfoClient->siren); ?>" required="required"/>
    <br />
    <label class="col_1">Numéro de TVA<font color='#FF0000'>*</font></label>
    <input type="text" name="tva" value="<?php echo stripslashes($InfoClient->tva); ?>" required="required"/>
    <br />
    <label class="col_1">Nom<font color='#FF0000'>*</font></label>
    <input type="text" name="nom" value="<?php echo stripslashes($InfoClient->nom); ?>" required="required"/>
    <br />
    <label class="col_1">Prénom<font color='#FF0000'>*</font></label>
    <input type="text" name="prenom" value="<?php echo stripslashes($InfoClient->prenom); ?>" required="required"/>
    <br />
    <label class="col_1">Numéro de téléphone<font color='#FF0000'>*</font></label>
    <input type="text" name="tel" value="<?php echo $InfoClient->tel; ?>" required="required"/>
    <br />
    <label class="col_1">Adresse de siège<font color='#FF0000'>*</font></label>
    <input type="text" name="adresse" value="<?php echo stripslashes($InfoClient->adresse); ?>" required="required"/>
    <br />
    <label class="col_1">Code postal<font color='#FF0000'>*</font></label>
    <input type="text" name="cp" value="<?php echo $InfoClient->cp; ?>" required="required"/>
    <br />
    <label class="col_1">Ville<font color='#FF0000'>*</font></label>
    <input type="text" name="ville" value="<?php echo stripslashes($InfoClient->ville); ?>" required="required"/>
    <br /><br />
    <input type="submit" class="ButtonOrange" name="Modifier3" value="Modifier"/>
    </form>
    </div>
    <?php
    }
?>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>