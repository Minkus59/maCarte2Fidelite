<?php
session_start();
if (isset($_SESSION['NeuroAdmin'])) {
    $SessionAdmin=$_SESSION['NeuroAdmin'];

    $VerifSessionAdmin=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Admin WHERE email=:email");
    $VerifSessionAdmin->bindParam(':email', $SessionAdmin, PDO::PARAM_STR);
    $VerifSessionAdmin->execute();

    $NumRowSessionAdmin=$VerifSessionAdmin->rowCount();

    if ((isset($SessionAdmin))&&($NumRowSessionAdmin==1)) {
        $Cnx_Admin=true;
    }
    else {
        $Cnx_Admin=false;
    }
}    
else {
        $Cnx_Admin=false;
}

if (isset($_SESSION['NeuroClient'])) {
    $SessionClient=$_SESSION['NeuroClient'];

    $VerifSessionClient=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_compte_Client WHERE email=:email");
    $VerifSessionClient->bindParam(':email', $SessionClient, PDO::PARAM_STR);
    $VerifSessionClient->execute();

    $NumRowSessionClient=$VerifSessionClient->rowCount();

    if ((isset($SessionClient))&&($NumRowSessionClient==1)) {
        $Cnx_Client=true;
    }
    else {
        $Cnx_Client=false;
    }
}   
else {
        $Cnx_Client=false;
}

if (isset($_SESSION['NeuroCompteClient'])) {
    $SessionCompteClient=$_SESSION['NeuroCompteClient'];

    $VerifSessionCompteClient=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:hash");
    $VerifSessionCompteClient->bindParam(':hash', $SessionCompteClient, PDO::PARAM_STR);
    $VerifSessionCompteClient->execute();

    $NumRowSessionCompteClient=$VerifSessionCompteClient->rowCount();

    if ((isset($SessionCompteClient))&&($NumRowSessionCompteClient==1)) {
        if ($_SESSION['Actif']==0) {
            $CompteExpirer=true;
        }
        $Cnx_CompteClient=true;
    }
    else {
        $Cnx_CompteClient=false;
    }
}   
else {
        $Cnx_CompteClient=false;
}

?>