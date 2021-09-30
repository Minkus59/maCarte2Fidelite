<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");      

if ($_SESSION['Actif']=="0")  {
    $Erreur="Votre compte n'est plus actif.<br />";
    $Erreur.="- Soit votre période d'essai est terminé<br />";
    $Erreur.="- Soit votre abonnement est arrivé à echéance<br />";
    $Erreur.="Veuillez prolonger votre abonnement afin de retrouver vos services</p>";
    
    header("location:".$Home."/Abonnement/?erreur=".urlencode($Erreur));
}              

$Erreur.=$_GET['erreur'];
$Email=$_SESSION['email']=trim($_POST['email']);
$Societe=$_SESSION['societe']=trim($_POST['societe']);
$Siren=$_SESSION['SIREN']=trim($_POST['SIREN']);
$Tva=$_SESSION['TVA']=trim($_POST['TVA']);
$Nom=$_SESSION['nom']=trim($_POST['nom']);
$Prenom=$_SESSION['prenom']=trim($_POST['prenom']);
$Adresse2=$_SESSION['adresse']=$_POST['adresse'];
$Cp=$_SESSION['cp']=trim($_POST['cp']);
$Ville=$_SESSION['ville']=trim($_POST['ville']);
$Tel=$_SESSION['tel']=trim($_POST['tel']);
$Code = md5(uniqid(rand(), true));
$Client=trim($_POST['client']);
$Ip=$_SERVER['REMOTE_ADDR'];
$Mdp=trim($_POST['mdp']);
$Mdp2=trim($_POST['mdp2']);
$Temps=time();
$Essai="Période d'essai";

