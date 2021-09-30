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
  
//Generer un code barre EAN13
$RecupInfoClient=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:hash");
$RecupInfoClient->bindParam(':hash', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoClient->execute();
$InfoClient=$RecupInfoClient->fetch(PDO::FETCH_OBJ);

$CountNbClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client");
$CountNbClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$CountNbClient->execute();
$NbClient=$CountNbClient->rowCount();

$CodePays="147";
$CodeEntreprise=$InfoClient->id;
$CodeEntreprise=trim(money_format('%=0(#4.0n', $CodeEntreprise));
$CodeFidelite=$NbClient + 1;
$CodeFidelite=trim(money_format('%=0(#5.0n', $CodeFidelite));
$Cle="";

/*for ($c=0;$c<=999;$c++) {
    $CodeFidelite=$c + 1;
    $CodeFidelite=trim(money_format('%=0(#5.0n', $CodeFidelite));
 
    $CodeBar12[$c]=$CodePays.$CodeEntreprise.$CodeFidelite;
    echo $CodeBar12[$c].";<BR />";
}*/

$CodeBar12=$CodePays.$CodeEntreprise.$CodeFidelite;

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

$CodeBar13=$CodePays.$CodeEntreprise.$CodeFidelite.$Cle;

//Faire une verification du code barre EAN13

if ((isset($_POST['Valider']))||(isset($_POST['Valider2']))) {
    
    $Carte=$_SESSION['carte']=$_POST['carte'];
    $Civilite=$_SESSION['civilite']=$_POST['civilite'];
    $Nom=$_SESSION['nom']=$_POST['nom'];
    $Prenom=$_SESSION['prenom']=$_POST['prenom'];
    $Adresse=$_SESSION['adresse']=$_POST['adresse'];
    $Cp=$_SESSION['cp']=$_POST['cp'];
    $Ville=$_SESSION['ville']=$_POST['ville'];
    $Tel=$_SESSION['tel']=trim($_POST['tel']);
    $Email=$_SESSION['email']=FiltreEmail('email');
    $Hash = md5(uniqid(rand(), true));
    $Now=time();
    
    $VerifExistEmail=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client AND email=:email");
    $VerifExistEmail->bindParam(':email', $Email, PDO::PARAM_STR);
    $VerifExistEmail->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $VerifExistEmail->execute();
    $VerifEmail=$VerifExistEmail->rowCount();
    
    $VerifExistCarte=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client AND carte=:carte");
    $VerifExistCarte->bindParam(':carte', $Carte, PDO::PARAM_STR);
    $VerifExistCarte->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $VerifExistCarte->execute();
    $VerifCarte=$VerifExistCarte->rowCount();
    
    if ($Email[0]==false) { 
        $Erreur=$Email[1];
    }
    elseif ($VerifEmail==1) {          
        $Erreur="Cette adresse E-mail existe déjà, veuillez en choisir une autre !</p>";
    }
    elseif ($VerifCarte==1) {          
        $Erreur="Cette carte est deja utilisé, veuillez en choisir une autre !</p>";
    }
    elseif (strlen($Carte)!=13) {
        $Erreur="Le code barre de la carte n'est pas valide </p>";
    }
    elseif (strlen($Nom)<=2) {
        $Erreur="Le nom doit être saisie </p>";
    }
    elseif (strlen($Prenom)<=2) {
        $Erreur="Le prenom doit être saisie </p>";
    }
    elseif (strlen($Adresse)<=2) {
        $Erreur="L'adresse doit être saisie </p>";
    }
    elseif (strlen($Cp)<=2) {
        $Erreur="Le code postal doit être saisie </p>";
    }
    elseif (strlen($Ville)<=2) {
        $Erreur="La ville doit être saisie </p>";
    }
    else {
        $InsertUser=$cnx->prepare("INSERT INTO ".$Prefix."Fidelite_Client (carte, civilite, nom, prenom, adresse, cp, ville, tel, email, hash, created, client) VALUES (:carte, :civilite, :nom, :prenom, :adresse, :cp, :ville, :tel, :email, :hash, :created, :client)"); 
        $InsertUser->bindParam(':carte', $Carte, PDO::PARAM_STR);
        $InsertUser->bindParam(':civilite', $Civilite, PDO::PARAM_STR);
        $InsertUser->bindParam(':nom', $Nom, PDO::PARAM_STR);
        $InsertUser->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
        $InsertUser->bindParam(':adresse', $Adresse, PDO::PARAM_STR);
        $InsertUser->bindParam(':cp', $Cp, PDO::PARAM_STR);
        $InsertUser->bindParam(':ville', $Ville, PDO::PARAM_STR);
        $InsertUser->bindParam(':tel', $Tel, PDO::PARAM_STR);
        $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
        $InsertUser->bindParam(':hash', $Hash, PDO::PARAM_STR); 
        $InsertUser->bindParam(':created', $Now, PDO::PARAM_STR); 
        $InsertUser->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR); 
        $InsertUser->execute();

        $Valid="Enregistrement effectué avec succés !</p>";
        
        unset($_SESSION['civilite']);
        unset($_SESSION['nom']);
        unset($_SESSION['prenom']);
        unset($_SESSION['adresse']);
        unset($_SESSION['cp']);
        unset($_SESSION['ville']);
        unset($_SESSION['tel']);
        unset($_SESSION['email']);
        
        if (isset($_POST['Valider2'])) {
            $HashTransac=md5(uniqid(rand(), true));
            $_SESSION['HashTransac']=$HashTransac;
            $_SESSION['etape']="Etape3";
            
            $InsertTransac=$cnx->prepare("INSERT INTO ".$Prefix."Historique (hash, hash_client, client, created) VALUES(:hash, :hash_client, :client, :created)");
            $InsertTransac->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $InsertTransac->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
            $InsertTransac->bindParam(':hash_client', $Hash, PDO::PARAM_STR);
            $InsertTransac->bindParam(':created', $Now, PDO::PARAM_STR);
            $InsertTransac->execute(); 
            
            header("location:".$Home."/DashBoard/Encaissement/?valid=".$Valid);
        }
        else {
            unset($_SESSION['carte']);
            header("location:".$Home."/DashBoard/Fidelite/Gestion/?valid=".urlencode($Valid));
        }
    }
}

