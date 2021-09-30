<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php"); 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");      

require_once($_SERVER['DOCUMENT_ROOT']."/lib/PayPlug/Payplug.php");
Payplug::setConfigFromFile($_SERVER['DOCUMENT_ROOT']."/impinfbdd/parameters.json");

$Code = md5(uniqid(rand(), true));
$Commande=substr($Code, 0, 8);

$SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:client");
$SelectParam->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$SelectParam->execute();
$Param=$SelectParam->fetch(PDO::FETCH_OBJ);

$paymentUrl = PaymentUrl::generateUrl(array(
                                      'amount' => 58800,
                                      'currency' => 'EUR',
                                      'ipnUrl' => $Home.'/lib/PayPlug/IpN.php',
                                      'returnUrl' => $Home.'/Paiement/Validation.php',
                                      'cancelUrl' => $Home.'/Paiement/Annulation.php',
                                      'email' => $Param->email,
                                      'firstName' => $Param->nom,
                                      'lastName' => $Param->prenom,
									  'customer' => $Param->hash,
									  'order' => $Commande,
                                      'customData' => '3'
                                      ));
header("Location: $paymentUrl");
exit();
?>