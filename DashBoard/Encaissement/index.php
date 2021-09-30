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

//ETAPE 1 --------------------------------------------------------------------------------------------------------------------------------------------------------
//recherche
if (isset($_POST['Recherche'])) {
    $Carte=$_SESSION['carte']=trim($_POST['carte']);
    $Nom=$_SESSION['nom']=trim($_POST['nom']);
    $Now=time();
    $Hash=md5(uniqid(rand(), true));
    //par carte
    if ((isset($Carte))&&(strlen($Carte)==13)) {
        $SelectClientExist=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client AND carte=:carte");
        $SelectClientExist->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $SelectClientExist->bindParam(':carte', $Carte, PDO::PARAM_STR);
        $SelectClientExist->execute();   
        $VerifExist=$SelectClientExist->rowCount();
        
        if($VerifExist!=1)  {
            $Erreur="Aucun client n'a été trouvé avec ce numéro";
        }
        else {            
            $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client AND carte=:carte ORDER by id DESC");
            $SelectClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $SelectClient->bindParam(':carte', $Carte, PDO::PARAM_STR);
            $SelectClient->execute();
            $ClientFid=$SelectClient->fetch(PDO::FETCH_OBJ);
            
            $DeleteTransac=$cnx->prepare("DELETE FROM ".$Prefix."Historique WHERE client=:client AND activate='0'");
            $DeleteTransac->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $DeleteTransac->execute(); 
            
            $_SESSION['HashTransac']=$Hash;
            $_SESSION['Hashclient']=$ClientFid->hash;
            
            $InsertTransac=$cnx->prepare("INSERT INTO ".$Prefix."Historique (hash, hash_client, client, created) VALUES(:hash, :hash_client, :client, :created)");
            $InsertTransac->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $InsertTransac->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
            $InsertTransac->bindParam(':hash_client', $ClientFid->hash, PDO::PARAM_STR);
            $InsertTransac->bindParam(':created', $Now, PDO::PARAM_STR);
            $InsertTransac->execute(); 
              
            $_SESSION['etape']="Etape3";
        }
    }
    //par nom
    else {
        if ((isset($Nom))&&(strlen($Nom)>=2)) {
            $SelectClientExist=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client AND nom LIKE :nom");
            $SelectClientExist->execute(array(':nom' => $Nom."%",':client'=> $SessionCompteClient));  
            $VerifExist=$SelectClientExist->rowCount();

            if($VerifExist==0)  {
                $Erreur="Aucun client n'a été trouvé avec ce nom";
            }
            else {                
                $_SESSION['etape']="Etape2";
            }
        }
    }
}    
//ETAPE 2 --------------------------------------------------------------------------------------------------------------------------------------------------------
//selection client
if (isset($_POST['Selection'])) {
    $Carte=$_SESSION['carte']=$_POST['selection'];
    $Now=time();
    $Hash=md5(uniqid(rand(), true));
    
    $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client AND carte=:carte ORDER by id DESC");
    $SelectClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $SelectClient->bindParam(':carte', $Carte, PDO::PARAM_STR);
    $SelectClient->execute();
    $ClientFid=$SelectClient->fetch(PDO::FETCH_OBJ);
    
    $DeleteTransac=$cnx->prepare("DELETE FROM ".$Prefix."Historique WHERE client=:client AND activate='0'");
    $DeleteTransac->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $DeleteTransac->execute(); 
    
    $_SESSION['HashTransac']=$Hash;
    $_SESSION['Hashclient']=$ClientFid->hash;
    
    $InsertTransac=$cnx->prepare("INSERT INTO ".$Prefix."Historique (hash, hash_client, client, created) VALUES(:hash, :hash_client, :client, :created)");
    $InsertTransac->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $InsertTransac->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
    $InsertTransac->bindParam(':hash_client', $ClientFid->hash, PDO::PARAM_STR);
    $InsertTransac->bindParam(':created', $Now, PDO::PARAM_STR);
    $InsertTransac->execute(); 
    
    $_SESSION['etape']="Etape3";
}

//reset recherche
if (isset($_POST['reset2'])) {
    unset($_SESSION['etape']);
}

//element etape2
if ((isset($_SESSION['etape']))&&($_SESSION['etape']=="Etape2")) {
    $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client AND nom LIKE :nom");
    $SelectClient->execute(array(':nom' => $_SESSION['nom']."%",':client'=> $SessionCompteClient));  
}

//ETAPE 3 --------------------------------------------------------------------------------------------------------------------------------------------------------