if (isset($_POST['reset'])) {
    unset($_SESSION['carte']);
    unset($_SESSION['civilite']);
    unset($_SESSION['nom']);
    unset($_SESSION['prenom']);
    unset($_SESSION['adresse']);
    unset($_SESSION['cp']);
    unset($_SESSION['ville']);
    unset($_SESSION['tel']);
    unset($_SESSION['email']);
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
<H1 class="TitreRose">Nouveau client</H1>
<form action="" method="POST">
<H2>Carte de fidélité</H2>

<input class="login" type="text" name="carte" required="required" autofocus value="<?php echo $CodeBar13; ?>" placeholder="Carte N°:"/>
<BR /><BR />

<H2>Informations</H2>

<select class="login" name="civilite" required="required">
<option value="NULL" <?php if ($_SESSION['civilite']=="Mr") { echo "selected"; } ?> >Civilité*</option>
<option value="Mr" <?php if ($_SESSION['civilite']=="Mr") { echo "selected"; } ?> >Mr</option>
<option value="Mme" <?php if ($_SESSION['civilite']=="Mme") { echo "selected"; } ?> >Mme</option>
<option value="Mme" <?php if ($_SESSION['civilite']=="Mlle") { echo "selected"; } ?> >Mlle</option>
<option value="Sarl" <?php if ($_SESSION['civilite']=="Sarl") { echo "selected"; } ?> >Sarl</option>
<option value="SAS" <?php if ($_SESSION['civilite']=="SAS") { echo "selected"; } ?> >SAS</option>
<option value="SASU" <?php if ($_SESSION['civilite']=="SASU") { echo "selected"; } ?> >SASU</option>
<option value="Ets" <?php if ($_SESSION['civilite']=="Ets") { echo "selected"; } ?> >Ets</option>
</select>
<br /><BR />
<input class="login" type="text" name="nom" value="<?php echo $_SESSION['nom']; ?>" required="required" placeholder="Nom de famille*"/>
<br /><BR />
<input class="login" type="text" name="prenom" value="<?php echo $_SESSION['prenom']; ?>" required="required" placeholder="Prénom*"/>
<BR /><BR />

<H2>Adresse</H2>
<input class="login" type="text" name="adresse" value="<?php echo $_SESSION['adresse']; ?>" required="required" placeholder="Adresse*"/>
<br /><BR />
<input class="login" type="text" name="cp" value="<?php echo $_SESSION['cp']; ?>" required="required" placeholder="Code postal*"/>
<br /><BR />
<input class="login" type="text" name="ville" value="<?php echo $_SESSION['ville']; ?>" required="required" placeholder="Ville*"/>
<BR /><BR />

<H2>Contact</H2>
<input class="login" type="text" value="<?php echo $_SESSION['tel']; ?>" name="tel" placeholder="Téléphone*"/>
<br /><BR />
<input class="login" type="email" name="email" value="<?php echo $_SESSION['email']; ?>" required="required" placeholder="E-mail*"/>
<BR /><BR />

<input type="submit" class="ButtonRose" name="Valider2" value="Enregistrer et encaisser"/>
<BR /><BR />
<input type="submit" class="ButtonRose" name="Valider" value="Enregistrer"/>

<input type="submit" class="ButtonRose" name="reset" value="Réinitialiser"/>
</form>
</p>
<font color='#FF0000'>*</font> : Informations requises
</div>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>