<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/FPDF/fpdf.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/FPDI-1.6.1/fpdi.php");

$repInt=$_SERVER['DOCUMENT_ROOT']."/lib/Document/Original/";
$repExt=$Home."/lib/Document/Original/";
$repIntSigner=$_SERVER['DOCUMENT_ROOT']."/lib/Document/Signer/";
$repExtSigner=$Home."/lib/Document/Signer/";
$Now=time();

if (isset($_POST['Signer'])) {  
    
    if (!file_exists($repInt)) {
        $Preparation1=$cnx->query("CREATE TABLE IF NOT EXISTS ".$Prefix."neuro_Signature (
        id int(32) unsigned NOT NULL AUTO_INCREMENT,
        fichier longtext NOT NULL,
        created int(15) NOT NULL,
        PRIMARY KEY (id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        
        mkdir($_SERVER['DOCUMENT_ROOT']."/lib/Document/", 0777);
        mkdir($_SERVER['DOCUMENT_ROOT']."/lib/Document/Original/", 0777);
    }
    if (!file_exists($repIntSigner)) {
        mkdir($_SERVER['DOCUMENT_ROOT']."/lib/Document/", 0777);
        mkdir($_SERVER['DOCUMENT_ROOT']."/lib/Document/Signer/", 0777);
    }
    
    $chemin=$_FILES['fichier']['name'];
    $fichier=basename($chemin);
    
    $VerifExist=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Signature WHERE fichier=:fichier");
    $VerifExist->bindParam(':fichier', $fichier, PDO::PARAM_STR);
    $VerifExist->execute();
    $NbRows=$VerifExist->rowCount();
            
    if ($NbRows==1) {
        $Erreur="Ce fichier existe déjà  !<br />";
        header('Location:'.$Home.'/Admin/Signature/?erreur='.$Erreur); 
    }
    else {
        $ext = array('.pdf', '.PDF');
        $ext2 = array('.pdf', '.PDF');
        $Format = $_POST['format'];
        $Horizontal=$_POST['horizontal'];
        $Vertical=$_POST['vertical'];
        $Page=$_POST['page'];
        $Type=$_POST['type'];

        if ($_FILES['fichier']['name']!="") {
            $taille_origin=filesize($_FILES['fichier']['tmp_name']);
            $ext_origin=strchr($chemin, '.');

            $TailleImage=@getimagesize($_FILES['fichier']['tmp_name']);
            $taille_max="20000000";

            $Code=md5(uniqid(rand(), true));
            $Hash=substr($Code, 0, 8);

            if (!in_array($ext_origin, $ext)) {
                $Erreur="Extention de fichier non pris en charge !";
            }
            else {
                //PDF
                if (in_array($ext_origin, $ext2)) {
                    if($taille_origin>$taille_max) {
                        $Erreur = "fichier trop volumineux, il ne doit dépassé les 20Mo";
                    }
                    if (!isset($Erreur)) {
                        $Upload = move_uploaded_file($_FILES['fichier']['tmp_name'], $repInt.$fichier);
                        
                        if ($Upload==false) {
                                $Erreur="Erreur de téléchargement, veuillez réassayer ultèrieurement";
                        }
                        else {
                            
                            $pdf_file = $repInt.$fichier;
                            $save_to = $repIntSigner."test.jpeg";
                            
                            exec('convert "'.$pdf_file.'"   "'.$save_to.'"', $output, $return_var);
                            
                            if($return_var == 0) {
                                print "Conversion OK";
                            }
                            else print "Conversion failed.<br />".$output;
/*
                            $pdf = new FPDI();                        
                            $pageCount = $pdf->setSourceFile($repInt.$fichier);
                            // iterate through all pages
                            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                                // import a page
                                $templateId = $pdf->importPage($pageNo);
                                // get the size of the imported page
                                $size = $pdf->getTemplateSize($templateId);

                                // create a page (landscape or portrait depending on the imported page size)
                                if ($size['w'] > $size['h']) {
                                    $pdf->AddPage('L', array($size['w'], $size['h']));
                                } else {
                                    $pdf->AddPage('P', array($size['w'], $size['h']));
                                }

                                // use the imported page
                                $pdf->useTemplate($templateId);

                                if ($Page=="All") {
                                    if ($Type=="Tampon") {                                    
                                        $pdf->SetXY($Horizontal, $Vertical);
                                        $pdf->Image($_SERVER['DOCUMENT_ROOT']."/Admin/Signature/Documents/tampon-numerique.jpg",$Horizontal, $Vertical,'JPG');
                                    }
                                    if ($Type=="Signature") {                                    
                                        $pdf->SetXY($Horizontal, $Vertical);
                                        $pdf->Image($_SERVER['DOCUMENT_ROOT']."/Admin/Signature/Documents/signature-numerique.jpg",$Horizontal, $Vertical,'JPG');
                                    }
                                }
                            }
                            
                            if ($Page=="Last") {
                                    if ($Type=="Tampon") {                                    
                                        $pdf->SetXY($Horizontal, $Vertical);
                                        $pdf->Image($_SERVER['DOCUMENT_ROOT']."/Admin/Signature/Documents/tampon-numerique.jpg",$Horizontal, $Vertical,'JPG');
                                    }
                                    if ($Type=="Signature") {                                    
                                        $pdf->SetXY($Horizontal, $Vertical);
                                        $pdf->Image($_SERVER['DOCUMENT_ROOT']."/Admin/Signature/Documents/signature-numerique.jpg",$Horizontal, $Vertical,'JPG');
                                    }
                            }

                            $pdf->Output($repIntSigner.$fichier, "F");
                            //$pdf->Output($fichier, "D");
                            
                            $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Signature (fichier, created) VALUES (:fichier, :created)");
                            $Insert->BindParam(":fichier", $fichier, PDO::PARAM_STR);
                            $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                            $Insert->execute();

                            if ($Insert==false) {
                                $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
                            }
                            else  {
                                $Valid="Document signer avec succès";
                            }
                      */  }
                    } 
                }
            }
        }
    }
}

if (isset($_POST['Ajuster'])) {
    
    $ext = array('.pdf', '.PDF');
    $ext2 = array('.pdf', '.PDF');
    $Format = $_POST['format'];
    $Horizontal=$_POST['horizontal'];
    $Vertical=$_POST['vertical'];
    $Page=$_POST['page'];
    $fichier=$_POST['fichier'];
    $Type=$_POST['type'];

    $pdf = new FPDI();                        
    $pageCount = $pdf->setSourceFile($repInt.$fichier);
    // iterate through all pages
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        // import a page
        $templateId = $pdf->importPage($pageNo);
        // get the size of the imported page
        $size = $pdf->getTemplateSize($templateId);

        // create a page (landscape or portrait depending on the imported page size)
        if ($size['w'] > $size['h']) {
            $pdf->AddPage('L', array($size['w'], $size['h']));
        } else {
            $pdf->AddPage('P', array($size['w'], $size['h']));
        }

        // use the imported page
        $pdf->useTemplate($templateId);

        if ($Page=="All") {
            if ($Type=="Tampon") {                                    
                $pdf->SetXY($Horizontal, $Vertical);
                $pdf->Image($_SERVER['DOCUMENT_ROOT']."/Admin/Signature/Documents/tampon-numerique.jpg",$Horizontal, $Vertical,'JPG');
            }
            if ($Type=="Signature") {                                    
                $pdf->SetXY($Horizontal, $Vertical);
                $pdf->Image($_SERVER['DOCUMENT_ROOT']."/Admin/Signature/Documents/signature-numerique.jpg",$Horizontal, $Vertical,'JPG');
            }
        }
    }

    if ($Page=="Last") {
            if ($Type=="Tampon") {                                    
                $pdf->SetXY($Horizontal, $Vertical);
                $pdf->Image($_SERVER['DOCUMENT_ROOT']."/Admin/Signature/Documents/tampon-numerique.jpg",$Horizontal, $Vertical,'JPG');
            }
            if ($Type=="Signature") {                                    
                $pdf->SetXY($Horizontal, $Vertical);
                $pdf->Image($_SERVER['DOCUMENT_ROOT']."/Admin/Signature/Documents/signature-numerique.jpg",$Horizontal, $Vertical,'JPG');
            }
    }

    $pdf->Output($repIntSigner.$fichier, "F");
}
?>


<!-- ************************************
*** Script réalisé par NeuroSoft Team ***
********* www.neuro-soft.fr *************
**************************************-->

<!doctype html>
<html>
<head>
<meta charset="ISO-8859-15">

<title>NeuroSoft Team - Accès PRO</title>

<meta http-equiv="pragma" content="no-cache" />
<meta name="robots" content="noindex, nofollow" />

<meta name="author" content="NeuroSoft Team" />
<meta name="publisher" content="Helinckx Michael" />
<meta name="reply-to" content="contact@neuro-soft.fr" />

<meta name="viewport" content="width=device-width" />                                                            

<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico" />

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpapc.css" />

</head>

<body>
<header>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>
</header>

<section>
    
<nav>
<div id="MenuGauche">
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>
</div>
</nav>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".$Valid."</font><BR />"; } ?>

<H1>Signer un document</H1></p>

<div id="Affichage">
<iframe src="<?php echo $repExtSigner.$fichier; ?>" width="480" height="660"></iframe>
</div>

<form name="form_pdfAjust" action="" method="POST">

<input name="fichier" type="hidden" value="<?php echo $fichier; ?>"/>
<input name="page" type="hidden" value="<?php echo $Page; ?>"/>
<input name="type" type="hidden" value="<?php echo $Type; ?>"/>

<span class="col_1" >Horizontal : </span>
<input type="range" name="horizontal" min="0" max="130" value="<?php echo $Horizontal; ?>" />
</p>
<span class="col_1" >Vertical :  </span>
<input type="range" orient="vertical" name="vertical" min="0" max="255" value="<?php echo $Vertical; ?>" />
</p>
<p><input type="submit" name="Ajuster" value="Ajuster"/>
</form></p>

</article>
</section>
</div>
</CENTER>
</body>

</html>