//element etape3
if ((isset($_SESSION['etape']))&&($_SESSION['etape']=="Etape3")) {
    
    //Option
    $OptionFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Option WHERE client=:client");    
    $OptionFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $OptionFidelite->execute(); 
    $Option=$OptionFidelite->fetch(PDO::FETCH_OBJ);
    //Panier
    $SelectPanierExist=$cnx->prepare("SELECT * FROM ".$Prefix."Historique_produit WHERE client=:client AND hash=:hash");
    $SelectPanierExist->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $SelectPanierExist->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
    $SelectPanierExist->execute(); 
    $CompteurPanier=$SelectPanierExist->rowCount();
    
    //Produit
    $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE client=:client");
    $SelectArticleExist->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $SelectArticleExist->execute(); 

    //categorie
    $SelectCategorieExist=$cnx->prepare("SELECT * FROM ".$Prefix."categorie WHERE client=:client");
    $SelectCategorieExist->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $SelectCategorieExist->execute(); 
    
    //Client    
    $SelectClient=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Client WHERE client=:client AND carte=:carte ORDER by id DESC");
    $SelectClient->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $SelectClient->bindParam(':carte', $_SESSION['carte'], PDO::PARAM_STR);
    $SelectClient->execute();
    $ClientFid=$SelectClient->fetch(PDO::FETCH_OBJ);
    
    $ParamMode=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client ORDER by id DESC");    
    $ParamMode->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $ParamMode->execute(); 
    $Model=$ParamMode->fetch(PDO::FETCH_OBJ);
    
    if (isset($_POST['mode'])) {
        $_SESSION['mode']=$_POST['mode'];
    }
    elseif(isset($_SESSION['mode'])) {
        $_SESSION['mode']=$_SESSION['mode'];
    }
    elseif(!isset($_SESSION['mode'])) {
        $_SESSION['mode']=$Model->mode;
    }
    
    //Generer un code barre EAN13
    $RecupInfoClient=$cnx->prepare("SELECT * FROM ".$Prefix."compte WHERE hash=:hash ORDER by id DESC");
    $RecupInfoClient->bindParam(':hash', $SessionCompteClient, PDO::PARAM_STR);
    $RecupInfoClient->execute();
    $InfoClient=$RecupInfoClient->fetch(PDO::FETCH_OBJ);

    $CountNbProduit=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE client=:client");
    $CountNbProduit->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $CountNbProduit->execute();
    $NbProduit=$CountNbProduit->rowCount();

    $CodePays="046";
    $CodeEntreprise=$InfoClient->id;
    $CodeEntreprise=trim(money_format('%=0(#4.0n', $CodeEntreprise));
    $CodeProduit=$NbProduit + 1;
    $CodeProduit=trim(money_format('%=0(#5.0n', $CodeProduit));
    $Cle="";

    $CodeBar12=$CodePays.$CodeEntreprise.$CodeProduit;

    for ($i=0;$i<=11;$i++) {
        if($i%2==1) {
            $N[$i]=$CodeBar12[$i]*3;
        }
        else {
            $N[$i]=$CodeBar12[$i]*1;
        }
        $Total+=$N[$i]; 
    }
    $Reste_1=fmod($Total,10);
    if ($Reste_1!=0) {
        $Cle=10-$Reste_1;
    }
    else {
        $Cle=$Reste_1;
    }

    $CodeBar13=$CodePays.$CodeEntreprise.$CodeProduit.$Cle;
}

//Moteur de recherche
if (isset($_POST['MoteurRecherche'])) {
    if (!empty($_POST['RechercheGencode'])) {
        $RechercheGencode=trim($_POST['RechercheGencode']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE gencode=:gencode AND client=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionCompteClient,':gencode'=> $RechercheGencode)); 
    }
    if (!empty($_POST['RechercheDescription'])) {
        $RechercheDescription=trim($_POST['RechercheDescription']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE description LIKE :description AND client=:client");
        $SelectArticleExist->execute(array(':description' => "%".$RechercheDescription."%",':client'=> $SessionCompteClient)); 
    }
    if (!empty($_POST['RechercheCategorie'])) {
        $RechercheCategorie=trim($_POST['RechercheCategorie']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE categorie=:categorie AND client=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionCompteClient,':categorie' =>$RechercheCategorie)); 
    }
    if (!empty($_POST['RecherchePrix'])) {
        $RecherchePrix=trim($_POST['RecherchePrix']);
        $SelectArticleExist=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE prix=:prix AND client=:client");
        $SelectArticleExist->execute(array(':client'=> $SessionCompteClient,':prix' => $RecherchePrix)); 
    }
}

