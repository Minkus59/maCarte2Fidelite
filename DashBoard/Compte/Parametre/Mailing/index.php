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

$ParamFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");    
$ParamFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$ParamFidelite->execute(); 
$Param=$ParamFidelite->fetch(PDO::FETCH_OBJ);

$OptionFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Option WHERE client=:client");    
$OptionFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$OptionFidelite->execute(); 
$Option=$OptionFidelite->fetch(PDO::FETCH_OBJ);


if (isset($_POST['Enregistrer4'])) {
    $Mailing=$_POST['mailling']; 

    if (empty($Mailing)) {
        $Erreur="Un message doit etre saisie !";
    }
    else {
        $UpdateParam=$cnx->prepare("UPDATE ".$Prefix."Fidelite_Param SET mailling=:mailling WHERE client=:client");
        $UpdateParam->bindParam(':mailling', $Mailing, PDO::PARAM_STR);
        $UpdateParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $UpdateParam->execute();

        $Valid="Paramètre modifié avec succès";
        header("location:".$Home."/DashBoard/Compte/Parametre/Mailing/?valid=".urlencode($Valid));
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
<script type="text/javascript" src="<?php echo $Home; ?>/Admin/lib/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
  tinymce.init({
    relative_urls : false,
    remove_script_host : false,
    min_height : '350',
    selector: '#message',
    language : 'fr_FR',
    plugins: [
      'advlist autolink link image lists charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality paste textcolor'
    ],
    content_css: 'css/content.css',
    toolbar: 'insertfile undo redo | styleselect | fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
  });
</script>

<script type="text/javascript">
  tinymce.init({
    relative_urls : false,
    remove_script_host : false,
    min_height : '350',
    selector: '#message2',
    language : 'fr_FR',
    plugins: [
      'advlist autolink link image lists charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'save table contextmenu directionality paste textcolor'
    ],
    content_css: 'css/content.css',
    toolbar: 'insertfile undo redo | styleselect | fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
  });
</script>
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
<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/navParam.inc.php"); ?>
</div>
<div id="ColoneRight">

<div id="Form_Middle5">
<H1 class="TitreOrange">Mailing Type</H1>

<form name="SelectMode" action="" method="POST">
Message envoyé avec le Mailing <font color='#FF0000'>*</font> 

<textarea id="message2" name="mailling" placeholder="Message*" require="required"><?php echo $Param->mailling; ?></textarea>
<BR />
<span class="col_1"></span>
<input type="submit" class="ButtonOrange" name="Enregistrer4" value="Enregistrer"/>
</form>
</div>

</div>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>