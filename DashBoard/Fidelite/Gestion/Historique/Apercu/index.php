<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");   
require($_SERVER['DOCUMENT_ROOT']."/lib/FPDF/fpdf.php");  

if ($Cnx_CompteClient==false) { 
    header("location:".$Home);
}  
elseif ($CompteExpirer==true) {
    $Erreur="Votre compte n'est plus actif, merci de prolonger votre abonnement pour continuer à bénéficier du service";
}      

$Id=$_GET['id'];
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];   
  
$RecupInfoFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE id=:id AND client=:client");
$RecupInfoFidelite->bindParam(':id', $Id, PDO::PARAM_STR);
$RecupInfoFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoFidelite->execute();
$InfoBon=$RecupInfoFidelite->fetch(PDO::FETCH_OBJ);

$RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");
$RecupParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupParam->execute();
$Param=$RecupParam->fetch(PDO::FETCH_OBJ);

$RecupInfoClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE hash=:hash AND client=:client");
$RecupInfoClient->bindParam(':hash', $InfoBon->hash_client, PDO::PARAM_STR);
$RecupInfoClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoClient->execute();
$InfoClient=$RecupInfoClient->fetch(PDO::FETCH_OBJ);

$RecupInfoSociete=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:client");
$RecupInfoSociete->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoSociete->execute();
$InfoSociete=$RecupInfoSociete->fetch(PDO::FETCH_OBJ);

$RecupInfoBon=$cnx->prepare("SELECT * FROM ".$Prefix."Bon_Fidelite WHERE hash_client=:hash_client AND hash_transac=:hash_transac AND client=:client");
$RecupInfoBon->bindParam(':hash_client', $InfoBon->hash_client, PDO::PARAM_STR);
$RecupInfoBon->bindParam(':hash_transac', $InfoBon->hash, PDO::PARAM_STR);
$RecupInfoBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoBon->execute();
$InfoFidelite=$RecupInfoBon->fetch(PDO::FETCH_OBJ);

$Typo='Helvetica';
$ColorText1="0";
$ColorText2="0, 204, 0";
$TailleText="11";
$Height="6";

if ($Param->model=="1") {
$ColorFont1="100";
$ColorFont2="100";
$ColorFont3="100";
}

if ($Param->model=="2") {
$ColorFont1="23";
$ColorFont2="57";
$ColorFont3="85";
}

if ($Param->model=="3") {
$ColorFont1="108";
$ColorFont2="22";
$ColorFont3="22";
}

//Edition du bon d'achat PDF

//------------------------------------------------------

require($_SERVER['DOCUMENT_ROOT']."/DashBoard/Fidelite/Gestion/Historique/Apercu/Model_1.php");

//------------------------------------------------------

// Instanciation de la classe dérivée
$pdf = new PDF();
$pdf->AliasNbPages();

$pdf->setTailleText($TailleText);
$pdf->setWidth($Height);
$pdf->setTypo($Typo);
$pdf->setColorText($ColorText);
$pdf->setColorText2($ColorText2);
$pdf->setColorFont1($ColorFont1);
$pdf->setColorFont2($ColorFont2);
$pdf->setColorFont3($ColorFont3);

$pdf->setClient($InfoClient->civilite.' '.stripslashes($InfoClient->nom).' '.stripslashes($InfoClient->prenom)." \n".stripslashes($InfoClient->adresse)." \n".$InfoClient->cp.', '.stripslashes($InfoClient->ville));
$pdf->setSociete(stripslashes($InfoSociete->nom)." \n".stripslashes($InfoSociete->adresse)." \n".$InfoSociete->cp.', '.stripslashes($InfoSociete->ville));

$Date=$InfoFidelite->consomed-86400;
$Date=date("d-m-y", $Date);
$pdf->setValidite($Date);
$pdf->setGencode($InfoFidelite->gencode);
$pdf->setCadeau($InfoFidelite->cadeau);
$pdf->setSocieteNom($InfoSociete->nom);
$pdf->setGencodeClient($InfoClient->carte);

$pdf->AddPage();
$pdf->EAN13(10, 235, $InfoFidelite->gencode);
$pdf->Output("", "I");
?>