//ajout produit existant  
if (isset($_POST['Ajouter1'])) {
    
    $Gencode=trim($_POST['gencode']);
    $Description=$_POST['description'];
    $Prix=trim($_POST['prix']);
    $Quantite=trim($_POST['quantite']);
    
    if(!preg_match("#[0-9.]#", $Prix)) {
        $Erreur="Le Prix Unitaire n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    elseif(!preg_match("#[0-9.]#", $Quantite)) {
        $Erreur="La quantité n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    elseif($Quantite=="0") {
        $Erreur="La quantité n'est pas conforme !<br />";
        $Erreur.="Veuillez saisir un chiffre supérieur à 0</p>";
    }
    else {
        $InsertArticle=$cnx->prepare("INSERT INTO ".$Prefix."Historique_produit (gencode, description, prix, quantite, client, hash) VALUES (:gencode, :description, :prix, :quantite, :client, :hash)");
        $InsertArticle->bindParam(':gencode', $Gencode, PDO::PARAM_STR);
        $InsertArticle->bindParam(':description', $Description, PDO::PARAM_STR);
        $InsertArticle->bindParam(':prix', $Prix, PDO::PARAM_STR);
        $InsertArticle->bindParam(':quantite', $Quantite, PDO::PARAM_STR);
        $InsertArticle->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $InsertArticle->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
        $InsertArticle->execute();

        header("location:".$Home."/DashBoard/Encaissement/");
    }
}
//Valider Mode Normal
if (isset($_POST['ValiderTr'])) { 
    $Now=time();
    $Total=$_POST['Total'];
    $Bon=$_POST['BonGencode'];
    $Reedition=0;
    
    if (empty($Total)) {
        $Erreur="Aucune transaction ne peut être valider sans vente !";
    }
    elseif(!preg_match("#[0-9.]#", $Total)) {
        $Erreur="Le total n'est pas conforme !<br />";
        $Erreur.="Ce champ ne doit contenir que des chiffres !</p>";
    }
    else {
        if (!empty($Bon)) {
            $VerifBon=$cnx->prepare("SELECT * FROM ".$Prefix."Bon_Fidelite WHERE client=:client AND gencode=:gencode");    
            $VerifBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);   
            $VerifBon->bindParam(':gencode', $Bon, PDO::PARAM_STR);
            $VerifBon->execute(); 
            $NbBon=$VerifBon->rowCount();
            $InfoBon=$VerifBon->fetch(PDO::FETCH_OBJ);
            
            if ($NbBon==0) {      
                $Erreur="Aucun bon de réduction ne correspond à ce code barre !";
            }
            elseif($NbBon==1) {
                if ($InfoBon->consomed < $Now) {
                    $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Bon_Fidelite SET fidelite='0' WHERE client=:client AND gencode=:gencode");
                    $UpdateBon->bindParam(':gencode', $Bon, PDO::PARAM_STR);
                    $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                    $UpdateBon->execute();
                    
                    $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Historique SET fidelite='0' WHERE client=:client AND hash=:hash");
                    $UpdateBon->bindParam(':hash', $InfoBon->hash_transac, PDO::PARAM_STR);
                    $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                    $UpdateBon->execute();
                    
                    $Erreur="Le bon de fidelité est arrivé à expiration !";
                }
                elseif($InfoBon->consomed >= $Now) {
                    if ($InfoBon->fidelite==1) {
                        $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Bon_Fidelite SET fidelite='2' WHERE client=:client AND gencode=:gencode");
                        $UpdateBon->bindParam(':gencode', $Bon, PDO::PARAM_STR);
                        $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                        $UpdateBon->execute();
                        
                        $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Historique SET fidelite='2' WHERE client=:client AND hash=:hash");
                        $UpdateBon->bindParam(':hash', $InfoBon->hash_transac, PDO::PARAM_STR);
                        $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                        $UpdateBon->execute();
                        
                        //réédition bon de reduct si non cumul total
                        if ($InfoBon->cadeau > $Total) {
                            $Reedition=1;
                            $Cadeau= $InfoBon->cadeau - $Total;
                            $Total = $Cadeau - $Total;
                        }
                        else {
                            $Total = $Total - $InfoBon->cadeau;
                        }
                    }
                    elseif ($InfoBon->fidelite==2) {
                        $Erreur="Le bon d'achat a déja été consommé";
                    }
                    elseif ($InfoBon->fidelite==3) {
                        $Erreur="Le bon d'achat a été annuler";
                    }
                    else {
                        $Erreur="Une erreur est survenue, plusieur bon de réduction on le même gencode !";
                    }
                }
            }
            else {
                $Erreur="Une erreur est survenue, plusieur bon de réduction on le même gencode !";
            }
        }
               
        //Calcule des points cumuler au Jour J
        $ParamFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client ORDER by id DESC");    
        $ParamFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $ParamFidelite->execute(); 
        $Param=$ParamFidelite->fetch(PDO::FETCH_OBJ);
        
        $TotalPoint=$Total * $Param->conversion;
        $Valided=($Now + ($Param->validite * 2635200));
                
        //Calcul des points et Verif si bon de reduction
        $VerifComptePoint=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE client=:client AND hash_client=:hash_client AND activate='1' ORDER BY id DESC");    
        $VerifComptePoint->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $VerifComptePoint->bindParam(':hash_client', $ClientFid->hash, PDO::PARAM_STR);
        $VerifComptePoint->execute(); 
        $ComptePoint=$VerifComptePoint->fetch(PDO::FETCH_OBJ);
        $VerifCompte=$VerifComptePoint->rowCount();
               
        //Si 1er achat
        if($VerifCompte==0) {
            $NbBon=$TotalPoint / $Param->tranche;
            $NbBon=floor($NbBon);
            
            //Si plusieurs Bon de reduction
            if($NbBon>=2) {
                $Cadeau=$Cadeau + ($Param->cadeau * $NbBon);
                $Reste=$TotalPoint - ($Param->tranche * $NbBon);
                $TotalPointCumuler=$ComptePoint->total_point + $TotalPoint;
            }
            //Avec 1 Bon 
            elseif (($NbBon>=1)&&($NbBon<2)) {
                $Cadeau=$Cadeau + $Param->cadeau;
                $Reste = $TotalPoint - $Param->tranche;
                $TotalPointCumuler=$TotalPoint;
            }
            //Sans Bon
            else {
                $Cadeau=$Cadeau + 0;
                $Reste=$TotalPoint;
                $TotalPointCumuler=$TotalPoint;
            }
        }
        //Autre Achat
        else {
            $NbBon=($ComptePoint->reste + $TotalPoint) / $Param->tranche;
            $NbBon=floor($NbBon);
            //Si plusieurs Bon de reduction
            if($NbBon>=2) {
                $Cadeau=$Cadeau + ($Param->cadeau * $NbBon);
                $Reste=($ComptePoint->reste + $TotalPoint) - ($Param->tranche * $NbBon);
                $TotalPointCumuler=$ComptePoint->total_point + $TotalPoint;
            }
            //Avec 1 Bon 
            elseif (($NbBon>=1)&&($NbBon<2)) {
                $Cadeau=$Cadeau + $Param->cadeau;
                $Reste=($ComptePoint->reste + $TotalPoint) - $Param->tranche;
                $TotalPointCumuler=$ComptePoint->total_point + $TotalPoint;
            }
            //Sans Bon
            else {
                $Cadeau=$Cadeau + 0;
                $Reste=$ComptePoint->reste + $TotalPoint;
                $TotalPointCumuler=$ComptePoint->total_point + $TotalPoint;
            }
        }  
        
        $InsertPoint=$cnx->prepare("UPDATE ".$Prefix."Historique SET prix=:prix, point_J=:point_J, tranche=:tranche, reste=:reste, total_point=:total_point, activate='1' WHERE client=:client AND hash=:hash");
        $InsertPoint->bindParam(':prix', $Total, PDO::PARAM_STR);
        $InsertPoint->bindParam(':point_J', $TotalPoint, PDO::PARAM_STR);
        $InsertPoint->bindParam(':tranche', $Param->tranche, PDO::PARAM_STR);
        $InsertPoint->bindParam(':reste', $Reste, PDO::PARAM_STR);
        $InsertPoint->bindParam(':total_point', $TotalPointCumuler, PDO::PARAM_STR);
        $InsertPoint->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $InsertPoint->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
        $InsertPoint->execute();

        if (($NbBon>=0)&&($NbBon<1)&&($Reedition==0)) {
            unset($_SESSION['etape']);
            unset($_SESSION['HashTransac']);
            unset($_SESSION['carte']);
            unset($_SESSION['nom']);
            unset($_SESSION['Hashclient']);
            unset($_SESSION['TotalArticle']);
            unset($_SESSION['cadeau']);
            unset($_SESSION['TotalMoinCadeau']);
            unset($_SESSION['Fidelite']);
            unset($_SESSION['Bon']);
        }
        else {
            //Ajout du bon de fidelite
            //Generer un code barre EAN13
            $CountNbFid=$cnx->prepare("SELECT * FROM ".$Prefix."Bon_Fidelite WHERE client=:client");
            $CountNbFid->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $CountNbFid->execute();
            $NbFid=$CountNbFid->rowCount();

            $CodePaysFid="145";
            $CodeFid=$NbFid + 1;
            $CodeFid=trim(money_format('%=0(#5.0n', $CodeFid));
            $Cle="";

            $CodeBar12_2=$CodePaysFid.$CodeEntreprise.$CodeFid;

            for ($i2=0;$i2<=11;$i2++) {
                if($i2%2==1) {
                    $N2[$i2]=$CodeBar12_2[$i2]*3;
                }
                else {
                    $N2[$i2]=$CodeBar12_2[$i2]*1;
                }
                $Total2+=$N2[$i2]; 
            }
            $Reste_2=fmod($Total2,10);
            if ($Reste_2!=0) {
                $Cle_2=10-$Reste_2;
            }
            else {
                $Cle_2=$Reste_2;
            }

            $CodeBar13_2=$CodePaysFid.$CodeEntreprise.$CodeFid.$Cle_2;
            
            $Consomed=($Now + ($Param->validite * 2635200));

            $InsertBon=$cnx->prepare("INSERT INTO ".$Prefix."Bon_Fidelite (gencode, fidelite, created, consomed, cadeau, hash_client, hash_transac, client) VALUES(:gencode, '1', :created, :consomed, :cadeau, :hash_client, :hash_transac, :client)");
            $InsertBon->bindParam(':gencode', $CodeBar13_2, PDO::PARAM_STR);
            $InsertBon->bindParam(':created', $Now, PDO::PARAM_STR);
            $InsertBon->bindParam(':consomed', $Consomed, PDO::PARAM_STR);
            $InsertBon->bindParam(':cadeau', $Cadeau, PDO::PARAM_STR);
            $InsertBon->bindParam(':hash_client', $_SESSION['Hashclient'], PDO::PARAM_STR);
            $InsertBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $InsertBon->bindParam(':hash_transac', $_SESSION['HashTransac'], PDO::PARAM_STR);
            $InsertBon->execute();
            
            $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Historique SET fidelite='1' WHERE client=:client AND hash=:hash");
            $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $UpdateBon->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
            $UpdateBon->execute();
            
            $_SESSION['etape']="Etape4";
        }
    }
}
//Valider Mode Stock
//Terminer la Transation
if (isset($_POST['Terminer'])) { 
    $Now=time();
    $Total=$_SESSION['TotalMoinCadeau'];
    $Bon = $_SESSION['Bon'];
    
    //Verif Panier vide
    if ($CompteurPanier==0) {
        $Erreur="Le panier est vide, impossible de valider la transaction !";
    }
    else {
        //Retirer les produits du stock
        $SelectPanier=$cnx->prepare("SELECT * FROM ".$Prefix."Historique_produit WHERE client=:client AND hash=:hash");
        $SelectPanier->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $SelectPanier->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
        $SelectPanier->execute(); 
            
        while($Panier=$SelectPanier->fetch(PDO::FETCH_OBJ)) {
            $SelectQuantiter=$cnx->prepare("SELECT * FROM ".$Prefix."produit WHERE gencode=:gencode AND client=:client");
            $SelectQuantiter->bindParam(':gencode', $Panier->gencode, PDO::PARAM_STR);
            $SelectQuantiter->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $SelectQuantiter->execute(); 
            $QuantitePanier=$SelectQuantiter->fetch(PDO::FETCH_OBJ);

            $QuantiteTotal=$QuantitePanier->quantite - $Panier->quantite;
            
            $UpdateStock=$cnx->prepare("UPDATE ".$Prefix."produit SET quantite=:quantite WHERE gencode=:gencode AND client=:client");
            $UpdateStock->bindParam(':gencode', $Panier->gencode, PDO::PARAM_STR);
            $UpdateStock->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $UpdateStock->bindParam(':quantite', $QuantiteTotal, PDO::PARAM_STR);
            $UpdateStock->execute();  
        }
               
        //Calcule des points cumuler au Jour J
        $ParamFidelite=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client ORDER by id DESC");    
        $ParamFidelite->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $ParamFidelite->execute(); 
        $Param=$ParamFidelite->fetch(PDO::FETCH_OBJ);
        
        $TotalPoint= ($Total * $Param->conversion);
        $Valided=($Now + ($Param->validite * 2635200));
                
        //Calcul des points et Verif si bon de reduction
        $VerifComptePoint=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE client=:client AND hash_client=:hash_client AND activate='1' ORDER BY id DESC");    
        $VerifComptePoint->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $VerifComptePoint->bindParam(':hash_client', $ClientFid->hash, PDO::PARAM_STR);
        $VerifComptePoint->execute(); 
        $ComptePoint=$VerifComptePoint->fetch(PDO::FETCH_OBJ);
        $VerifCompte=$VerifComptePoint->rowCount();
        
        //Si 1er achat
        if($VerifCompte==0) {
            $NbBon=($TotalPoint / $Param->tranche);
            $NbBon=floor($NbBon);
            //Si plusieurs Bon de reduction
            if($NbBon>=2) {
                $Cadeau=$Param->cadeau * $NbBon;
                $Reste=$TotalPoint - ($Param->tranche * $NbBon);
                $TotalPointCumuler=$ComptePoint->total_point + $TotalPoint;
            }
            //Avec 1 Bon 
            elseif (($NbBon>=1)&&($NbBon<2)) {
                $Cadeau=$Param->cadeau;
                $Reste = $TotalPoint - $Param->tranche;
                $TotalPointCumuler=$TotalPoint;
            }
            //Sans Bon
            else {
                $Cadeau=0;
                $Reste=$TotalPoint;
                $TotalPointCumuler=$TotalPoint;
            }
        }
        //Autre Achat
        else {
            $NbBon=($ComptePoint->reste + $TotalPoint) / $Param->tranche;
            $NbBon=floor($NbBon);
            //Si plusieurs Bon de reduction
            if($NbBon>=2) {
                $Cadeau=$Param->cadeau * $NbBon;
                if ($TotalPoint>0) {
                    $Reste=($ComptePoint->reste + $TotalPoint) - ($Param->tranche * $NbBon);
                    $TotalPointCumuler=$ComptePoint->total_point + $TotalPoint;
                }
                else {
                    $Reste=$ComptePoint->reste;
                    $TotalPointCumuler=$ComptePoint->total_point;
                }
            }
            //Avec 1 Bon 
            elseif (($NbBon>=1)&&($NbBon<2)) {
                $Cadeau=$Param->cadeau;
                if ($TotalPoint>0) {
                    $Reste=($ComptePoint->reste + $TotalPoint) - $Param->tranche;
                    $TotalPointCumuler=$ComptePoint->total_point + $TotalPoint;
                }
                else {
                    $Reste=$ComptePoint->reste;
                    $TotalPointCumuler=$ComptePoint->total_point;
                }
            }
            //Sans Bon
            else {
                $Cadeau=0;
                if ($TotalPoint>0) {
                    $Reste=$ComptePoint->reste + $TotalPoint;
                    $TotalPointCumuler=$ComptePoint->total_point + $TotalPoint;
                }
                else {
                    $Reste=$ComptePoint->reste;
                    $TotalPointCumuler=$ComptePoint->total_point;
                }
            }
        }  
        
        $InsertPoint=$cnx->prepare("UPDATE ".$Prefix."Historique SET prix=:prix, point_J=:point_J, tranche=:tranche, reste=:reste, total_point=:total_point, activate='1' WHERE client=:client AND hash=:hash");
        $InsertPoint->bindParam(':prix', $Total, PDO::PARAM_STR);
        $InsertPoint->bindParam(':point_J', $TotalPoint, PDO::PARAM_STR);
        $InsertPoint->bindParam(':tranche', $Param->tranche, PDO::PARAM_STR);
        $InsertPoint->bindParam(':reste', $Reste, PDO::PARAM_STR);
        $InsertPoint->bindParam(':total_point', $TotalPointCumuler, PDO::PARAM_STR);
        $InsertPoint->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
        $InsertPoint->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
        $InsertPoint->execute();
        
        if ($_SESSION['Fidelite']==1) {
            //REVOIR LE CODE ICI
                    
            $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Bon_Fidelite SET fidelite='2' WHERE client=:client AND gencode=:gencode");
            $UpdateBon->bindParam(':gencode', $Bon, PDO::PARAM_STR);
            $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $UpdateBon->execute();
            
            $SelectBon=$cnx->prepare("SELECT * FROM ".$Prefix."Bon_Fidelite WHERE client=:client AND gencode=:gencode");    
            $SelectBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);   
            $SelectBon->bindParam(':gencode', $Bon, PDO::PARAM_STR);
            $SelectBon->execute(); 
            $InfoBon=$SelectBon->fetch(PDO::FETCH_OBJ);
            
            $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Historique SET fidelite='2' WHERE client=:client AND hash=:hash");
            $UpdateBon->bindParam(':hash', $InfoBon->hash_transac, PDO::PARAM_STR);
            $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $UpdateBon->execute();
        }
       
        if (($NbBon>=0)&&($NbBon<1)&&($_SESSION['reedition']==0)) {
            unset($_SESSION['etape']);
            unset($_SESSION['HashTransac']);
            unset($_SESSION['carte']);
            unset($_SESSION['nom']);
            unset($_SESSION['Hashclient']);
            unset($_SESSION['reedition']);
            unset($_SESSION['cadeau']);
            unset($_SESSION['TotalArticle']);
            unset($_SESSION['TotalMoinCadeau']);
            unset($_SESSION['Fidelite']);
            unset($_SESSION['Bon']);
        }
        else {
            //Ajout du bon de fidelite
            //Generer un code barre EAN13
            $CountNbFid=$cnx->prepare("SELECT * FROM ".$Prefix."Bon_Fidelite WHERE client=:client");
            $CountNbFid->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $CountNbFid->execute();
            $NbFid=$CountNbFid->rowCount();

            $CodePaysFid="145";
            $CodeFid=$NbFid + 1;
            $CodeFid=trim(money_format('%=0(#5.0n', $CodeFid));
            $Cle="";

            $CodeBar12_2=$CodePaysFid.$CodeEntreprise.$CodeFid;

            for ($i2=0;$i2<=11;$i2++) {
                if($i2%2==1) {
                    $N2[$i2]=$CodeBar12_2[$i2]*3;
                }
                else {
                    $N2[$i2]=$CodeBar12_2[$i2]*1;
                }
                $Total2+=$N2[$i2]; 
            }
            $Reste_2=fmod($Total2,10);
            if ($Reste_2!=0) {
                $Cle_2=10-$Reste_2;
            }
            else {
                $Cle_2=$Reste_2;
            }

            $CodeBar13_2=$CodePaysFid.$CodeEntreprise.$CodeFid.$Cle_2;
            
            $Consomed=($Now + ($Param->validite * 2635200));
            
            $InsertBon=$cnx->prepare("INSERT INTO ".$Prefix."Bon_Fidelite (gencode, fidelite, created, consomed, cadeau, hash_client, hash_transac, client) VALUES(:gencode, '1', :created, :consomed, :cadeau, :hash_client, :hash_transac, :client)");
            $InsertBon->bindParam(':gencode', $CodeBar13_2, PDO::PARAM_STR);
            $InsertBon->bindParam(':created', $Now, PDO::PARAM_STR);
            $InsertBon->bindParam(':consomed', $Consomed, PDO::PARAM_STR);
            $InsertBon->bindParam(':cadeau', $Cadeau, PDO::PARAM_STR);
            $InsertBon->bindParam(':hash_client', $_SESSION['Hashclient'], PDO::PARAM_STR);
            $InsertBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $InsertBon->bindParam(':hash_transac', $_SESSION['HashTransac'], PDO::PARAM_STR);
            $InsertBon->execute();
            
            $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Historique SET fidelite='1' WHERE client=:client AND hash=:hash");
            $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
            $UpdateBon->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
            $UpdateBon->execute();
            
            $_SESSION['etape']="Etape4";
        }
    }
}


