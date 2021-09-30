<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");   
require_once($_SERVER['DOCUMENT_ROOT']."/lib/FPDF/fpdf.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/FPDI-1.6.1/fpdi.php");

if ($Cnx_CompteClient==false) { 
    header("location:".$Home."/DashBoard/");
}  
elseif ($CompteExpirer==true) {
    $Erreur="Votre compte n'est plus actif, merci de prolonger votre abonnement pour continuer à bénéficier du service";
}     

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];   

define('EURO',chr(128));
  
$RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");
$RecupParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupParam->execute();
$Param=$RecupParam->fetch(PDO::FETCH_OBJ);

$RecupInfoClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE hash=:hash AND client=:client");
$RecupInfoClient->bindParam(':hash', $_SESSION['Hashclient'], PDO::PARAM_STR);
$RecupInfoClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoClient->execute();
$InfoClient=$RecupInfoClient->fetch(PDO::FETCH_OBJ);

$RecupInfoSociete=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:client");
$RecupInfoSociete->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoSociete->execute();
$InfoSociete=$RecupInfoSociete->fetch(PDO::FETCH_OBJ);

$RecupInfoBon=$cnx->prepare("SELECT * FROM ".$Prefix."Bon_Fidelite WHERE hash_client=:hash_client AND hash_transac=:hash_transac AND client=:client");
$RecupInfoBon->bindParam(':hash_client', $_SESSION['Hashclient'], PDO::PARAM_STR);
$RecupInfoBon->bindParam(':hash_transac', $_SESSION['HashTransac'], PDO::PARAM_STR);
$RecupInfoBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoBon->execute();
$InfoFidelite=$RecupInfoBon->fetch(PDO::FETCH_OBJ);

$Typo='Helvetica';
$TailleText="11";
$Height="6";

if ($Param->model=="1") {
    $ColorText1="0";
    $ColorText21="217";
    $ColorText22="31";
    $ColorText23="118";
    $ColorText3="255";
}
if ($Param->model=="2") {
    $ColorText1="0";
    $ColorText21="207";
    $ColorText22="122";
    $ColorText23="0";
    $ColorText3="255";
}
if ($Param->model=="3") {
    $ColorText1="0";
    $ColorText21="0";
    $ColorText22="0";
    $ColorText23="0";
    $ColorText3="255";
}
if ($Param->model=="4") {
    $ColorText1="0";
    $ColorText21="118";
    $ColorText22="91";
    $ColorText23="134";
    $ColorText3="255";
}
if ($Param->model=="5") {
    $ColorText1="0";
    $ColorText21="217";
    $ColorText22="31";
    $ColorText23="118";
    $ColorText3="255";
}
if ($Param->model=="6") {
    $ColorText1="0";
    $ColorText21="221";
    $ColorText22="126";
    $ColorText23="49";
    $ColorText3="255";
}
if ($Param->model=="7") {
    $ColorText1="0";
    $ColorText21="74";
    $ColorText22="183";
    $ColorText23="206";
    $ColorText3="255";
}
if ($Param->model=="8") {
    $ColorText1="0";
    $ColorText21="196";
    $ColorText22="101";
    $ColorText23="144";
    $ColorText3="255";
}
if ($Param->model=="9") {
    $ColorText1="0";
    $ColorText21="131";
    $ColorText22="31";
    $ColorText23="217";
    $ColorText3="255";
}
if ($Param->model=="10") {
    $ColorText1="0";
    $ColorText21="217";
    $ColorText22="31";
    $ColorText23="118";
    $ColorText3="255";
}
if ($Param->model=="11") {
    $ColorText1="0";
    $ColorText21="63";
    $ColorText22="180";
    $ColorText23="66";
    $ColorText3="255";
}
if ($Param->model=="12") {
    $ColorText1="0";
    $ColorText21="255";
    $ColorText22="179";
    $ColorText23="59";
    $ColorText3="255";
}

//Edition du bon d'achat PDF

//------------------------------------------------------

require($_SERVER['DOCUMENT_ROOT']."/DashBoard/Fidelite/Historique/Apercu/Model_1.php");

$repInt=$_SERVER['DOCUMENT_ROOT']."/lib/Model/Model_".$Param->model.".pdf";

//------------------------------------------------------

// Instanciation de la classe derivee
$pdf = new PDF();
$pageCount = $pdf->setSourceFile($repInt);
$pdf->SetAutoPageBreak(5);

$Width=$Height;

