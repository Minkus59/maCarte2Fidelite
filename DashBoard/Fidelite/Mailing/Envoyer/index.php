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

$RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");
$RecupParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupParam->execute();
$Param=$RecupParam->fetch(PDO::FETCH_OBJ); 

$RecupInfoSociete=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:client");
$RecupInfoSociete->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoSociete->execute();
$InfoSociete=$RecupInfoSociete->fetch(PDO::FETCH_OBJ);     

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Selection=$_POST['selection'];
$Compteur=count($Selection);
$Fin=$Compteur-1;

for($u=0;$u<$Compteur;$u++) {
    if($Compteur>1) {
        if($u==0) {
            $Email.=$Selection[$u];  
        }
        else {
            $Email.=", ".$Selection[$u]; 
        }
    }
    else {
        $Email.=$Selection[$u];
    }
}

$Now=time();

if ((isset($_POST['Envoyer']))&&($_POST['Envoyer']=="Envoyer")) { 
    
    $RepInt=$_SERVER['DOCUMENT_ROOT']."/lib/Mail/Document/";
    $RepExt=$Home."/lib/Mail/Document/";
    
    if (!file_exists($RepInt)) {
        mkdir($_SERVER['DOCUMENT_ROOT']."/lib/Mail/", 0777);
        mkdir($_SERVER['DOCUMENT_ROOT']."/lib/Mail/Document/", 0777);
    }
                         
    $Retour=FiltreEmail('email');
    if ((isset($_POST['destinataire']))&&(!empty($_POST['destinataire']))) {
        if ((isset($_POST['objet']))&&(!empty($_POST['objet']))) {
            if ((isset($_POST['message']))&&(!empty($_POST['message']))) {               
                if (preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['retour'])) { 
                    
                    $Destinataire2=$_POST['destinataire'];
                    $Objet=$_POST['objet'];
                    $Message=$_POST['message'];
                    $Retour=$_POST['retour'];
                    
                    $boundary = md5(uniqid(mt_rand()));
                    
                    $Entete = "From: ".$InfoSociete->societe." <".$Retour.">\n";
                    $Entete .= "MIME-Version: 1.0\n";
                    $Entete .= "Content-Type:multipart/mixed; boundary=\"$boundary\"\n";
                    $Entete .= "\n";
                    
                    $message="Ce message est au format MIME.\n";
                    
                    $message.="--$boundary\n";
                    $message.="Content-Type: text/html; charset=iso8859-15\n";  
                    $message.="\n";
                    
                    $message.="<html><head>
                                <title>".$Objet."</title>
                                </head>
                                <body>
                                ".$Message."
                                </body>
                                </html>";
                                
                    $message.="\n\n";   
                    $message.="--$boundary\n"; 
                    
                    if (mail($Destinataire2, $Objet, $message, $Entete)===FALSE) {
                        $Erreur = "L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !";
                    }
                    else {
                        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Mail (destinataire, objet, message, retour, type, created, client) VALUES(:destinataire, :objet, :message, :retour, :type, :created, :client)");
                        $Insert->BindParam(":destinataire", $Destinataire2, PDO::PARAM_STR);
                        $Insert->BindParam(":objet", $Objet, PDO::PARAM_STR);
                        $Insert->BindParam(":message", $Message, PDO::PARAM_STR);
                        $Insert->BindParam(":retour", $Retour, PDO::PARAM_STR);
                        $Insert->BindParam(":type", $Type, PDO::PARAM_STR);
                        $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                        $Insert->BindParam(":client", $SessionCompteClient, PDO::PARAM_STR);
                        $Insert->execute();
                        
                        if ($Insert===FALSE) {
                            $Erreur="Erreur de base de donnée, veuillez contacter l'administrateur du site Internet";
                        }
                        else {            
                            $Valid="Votre message a bien été envoyé !";
                            header("location:".$Home."/DashBoard/Fidelite/Mailing/?valid=".urlencode($Valid));
                        }
                    }
                }
                else {
                    $Erreur="L'adresse e-mail de retour n'est pas conforme !</p>";
                }  
            }
            else {
                $Erreur="Veuillez entrer un message !";
            }
        }
        else {
            $Erreur="Veuillez entrer un objet de message !";
        }
    } 
    else {
        $Erreur="Veuillez entrer aux moins un destinataire !";
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
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking',
      'save table contextmenu directionality paste textcolor'
    ],
    content_css: 'css/content.css',
    toolbar: 'insertfile undo redo | fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
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

<div id="Form_Middle3">
<H1>Envoyer un e-mail</H1></p>
<form name="form_mail" action="" method="POST" enctype="multipart/form-data">

<input type="text" placeholder="à€ :" name="destinataire" require="required" value="<?php echo $Email; ?>"/></p>

<input type="text" placeholder="Objet :" name="objet" require="required"/></p>

<input type="text" placeholder="Adresse de retour" name="retour" value="<?php echo $Destinataire; ?>" require="required"/></p>
<!--
<input type="file"  placeholder="pièce jointe 1" name="fichier1"/><BR />
<input type="file"  placeholder="pièce jointe 2" name="fichier2"/><BR />
<input type="file"  placeholder="pièce jointe 3" name="fichier3"/><BR />
<input type="file"  placeholder="pièce jointe 4" name="fichier4"/><BR />
<input type="file"  placeholder="pièce jointe 5" name="fichier5"/></p>
-->
<textarea id="message" name="message" placeholder="Message*" require="required">
<?php echo $Param->mailling ?>
</textarea></p>

<input type="submit" class="ButtonRose" name="Envoyer" value="Envoyer"/>
</form>
</div>
</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>