if (isset($_POST['cnx'])) {

    $VerifEmail=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE email=:email");
    $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifEmail->execute();
    $NbRowsEmail=$VerifEmail->rowCount();
    $RecupCompteEmail=$VerifEmail->fetch(PDO::FETCH_OBJ);
    
    $VerifCompte=$cnx->prepare("SELECT * FROM ".$Prefix."compte_Boutique WHERE compte=:compte");
    $VerifCompte->bindParam(':compte', $Email, PDO::PARAM_STR);
    $VerifCompte->execute();
    $NbRowsCompte=$VerifCompte->rowCount();
    $RecupCompteClient=$VerifCompte->fetch(PDO::FETCH_OBJ);
    
    if(($NbRowsCompte==1)&&($NbRowsEmail==0)) {
       
        $SelectEmail=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:hash");
        $SelectEmail->bindParam(':hash', $RecupCompteClient->client, PDO::PARAM_STR);
        $SelectEmail->execute();
        $CompteEmail=$SelectEmail->fetch(PDO::FETCH_OBJ);
        
        $Email=$CompteEmail->email;
    }
    else {
        if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $Email)) { 
            $Erreur="L'adresse e-mail n'est pas conforme !</p>";
        }
    }

    $VerifValid=$cnx->prepare("SELECT (valided) FROM ".$Prefix."compte WHERE valided=1 AND email=:email");
    $VerifValid->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifValid->execute();
    $NbRowsValid=$VerifValid->rowCount();

    $RecupClient=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE email=:email");
    $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
    $RecupClient->execute();
    $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);

    $Fin=$RecupC->essai+1209600;
    $Fin2=$RecupC->fin;

    if (strlen($Mdp)<=3) { 
        $Erreur="Le mot de passe n'est pas conforme !<br />";
        $Erreur.="Le mot de passe doit contenir au moin 4 caractères !</p>";
    }

    elseif ($NbRowsValid!=1) {
        $Erreur="Votre compte n'a pas été activé !<br />";
        $Erreur.="Lors de votre inscription un e-mail vous a été envoyé<br />";
        $Erreur.="Veuillez valider votre adresse e-mail en cliquant sur le lien.<br />";
        $Erreur.="vous pouvais toujours recevoir le mail a nouveau en cliquant sur ' recevoir '<br />";
        $Erreur.="<form action='' method='post'/><input type='hidden' name='client' value='".$RecupC->hash."'/><input type='hidden' name='email' value='".$RecupC->email."'/><input type='submit' name='Recevoir' value='Recevoir'/></form></p>";
    }

    else {
        
        if(($NbRowsCompte==1)&&($NbRowsEmail==0)) {
            $RecupCreated=$cnx->prepare("SELECT created FROM ".$Prefix."compte_Boutique WHERE client=:client");
            $RecupCreated->bindParam(':client', $RecupCompteClient->client, PDO::PARAM_STR);
            $RecupCreated->execute();

            $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
            $Salt=md5($DateCrea->created);
            $Salt2=md5($Mdp);
            $MdpCrypt=crypt($Salt2, $Salt);

            $VerifMdp=$cnx->prepare("SELECT * FROM ".$Prefix."compte_Boutique WHERE mdp=:mdp AND client=:client");
            $VerifMdp->bindParam(':mdp', $MdpCrypt, PDO::PARAM_STR);
            $VerifMdp->bindParam(':client', $RecupCompteClient->client, PDO::PARAM_STR);
            $VerifMdp->execute();
            $nb_rowsMdp=$VerifMdp->rowCount();
        }
        else {
            $RecupCreated=$cnx->prepare("SELECT created FROM ".$Prefix."compte WHERE email=:email");
            $RecupCreated->bindParam(':email', $Email, PDO::PARAM_STR);
            $RecupCreated->execute();

            $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
            $Salt=md5($DateCrea->created);
            $Salt2=md5($Mdp);
            $MdpCrypt=crypt($Salt2, $Salt);

            $VerifMdp=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE mdp=:mdp AND email=:email");
            $VerifMdp->bindParam(':mdp', $MdpCrypt, PDO::PARAM_STR);
            $VerifMdp->bindParam(':email', $Email, PDO::PARAM_STR);
            $VerifMdp->execute();
            $nb_rowsMdp=$VerifMdp->rowCount();
        }

        if ($nb_rowsMdp==1) {
            //Tous est ok
            //On verifie la localisation pour le multi boutique
            if(($NbRowsCompte==1)&&($NbRowsEmail==0)) {
                $VerifBoutique=$cnx->prepare("SELECT * FROM ".$Prefix."Boutique WHERE boutique=:boutique AND client=:client AND ip=:ip");
                $VerifBoutique->bindParam(':client', $RecupCompteClient->client, PDO::PARAM_STR);
                $VerifBoutique->bindParam(':boutique', $RecupCompteClient->boutique, PDO::PARAM_STR);
                $VerifBoutique->execute();
                $RecupBoutique=$VerifBoutique->fetch(PDO::FETCH_OBJ);
                
                if($RecupBoutique->ip!=$Ip) { 
                    $Erreur="Ce lieu de connection est différent du lieu dont vous avez activé le compte.<br/>"; 
                    $Erreur.="Merci de vous connectez de votre boutique enregistré<br/>"; 
                    header("location:".$Home."/DashBoard/?erreur=".urlencode($Erreur));
                }
            }

            if ($RecupC->actif==1) {
                if ($Temps>=$Fin) {
                    // periode d'essai terminer Mettre actif a 0
                    $UpdateActif=$cnx->prepare("UPDATE ".$Prefix."compte SET actif=0, abo='Désactivé' WHERE email=:email");
                    $UpdateActif->bindParam(':email', $Email, PDO::PARAM_STR);
                    $UpdateActif->execute();
                
                    $Erreur="- Votre période d'essai est arrivé à echéance<br />";
                    $Erreur.="Veuillez selectionner un abonnement afin de retrouver vos services<br />";
                    header("location:".$Home."/Abonnement/?erreur=".urlencode($Erreur));
                }
            }

            if ($RecupC->actif==2) {
                if ($Temps>=$Fin2) {
                    // Abo terminer Mettre actif a 0
                    $UpdateActif=$cnx->prepare("UPDATE ".$Prefix."compte SET actif=0, abo='Désactivé' WHERE email=:email");
                    $UpdateActif->bindParam(':email', $Email, PDO::PARAM_STR);
                    $UpdateActif->execute();
                }
            }

            $RecupClient=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE email=:email");
            $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
            $RecupClient->execute();
            $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);


            if ($RecupC->actif==0) {
                $_SESSION['NeuroCompteClient']=$RecupC->hash;
                $_SESSION['Actif']="0";
                $Erreur="Votre compte n'est plus actif.<br />";
                $Erreur.="- Soit votre période d'essai est terminé<br />";
                $Erreur.="- Soit votre abonnement est arrivé à echéance<br />";
                $Erreur.="Veuillez prolonger votre abonnement afin de retrouver vos services</p>";
                header("location:".$Home."/DashBoard/Compte/Abonnement/?erreur=".urlencode($Erreur));
            }

            //Actif 0 > Expirer, Actif 1 > période d'essai; Actif 2 > Abo en cour; Actif 3 > Abo VIP;

            if ($RecupC->actif==1) {
                $RecupClient=$cnx->prepare("UPDATE ".$Prefix."compte SET last_cnx=NOW() WHERE email=:email");
                $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
                $RecupClient->execute();
                
                $InsertSecu=$cnx->prepare("INSERT INTO ".$Prefix."securite (ip, hash, created) VALUES (:ip, :hash, NOW())");
                $InsertSecu->bindParam(':ip', $Ip, PDO::PARAM_STR);
                $InsertSecu->bindParam(':hash', $RecupC->hash, PDO::PARAM_STR);
                $InsertSecu->execute();

                $_SESSION['NeuroCompteClient']=$RecupC->hash;
                $_SESSION['Actif']="1";

                header("location:".$Home."/DashBoard/");
            }

            if ($RecupC->actif==2) {
                $RecupClient=$cnx->prepare("UPDATE ".$Prefix."compte SET last_cnx=NOW() WHERE email=:email");
                $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
                $RecupClient->execute();
                
                $InsertSecu=$cnx->prepare("INSERT INTO ".$Prefix."securite (ip, hash, created) VALUES (:ip, :hash, NOW())");
                $InsertSecu->bindParam(':ip', $Ip, PDO::PARAM_STR);
                $InsertSecu->bindParam(':hash', $RecupC->hash, PDO::PARAM_STR);
                $InsertSecu->execute();

                $_SESSION['NeuroCompteClient']=$RecupC->hash;
                $_SESSION['Actif']="2";

                header("location:".$Home."/DashBoard/");
            }

            if ($RecupC->actif==3) {
                $RecupClient=$cnx->prepare("UPDATE ".$Prefix."compte SET last_cnx=NOW() WHERE email=:email");
                $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
                $RecupClient->execute();
                
                $InsertSecu=$cnx->prepare("INSERT INTO ".$Prefix."securite (ip, hash, created) VALUES (:ip, :hash, NOW())");
                $InsertSecu->bindParam(':ip', $Ip, PDO::PARAM_STR);
                $InsertSecu->bindParam(':hash', $RecupC->hash, PDO::PARAM_STR);
                $InsertSecu->execute();

                $_SESSION['NeuroCompteClient']=$RecupC->hash;
                $_SESSION['Actif']="3";

                header("location:".$Home."/DashBoard/");
            }
        }

        else {
            $Erreur="Le mot de passe ne correspond pas à ce compte !</p>";       
        }
    }
}