$Client=$InfoClient->civilite.' '.stripslashes($InfoClient->nom).' '.stripslashes($InfoClient->prenom)." \n".stripslashes($InfoClient->adresse)." \n".$InfoClient->cp.', '.stripslashes($InfoClient->ville);
$Societe=stripslashes($InfoSociete->nom)." \n".stripslashes($InfoSociete->adresse)." \n".$InfoSociete->cp.', '.stripslashes($InfoSociete->ville);

$Date=$InfoFidelite->consomed-86400;
$Date=date("d-m-y", $Date);
$Validite=$Date;
$Gencode=$InfoFidelite->gencode;
$Cadeau=$InfoFidelite->cadeau;
$SocieteNom=$InfoSociete->nom;
$GencodeClient=$InfoClient->carte;

for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    // import a page
    $templateId = $pdf->importPage($pageNo);
    // get the size of the imported page
    $size = $pdf->getTemplateSize($templateId);

    // create a page (landscape or portrait depending on the imported page size)
    if ($size['w'] > $size['h']) {
        $pdf->AddPage('L', array($size['w'], $size['h']));
    }
    else {
        $pdf->AddPage('P', array($size['w'], $size['h']));
    }
    $pdf->useTemplate($templateId);
}

$pdf->SetTextColor($ColorText1);
$pdf->SetFont( $Typo, "", $TailleText);

$pdf->Image($Param->logo,10,10);

$pdf->SetXY( 10, 50);
$pdf->MultiCell(95, 4, $Client, "0", "L", false);

$pdf->SetXY( 120, 70);
$pdf->MultiCell(95, 4, $Societe, "0", "L", false);

$pdf->SetXY( 10, 100);
$pdf->SetTextColor(0, 204, 0);
$pdf->SetFont( $Typo, "B", 27);
$pdf->Cell(200, $Width, "Félicitations !", 0, 0, "L", false);
$pdf->Ln(20);
$pdf->SetTextColor($ColorText1);
$pdf->SetFont( $Typo, "", $TailleText);
$pdf->MultiCell(200, $Width, "Nous vous remercions de votre fidélité et nous sommes heureux de vous faire parvenir aujourd'hui", 0, "L", false);
$pdf->Ln();
$pdf->SetTextColor(0, 204, 0);
$pdf->MultiCell(200, $Width, "Votre chèque de fidélité de ".$Cadeau.EURO." valable jusqu'au ".$Validite, 0, "L", false);
$pdf->Ln();
$pdf->SetTextColor($ColorText1);
$pdf->MultiCell(200, $Width, "Pensez à présenter votre carte de fidélité à chacune de vos visites, pour cumuler des points sur vos achats et recevoir ainsi un nouveau chéque de fidélité !", 0, "L", false);
$pdf->Ln(10);
$pdf->SetTextColor(0, 204, 0);
$pdf->SetFont( $Typo, "B", 14);
$pdf->Cell(200, $Width, "Venez découvrir nos nouveautés", 0, 0, "C", false);
$pdf->Ln(20);
$pdf->SetTextColor($ColorText1);
$pdf->SetFont( $Typo, "", $TailleText);
$pdf->Cell(190, $Width, "Nous espèrons vous revoir très bientôt", 0, 0, "R", false);
$pdf->Ln();
$pdf->Cell(190, $Width, "Cordialement ".$SocieteNom, 0, 0, "R", false);
        
$pdf->SetXY(30, 230);
$pdf->SetTextColor($ColorText1);
$pdf->SetFont( $Typo, "B", $TailleText);
$pdf->MultiCell(50, $Width, "Bénéficiaire", 0, "L", false);
$pdf->SetFont( $Typo, "", $TailleText);        
$pdf->SetX(30);
$pdf->MultiCell(50, $Width, $Client, 0, "L", false);

$pdf->Ln(15);
$pdf->EAN13(30, 260, $InfoFidelite->gencode);
$pdf->Ln(15);
        
$pdf->SetX(30);
$pdf->SetFont( $Typo, "B", $TailleText);
$pdf->Cell(50, $Width, "Numéro de carte", 0, 0, "L", false);
$pdf->Ln();
$pdf->SetFont( $Typo, "", $TailleText);        
$pdf->SetX(30);
$pdf->Cell(50, $Width, $GencodeClient, 0, 0, "L", false);

if($Cadeau>99) {
    $pdf->SetXY(128, 262);  
}
elseif(($Cadeau>9)&&($Cadeau<=99)) {
    $pdf->SetXY(132, 262);  
}
else {
    $pdf->SetXY(135, 262);
}
$pdf->SetTextColor($ColorText3);
$pdf->SetFont( $Typo, "B", "47");
$pdf->Cell(20, $Width, $Cadeau.EURO, 0, 0, "L", false);

$pdf->Output("", "I");
?>