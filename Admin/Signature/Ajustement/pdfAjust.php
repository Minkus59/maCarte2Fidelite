<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

header('Content-Type: text/html; charset=ISO-8859-15');

require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/FPDF/fpdf.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/FPDI-1.6.1/fpdi.php");

$repInt=$_SERVER['DOCUMENT_ROOT']."/lib/Document/Original/";
$repExt=$Home."/lib/Document/Original/";
$repIntSigner=$_SERVER['DOCUMENT_ROOT']."/lib/Document/Signer/";
$repExtSigner=$Home."/lib/Document/Signer/";
$Now=time();

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

echo "<iframe src='".$repExtSigner.$fichier."' width='480' height='660'></iframe>";
?>