//reset transac
if (isset($_POST['reset'])) {
    $DeleteTransac=$cnx->prepare("DELETE FROM ".$Prefix."Historique WHERE hash=:hash AND client=:client");
    $DeleteTransac->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
    $DeleteTransac->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $DeleteTransac->execute(); 
    
    $DeleteProduitTransac=$cnx->prepare("DELETE FROM ".$Prefix."Historique_produit WHERE hash=:hash AND client=:client");
    $DeleteProduitTransac->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
    $DeleteProduitTransac->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
    $DeleteProduitTransac->execute(); 
    
    unset($_SESSION['etape']);
    unset($_SESSION['HashTransac']);
    unset($_SESSION['carte']);
    unset($_SESSION['nom']);
    unset($_SESSION['Hashclient']);
    unset($_SESSION['reedition']);
    unset($_SESSION['cadeau']);
    unset($_SESSION['TotalArticle']);
    unset($_SESSION['TotalMoinCadeau']);
    unset($_SESSION['Fidelite']);
    unset($_SESSION['Bon']);
}


// Deduction fidelite
if (isset($_POST['FideliteValid'])) { 
    $Now=time();
    $Bon=$_POST['BonGencode'];
    $_SESSION['reedition']=0;
    
    if ($_SESSION['Fidelite']==1) {
        $Erreur="Vous ne pouvez pas cumuler les bons d'achat !";
    }
    else {    
        // recup des info du bon d'achat
        $VerifBon=$cnx->prepare("SELECT * FROM ".$Prefix."Bon_Fidelite WHERE client=:client AND gencode=:gencode");    
        $VerifBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);   
        $VerifBon->bindParam(':gencode', $Bon, PDO::PARAM_STR);
        $VerifBon->execute(); 
        $NbBon=$VerifBon->rowCount();
        $InfoBon=$VerifBon->fetch(PDO::FETCH_OBJ);
        
        if ($NbBon==0) {      
            $Erreur="Aucun bon de réduction ne correspond à ce code barre !";
        }
        elseif($NbBon==1) {
            if ($InfoBon->consomed < $Now) {
                $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Bon_Fidelite SET fidelite='0' WHERE client=:client AND gencode=:gencode");
                $UpdateBon->bindParam(':gencode', $Bon, PDO::PARAM_STR);
                $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                $UpdateBon->execute();
                
                $UpdateBon=$cnx->prepare("UPDATE ".$Prefix."Historique SET fidelite='0' WHERE client=:client AND hash=:hash");
                $UpdateBon->bindParam(':hash', $InfoBon->hash_transac, PDO::PARAM_STR);
                $UpdateBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
                $UpdateBon->execute();
                
                $Erreur="Le bon de fidelité est arrivé à expiration !";
            }
            elseif($InfoBon->consomed >= $Now) {
                if ($InfoBon->fidelite==1) {                
                    //réédition bon de reduct si non cumul total
                    if ($InfoBon->cadeau > $_SESSION['TotalArticle']) {
                        $_SESSION['Fidelite']=1;
                        $_SESSION['reedition']=1;
                        $_SESSION['Bon'] = $Bon;
                        $_SESSION['cadeau'] = $InfoBon->cadeau;      
                    }
                    else {
                        $_SESSION['Fidelite']=1;
                        $_SESSION['Bon'] = $Bon;
                        $_SESSION['cadeau'] = $InfoBon->cadeau;      
                    }
                }
                elseif ($InfoBon->fidelite==2) {
                    $Erreur="Le bon d'achat a déja été consommé";
                }
                elseif ($InfoBon->fidelite==3) {
                    $Erreur="Le bon d'achat a été annuler";
                }
                else {
                    $Erreur="Une erreur est survenue, plusieur bon de réduction on le même gencode !";
                }
            }
        }
        else {
            $Erreur="Une erreur est survenue, plusieur bon de réduction on le même gencode !";
        }    
    }
}


