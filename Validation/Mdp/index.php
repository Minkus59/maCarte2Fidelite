<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");

$Client=trim($_GET['id']);
$Hash=trim($_GET['hash']);
$Mdp=trim($_POST['mdp']);
$Mdp2=trim($_POST['mdp2']);

if (isset($_POST['Valider'])) {

    if (strlen($Mdp)<=7) { 
        $Erreur="Le mot de passe n'est pas conforme !<br />";
        $Erreur.="Le mot de passe doit contenir au moin 8 caractères !</p>";
    }

    elseif ($Mdp!=$Mdp2) {
        $Erreur="Les mot de passe saisie doivent êtres identique !</p>";
    }

    else {
        $RecupCreated=$cnx->prepare("SELECT (created) FROM ".$Prefix."compte WHERE hash=:client");
        $RecupCreated->bindParam(':client', $Client, PDO::PARAM_STR);
        $RecupCreated->execute();

        $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);
        $Salt=md5($DateCrea->created);
        $Salt2=md5($Mdp);
        $MdpCrypt=crypt($Salt2, $Salt);

        $InsertMdp=$cnx->prepare("UPDATE ".$Prefix."compte SET mdp=:mdpcrypt WHERE hash=:client");
        $InsertMdp->bindParam(':mdpcrypt', $MdpCrypt, PDO::PARAM_STR);
        $InsertMdp->bindParam(':client', $Client, PDO::PARAM_STR);
        $InsertMdp->execute();

        $DeleteProcedure=$cnx->prepare("DELETE FROM ".$Prefix."secu_mdp WHERE hash=:client");
        $DeleteProcedure->bindParam(':client', $Client, PDO::PARAM_STR);
        $DeleteProcedure->execute();

        $Erreur= "Votre mot de passe a bien été validé !<br />";
        $Erreur.= "Vous pouvez dès à présent vous connecter !<br />";
        $Erreur.= '<input type=button onClick=(parent.location="'.$Home.'") value="Se connecter"></p>';       
    }
}

?>

<!-- **************************************
*** Script réalisé par Helinckx Michael ***
*********** www.neuro-soft.fr *************
****************************************-->

<!DOCTYPE HTML>
<html>

<head>   
<meta charset="ISO-8859-15"/>
<title><?php echo $Societe; ?></title>
<meta name="category" content="Accueil" />
<meta name="description" content="<?php echo $Societe; ?>" />
<meta name="robots" content="index, follow"/>
<meta name="author" content="NeuroSoft Team"/>
<meta name="publisher" content="<?php echo $Publisher; ?>"/>
<meta name="reply-to" content="<?php echo $Destinataire; ?>"/>
<meta name="viewport" content="width=device-width" />

<link rel="shortcut icon" href="<?php echo $Home; ?>/lib/img/icone.ico" >
<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/lib/css/misenpapc.css" />

<script type="text/javascript" src="<?php echo $Home; ?>/lib/js/analys.js"></script>
</head>

<body>
<center>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/header.inc.php"); ?>

<section>
<div id="Center">
    
<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; } 

if ((isset($Client))&&(!empty($Client))&&(isset($Hash))&&(!empty($Hash))) {

    $VerifClient=$cnx->prepare("SELECT * FROM ".$Prefix."secu_mdp WHERE hash=:client");
    $VerifClient->bindParam(':client', $Client, PDO::PARAM_STR);
    $VerifClient->execute();
    $NbRowsClient=$VerifClient->rowCount();

    $VerifHash=$cnx->prepare("SELECT * FROM ".$Prefix."secu_mdp WHERE code=:hash");
    $VerifHash->bindParam(':hash', $Hash, PDO::PARAM_STR);
    $VerifHash->execute();
    $NbRowsHash=$VerifHash->rowCount();
        
    if (strlen($Client)!=32) {
        echo "Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !";   
    }

    elseif (strlen($Hash)!=32) {
        echo "Le lien de vérification a été modifié, vérifier qu'il correspond a celui reçu dans l'e-mail de confirmation !";   
    }

    elseif ($NbRowsClient!=1) {
        echo "Aucune procédure de changement de mot de passe n'a été demandé !<br />";
    }

    elseif ($NbRowsHash!=1) {
        echo "Aucune procédure de changement de mot de passe n'a été demandé !<br />";
    }

    else { ?>
        <form id="form_mdp" action="" method="POST">

        <label class="col_1">Nouveau mot de passe :</label>
        <input type="password" name="mdp" required="required"/>
        <BR />
        <label class="col_1">Confirmer le mot de passe :</label>
        <input type="password" name="mdp2" required="required"/>
        <BR />

        <span class="col_1"></span>
        <input type="submit" name="Valider" value="Valider"/>
        </form><?php 
    }
}
else {
    echo "Erreur !";
}
?>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>