if (isset($_POST['Inscription'])) {
    
    $Compteur=$cnx->prepare("SELECT * FROM ".$Prefix."compte");
    $Compteur->execute();
    $NbCompte=$Compteur->rowCount();

    if($NbCompte==0) {
        $Preparation1=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."compte (
        id int(32) unsigned NOT NULL AUTO_INCREMENT,
        last_cnx datetime DEFAULT NULL,
        valided int(1) NOT NULL DEFAULT '0',
        essai int(20) DEFAULT NULL,
        actif int(32) NOT NULL DEFAULT '0',
        abo varchar(30) DEFAULT NULL,
        debut int(30) DEFAULT NULL,
        fin int(30) DEFAULT NULL,
        email varchar(50) NOT NULL,
        mdp varchar(16) NOT NULL,
        societe varchar(50) NOT NULL,
        siren varchar(25) NOT NULL,
        tva varchar(25) NOT NULL,
        nom varchar(16) NOT NULL,
        prenom varchar(16) NOT NULL,
        tel varchar(16) NOT NULL,
        adresse longtext,
        cp varchar(5) DEFAULT NULL,
        ville varchar(50) DEFAULT NULL,
        hash varchar(32) NOT NULL,
        created datetime NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY email (email),
        UNIQUE KEY hash (hash)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        
        $Preparation2=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."secu_mdp (
        id int(32) unsigned NOT NULL AUTO_INCREMENT,
        code varchar(32) NOT NULL,
        hash varchar(32) NOT NULL,
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $Preparation3=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."securite (
        id int(32) unsigned NOT NULL AUTO_INCREMENT,
        ip varchar(32) NOT NULL,
        created datetime NOT NULL,
        hash varchar(32) NOT NULL,
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    } 

    $Entete ='From: "'.$NoReply.'"<'.$Serveur.'>'."\n";                      
    $Entete .='Content-Type: text/html; charset="iso-8859-15"'."\n";                       
    $Message ="<html><head><title>Validation d'inscription</title>
        </head><body>
        <font color='#9e2053'><H1>Validation d'inscription</H1></font>
        Merci de vous être inscrit.</p>
        Afin de pouvoir vous connecter, cliquer sur le lien suivant pour valider votre inscription.</p>
        <a href='".$Home."/Validation/?id=$Code&Valid=1'>Cliquez ici</a></p>                   
        ____________________________________________________</p>
        Cordialement<br />
        ".$Home."</p>
        <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
        </body></html>";

    $Entete2 ='From: "'.$NoReply.'"<'.$Serveur.'>'."\n";           
    $Entete2 .='Content-Type: text/html; charset="iso-8859-15"'."\n";   
    $Message2 ="<html><head><title>Inscription</title>
        </head><body>
        <font color='#9e2053'><H1>Inscription</H1></font>           
        Une nouvelle inscription !</p>
        Email : ".$Email."<br />
        Nom : ".$Nom."<br />
        Prenom : ".$Prenom."<br />
        Téléphone : ".$Tel."<br />
        ____________________________________________________</p>
        Cordialement<br />
        ".$Home."</p>
        <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
        </body></html>";

    $VerifEmail=$cnx->prepare("SELECT (email) FROM ".$Prefix."compte WHERE email=:email");
    $VerifEmail->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifEmail->execute();
    $NbRowsEmail=$VerifEmail->rowCount();

    if (!preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $Email)) { 
        $Erreur="L'adresse e-mail n'est pas conforme !</p>";
    }
    elseif ($NbRowsEmail==1) {          
        $Erreur="Cette adresse E-mail existe déjà, veuillez en choisir une autre !</p>";
    }
    elseif (strlen($Mdp)<=7) { 
        $Erreur="Le mot de passe n'est pas conforme !<br />";
        $Erreur.="Le mot de passe doit contenir au moin 8 caractères !</p>";
    }
    elseif ($Mdp!=$Mdp2) {
        $Erreur="Les mot de passe saisie doivent êtres identique !</p>";
    }
    elseif (strlen($Societe)<=2) { 
        $Erreur="Le nom de la société doit etre saisie !</p>";
    }
    elseif (strlen($Siren)!=9) { 
        $Erreur="Le numéro de SIREN doit etre saisie !</p>";
    }
    elseif (strlen($Tva)<=2) { 
        $Erreur="Le numéro de TVA doit etre saisie !</p>";
    }
    elseif (strlen($Nom)<=2) { 
        $Erreur="Le nom doit etre saisie !</p>";
    }
    elseif (strlen($Prenom)<=2) { 
        $Erreur="Le prénom doit etre saisie !</p>";
    }
    elseif (strlen($Tel)<=9) { 
        $Erreur="Le numéro de téléphone doit etre saisie !</p>";
    }
    elseif (strlen($Adresse2)<=2) { 
        $Erreur="L'adresse doit être saisie !<br />";
    }
    elseif (strlen($Cp)<=2) { 
        $Erreur="Le code postal doit être saisie !<br />";
    }
    elseif (strlen($Ville)<=2) { 
        $Erreur="La ville doit être saisie !<br />";
    }
    else {
        $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."compte (email, societe, siren, tva, nom, prenom, adresse, cp, ville, tel , hash, actif, abo, created) VALUES (:email, :societe, :siren, :tva, :nom, :prenom, :adresse, :cp, :ville, :tel, :hash, 1, :abo, NOW())");
        $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
        $InsertUser->bindParam(':societe', $Societe, PDO::PARAM_STR);
        $InsertUser->bindParam(':siren', $Siren, PDO::PARAM_STR);
        $InsertUser->bindParam(':tva', $Tva, PDO::PARAM_STR);
        $InsertUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $InsertUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
        $InsertUser->bindParam(':adresse', $Adresse2, PDO::PARAM_STR);
        $InsertUser->bindParam(':cp', $Cp, PDO::PARAM_STR);
        $InsertUser->bindParam(':ville', $Ville, PDO::PARAM_STR);
        $InsertUser->bindParam(':tel', $Tel, PDO::PARAM_STR);
        $InsertUser->bindParam(':hash', $Code, PDO::PARAM_STR);
        $InsertUser->bindParam(':abo', $Essai, PDO::PARAM_STR);
        $InsertUser->execute();

        $RecupCreated=$cnx->prepare("SELECT created FROM ".$Prefix."compte WHERE email=:email");
        $RecupCreated->bindParam(':email', $Email, PDO::PARAM_STR);
        $RecupCreated->execute();

        $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
        $Salt=md5($DateCrea->created);
        $Salt2=md5($Mdp);
        $MdpCrypt=crypt($Salt2, $Salt);

        $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."compte SET mdp=:mdpcrypt WHERE email=:email");
        $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
        $InsertMdp->bindParam(':email', $Email, PDO::PARAM_STR);
        $InsertMdp->execute();

        $Insertsecu=$cnx->prepare("INSERT INTO ".$Prefix."securite (ip, created, hash) VALUE(:ip, NOW(), :client)");
        $Insertsecu->bindParam(':ip', $Ip, PDO::PARAM_STR);
        $Insertsecu->bindParam(':client', $Code, PDO::PARAM_STR);
        $Insertsecu->execute();
        
        $MessageMail='<h1><span style="color: #00ff00;"><strong>F&eacute;licitation,</strong></span></h1>
                <p>Nous vous remercions de votre fid&eacute;lit&eacute; et nous sommes heureux de vous faire parvenir votre bon de fid&eacute;lit&eacute; en pi&egrave;ce jointe</p>
                <p><span style="color: #33cccc;">En esp&eacute;rent vous revoir prochainement</span></p>';
                
        //Insert Param
        $InsertParam=$cnx->prepare("INSERT INTO ".$Prefix."Fidelite_Param (client, message) VALUE(:client, :message)");
        $InsertParam->bindParam(':client', $Code, PDO::PARAM_STR);
        $InsertParam->bindParam(':message', $MessageMail, PDO::PARAM_STR);
        $InsertParam->execute();
        
        $InsertParam2=$cnx->prepare("INSERT INTO ".$Prefix."Option (client) VALUE(:client)");
        $InsertParam2->bindParam(':client', $Code, PDO::PARAM_STR);
        $InsertParam2->execute();

        if ((!$InsertUser)||(!$InsertMdp)||(!$RecupCreated)||(!$VerifEmail)) {

            $DeleteUser=$cnx->prepare("DELETE FROM ".$Prefix."compte WHERE email=:email");
            $DeleteUser->bindParam(':email', $Email, PDO::PARAM_STR);
            $DeleteUser->execute();

            $Erreur="L'enregistrement des données à échouée, veuillez réessayer ultérieurement !</p>";
        }
        
        else {
            if (!mail($Email, "Validation d'inscription - ".$Societe, $Message, $Entete)) {                             
                $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";                       
            }

            elseif (!mail($Destinataire, "Nouvelle inscription - ".$Societe, $Message2, $Entete2)) {
                $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";                           
            }
                        
            else {
                $Valid="Bonjour, ".$Nom." ".$Prenom."<br />";
                $Valid.="Merci de vous êtres inscrit<br />";
                $Valid.="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
                $Valid.="Veuillez valider votre adresse e-mail avant de vous connecter !</p>";
                
                unset($_SESSION['email']);
                unset($_SESSION['nom']);
                unset($_SESSION['prenom']);
                unset($_SESSION['tel']);
                unset($_SESSION['adresse']);
                unset($_SESSION['cp']);
                unset($_SESSION['ville']);
                unset($_SESSION['SIREN']);
                unset($_SESSION['TVA']);
                unset($_SESSION['societe']);
            }
        }
    }
}