//ETAPE 4 --------------------------------------------------------------------------------------------------------------------------------------------------------

if (isset($_POST['Terminer4'])) {
    unset($_SESSION['etape']);
    unset($_SESSION['HashTransac']);
    unset($_SESSION['carte']);
    unset($_SESSION['nom']);
    unset($_SESSION['Hashclient']);
    unset($_SESSION['reedition']);
    unset($_SESSION['cadeau']);
    unset($_SESSION['TotalArticle']);
    unset($_SESSION['TotalMoinCadeau']);
    unset($_SESSION['Fidelite']);
    unset($_SESSION['Bon']);
}

$RecupInfoBon=$cnx->prepare("SELECT * FROM ".$Prefix."Historique WHERE hash_client=:hash_client AND hash=:hash AND client=:client");
$RecupInfoBon->bindParam(':hash_client', $_SESSION['Hashclient'], PDO::PARAM_STR);
$RecupInfoBon->bindParam(':hash', $_SESSION['HashTransac'], PDO::PARAM_STR);
$RecupInfoBon->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$RecupInfoBon->execute();
$InfoFidelite=$RecupInfoBon->fetch(PDO::FETCH_OBJ);

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

<?php 
//ETAPE 1 ------------------------------------------------------------------------------------------------------------------------------
if (!isset($_SESSION['etape'])) { ?>

<div id="Form_Middle">
<H1 class="TitreBleu">Encaissement</H1>
    
<form name="form_recherche" action="" method="POST">
<p>
<input type="text" name="carte" placeholder="N° de carte" autofocus/> 
</p>
<input  type="text" placeholder="Nom de famille" name="nom"/> 
<p>
<input type="submit" class="ButtonBleu" name="Recherche" value="Rechercher"/>
</p>
</form>
</div>

<?php
} 
//selection du bon client via son nom
//ETAPE 2 ------------------------------------------------------------------------------------------------------------------------------
if ((isset($_SESSION['etape']))&&($_SESSION['etape']=="Etape2")) { ?>
<H2>Selection du client</H2>

<table>
<tr>
    <th class="TableBleu">
        Gencode
    </th>
    <th class="TableBleu">
        Nom
    </th>
    <th class="TableBleu">
        Prenom
    </th>
    <th class="TableBleu">
        Adresse
    </th>
    <th class="TableBleu">
        Code postal
    </th>
    <th class="TableBleu">
        Ville
    </th>
    <th class="TableBleu">
        Action
    </th>
</tr>
<?php
while($ClientFid=$SelectClient->fetch(PDO::FETCH_OBJ)) { ?>
<form name="SelectClient" action="" method="POST">
    <tr>
        <td class="TableBleu">
            <?php echo $ClientFid->carte; ?>
        </td>
        <td class="TableBleu">
            <?php echo stripslashes($ClientFid->nom); ?>
        </td>
        <td class="TableBleu">
            <?php echo stripslashes($ClientFid->prenom); ?>
        </td>
        <td class="TableBleu">
            <?php echo nl2br(stripslashes($ClientFid->adresse)); ?>
        </td>
        <td class="TableBleu">
            <?php echo $ClientFid->cp; ?>
        </td>
        <td class="TableBleu">
            <?php echo stripslashes($ClientFid->ville); ?>
        </td>
        <td class="TableBleu">
            <?php  echo "<input type='radio' name='selection' required=required value='".$ClientFid->carte."'/>"; ?>
        </td>
    </tr>
<?php 
}
?>
</table>
<p><input type="submit" class="ButtonBleu" name="Selection" value="Selectionner"/>
</form>
<form name="reset" action="" method="POST">
<input type="submit" class="ButtonBleu" name="reset2" value="Annuler"/>
</form></p>
<?php
}
//ETAPE 3 ------------------------------------------------------------------------------------------------------------------------------
if ((isset($_SESSION['etape']))&&($_SESSION['etape']=="Etape3")) { ?>
<div id="ColoneLeft">
       
    <form name="SelectMode" action="" method="POST">
    <span class="col_1_log">Mode <font color='#FF0000'>*</font> : </span> 
    <select name="mode" onchange="this.form.submit()">
        <option value="0" <?php if ($_SESSION['mode']==0) { echo "selected"; } ?>>Normal</option>
        <?php if ($Option->stock==1) { ?>
            <option value="1" <?php if ($_SESSION['mode']==1) { echo "selected"; } ?>>Stock</option>
        <?php } ?>
    </select>
    </form>
    
    <p><HR /></p>

    <?php echo "<label class='col_1'>N° de carte : </label><b>".$ClientFid->carte."</b><BR />
    <label class='col_1'>Nom du client : </label><b>".$ClientFid->nom." ".$ClientFid->prenom."</b></p>"; ?>
    
    <p><HR /></p>
    
    <?php 
    if ($_SESSION['mode']==0) { ?>
        </div>
        <div id="Form_Middle">
        <H1 class="TitreBleu">Mode Normal</H1>
        <form name="Transac" action="" method="POST">
        <input type="text" name="Total" placeholder="Total"/> 
        <BR /><BR />
        <input type="text" name="BonGencode" placeholder="Déduire un bon de fidélité"/>         
        <BR /><BR />
        <input type="submit" class="ButtonBleu" name="ValiderTr" value="Valider la transaction" onclick="return(confirm('Êtes vous sur de vouloir valider la transaction ?'));"/>
        <BR /><BR /> 
        <form name="reset" action="" method="POST">
        <input type="submit" class="ButtonBleu" name="reset" value="Annuler la transaction" onclick="return(confirm('Êtes vous sur de vouloir annuler la transaction ?'));"/>
        </form>
        </div>
    <?php 
    } 
    if ($_SESSION['mode']==1) { ?>
        
        <H2>Déduire un bon de fidélité</H2>
        
        <form name="form_Fidelite" action="" method="POST">
        <input class="moyen" type="text" name="BonGencode"/> 
        <BR />
        <input type="submit" class="ButtonBleu" name="FideliteValid" value="Déduire"/>
        </form>  

        </div>
        <div id="ColoneRight">
            
        <H2>Panier client</H2>
        
        <form name="TerminerTransac" action="" method="POST">
        <input class="Terminer" type="submit" name="Terminer" value="Valider la transaction" onclick="return(confirm('Êtes vous sur de vouloir valider la transaction ?'));"/>
        </form>

        <form name="reset" action="" method="POST">
        <input class="Annuler" type="submit" name="reset" value="Annuler la transaction" onclick="return(confirm('Êtes vous sur de vouloir annuler la transaction ?'));"/>
        </form>
        
        <table>
        <TR>
            <th class="TableBleu">
                Gencode
            </TH>
            <th class="TableBleu">
                Description
            </TH>
            <th class="TableBleu">
                Quantité
            </TH>
            <th class="TableBleu">
                Prix
            </TH>
            <th class="TableBleu">
                Action
            </TH>
        </TR>
        <?php
        $_SESSION['TotalArticle']=0;
        while($ArticlePanier=$SelectPanierExist->fetch(PDO::FETCH_OBJ)) { ?>
            <TR>
                <td class="TableBleu">
                    <?php echo $ArticlePanier->gencode; ?>
                </TD>
                <td class="TableBleu">
                    <?php echo stripslashes($ArticlePanier->description); ?>
                </TD>
                <td class="TableBleu">
                    <?php echo $ArticlePanier->quantite; ?>
                </TD>
                <td class="TableBleu">
                    <?php echo number_format($ArticlePanier->prix, 2,".", ""); ?>
                </TD>
                <td class="TableBleu">
                    <a href="<?php echo $Home; ?>/DashBoard/Encaissement/Suppr.php?id=<?php echo $ArticlePanier->id; ?>"><acronym title="Supprimer"><img src="<?php echo $Home; ?>/lib/img/Button/supprimer-bleu.png"/></acronym></a>
                </TD>
            </TR>

            <?php
            $TotalArticle+=$ArticlePanier->quantite;
            $_SESSION['TotalArticle']+=($ArticlePanier->prix * $ArticlePanier->quantite);
            $_SESSION['TotalMoinCadeau']= $_SESSION['TotalArticle'] - $_SESSION['cadeau'];
        }
        ?>
        <TR>
            <td class="TableBleu">

            </TD>
            <td class="TableBleu">
                TOTAL
            </TD>
            <td class="TableBleu">
                <?php echo $TotalArticle; ?>
            </TD>
            <td class="TableBleu">
                <?php echo number_format($_SESSION['TotalMoinCadeau'], 2,".", "")." ¤"; ?>
            </TD>
            <td class="TableBleu">
                
            </TD>
        </TR>
        </table>
        
        <p><HR /></p>

        <H2>Produit existant</H2>

        <table>
        <TR>
            <th class="TableBleu">
                Gencode
            </TH>
            <th class="TableBleu">
                Description
            </TH>
            <th class="TableBleu">
                Categorie
            </TH>
            <th class="TableBleu">
                Quantité
            </TH>
            <th class="TableBleu">
                Prix
            </TH>
            <th class="TableBleu">
                Action
            </TH>
        </TR>

        <form name="form_recherche" action="" method="POST">
        <TR>
            <td class="TableBleu">
                <input class="moyen" type="text" name="RechercheGencode" autofocus/>
            </TD>
            <td class="TableBleu">
                <input class="description" type="text" name="RechercheDescription"/>
            </TD>
            <td class="TableBleu">
                <select name="categorie">
                    <option value="">-- --</option><?php
                    while ($Categorie=$SelectCategorieExist->fetch(PDO::FETCH_OBJ)) { 
                    echo "<option value='".$Categorie->nom."'>".$Categorie->nom."</option>";
                    } ?>
                </select>
            </TD>
            <td class="TableBleu">
                
            </TD>
            <td class="TableBleu">
                <input class="mini" type="text" name="RecherchePrix"/>
            </TD>
            <td class="TableBleu">
                <input type="submit" class="ButtonBleu" name="MoteurRecherche" value="Rechercher"/>
            </TD>
        </TR>
        </form>

        <?php
        while($ArticleExist=$SelectArticleExist->fetch(PDO::FETCH_OBJ)) { ?>
            <form name="form_ajout_exist" action="" method="POST">
            <input type="hidden" name="gencode" value="<?php echo $ArticleExist->gencode; ?>"/>
            <input type="hidden" name="description" value="<?php echo $ArticleExist->description; ?>"/>
            <input type="hidden" name="prix" value="<?php echo $ArticleExist->prix; ?>"/>
            <TR>
                <td class="TableBleu">
                    <?php echo $ArticleExist->gencode; ?>
                </TD>
                <td class="TableBleu">
                    <?php echo stripslashes($ArticleExist->description); ?>
                </TD>
                <td class="TableBleu">
                    <?php echo stripslashes($ArticleExist->categorie); ?>
                </TD>
                <td class="TableBleu">
                    <input class="mini" type="text" name="quantite"/>
                </TD>
                <td class="TableBleu">
                    <?php echo number_format($ArticleExist->prix, 2,".", ""); ?>
                </TD>
                <td class="TableBleu">
                    <input type="submit" class="ButtonBleu" name="Ajouter1" value="Ajouter"/>
                </TD>
            </TR>
            </form>
            <?php
        } ?>
        </table>
        <?php    
    } 
    ?></div><?php
} 

//ETAPE 4 ------------------------------------------------------------------------------------------------------------------------------
if ((isset($_SESSION['etape']))&&($_SESSION['etape']=="Etape4")) { ?>

    <form name="Terminer" action="" method="POST">
    <input class="Terminer" type="submit" name="Terminer4" value="Terminer"/>
    </form>

    <H2>Bon de fidélité client</H2>

    <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Historique/Apercu/?id=<?php echo $InfoFidelite->id; ?>&type=mail&page=1"><acronym title="Envoyer au client par E-mail"><img src="<?php echo $Home; ?>/lib/img/button-mailing.png"/></acronym></a>
    
    <iframe src="<?php echo $Home; ?>/DashBoard/Encaissement/Apercu/" width="1160" height="660"></iframe>
<?php  
} ?>

</article>
    
</div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/footer.inc.php"); ?>
</center>
</body>
</html>