if ((isset($_POST['Recevoir']))&&($_POST['Recevoir']=="Recevoir")) {

    $Entete ='From: "'.$NoReply.'"<'.$Serveur.'>'."\r\n";                    
    $Entete .='Content-Type: text/html; charset="iso-8859-15"'."\r\n";                       
    $Message ="<html><head><title>Validation d'inscription</title>
        </head><body>
        <font color='#9e2053'><H1>Validation d'inscription</H1></font>          
        Veuillez cliquer sur le lien suivant pour valider votre inscription sur ".$Home.".</p>                       
        <a href='".$Home."/Validation/?id=$Client&Valid=1'>Cliquez ici</a></p>                 
        ____________________________________________________</p>
        Cordialement<br />
        ".$Home."</p>
        <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou protégées par la loi. Si vous n'en êtes pas le véritable destinataire ou si vous l'avez reçu par erreur, informez-en immédiatement son expéditeur et détruisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
        </body></html>";

    if (!mail($Email, "Validation d'inscription - ".$Societe, $Message, $Entete)) {                             
        $Erreur="L'e-mail de confirmation n'a pu être envoyé, vérifiez que vous l'avez entré correctement !</p>";
    }
                
    else {
        $Erreur="Un E-mail de confirmation vous a été envoyé à l'adresse suivante : ".$Email."<br />";
        $Erreur.="Veuillez valider votre adresse e-mail avant de vous connecter !</p>";                 
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
  
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>
     
<?php if ($Cnx_CompteClient==false) { ?>

<div id="Form_Middle">
    <H1 class="TitreBleu">Inscription</H1>
    Essayer le sevice gratuitement pendant 14 jours !<p>

    <form id="form_inscription" action="" method="POST">
    <input class="long" type="text" name="societe" placeholder="Nom de société" required="required" value="<?php echo $Societe; ?>"/>
    <br />     
    <input class="long" type="text" name="SIREN" placeholder="Numéro de SIREN" required="required" value="<?php echo $Siren; ?>"/>
    <br />   
    <input class="long" type="text" name="TVA" placeholder="Numéro de TVA" required="required" value="<?php echo $Tva; ?>"/>
    <br />  <br />  
    <input class="long" type="text" name="nom" placeholder="Nom" required="required" value="<?php echo $Nom; ?>"/>
    <br />
    <input class="long" type="text" name="prenom" placeholder="Prénom" required="required" value="<?php echo $Prenom; ?>"/>
    <br />
    <input class="long" type="text" name="adresse" placeholder="Adresse" required="required" value="<?php echo $Adresse2; ?>"/>
    <br />
    <input class="long" type="text" name="cp" placeholder="Code postal" required="required" value="<?php echo $Cp; ?>"/>
    <br />
    <input class="long" type="text" name="ville" placeholder="Ville" required="required" value="<?php echo $Ville; ?>"/>
    <br /><br />  
    <input class="long" type="text" name="tel" placeholder="Numéro de téléphone" required="required" value="<?php echo $Tel; ?>"/>
    <br />
    <input class="long" type="email" name="email" placeholder="Adresse e-mail" required="required" value="<?php echo $Email; ?>"/>
    <br /><br />  
    <input class="long" type="password" name="mdp" placeholder="Créer un mot de passe" required="required"/>
    <br />
    <input class="long" type="password" name="mdp2" placeholder="Confirmer le mot de passe" required="required"/>
    </p>
    <input type="submit" class="ButtonBleu" name="Inscription" value="M'inscrire"/>
    </p>
    </form>
</div>

<div id="Form_Middle">
  <h1 class="TitreBleu">Connexion</h1>

    <form id="form_cnx" action="" method="POST">
    <input class="long" name="email" type="text" placeholder="Adresse e-mail ou Compte n°" required="required"/>
    <br />
    <input class="long" name="mdp" type="password" placeholder="Mot de passe" required="required"/>
    </p>
    <input type="submit" class="ButtonBleu" name="cnx" value="Connexion"/></p>
    <a href="<?php echo $Home; ?>/Securite/">Mot de passe oublié ?</a>
    <p>
    </form>
</div>

<?php
}
else {
    echo "Vous êtes connecté !<p>";
    echo '<a href='.$Home.'/deconnexion.php>Déconnexion</a></p>';
}
?>

</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>