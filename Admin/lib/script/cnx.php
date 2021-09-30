<?php
header('Content-Type: text/html; charset=ISO-8859-15');
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
//if (isset($_POST['OK'])) {


    $Email=FiltreEmail('email');
    $Mdp=FiltreMDP('mdp');
    
    if ($Email[0]===false) {
       $Erreur=$Email[1];
       echo $Erreur;
    }

    elseif ($Mdp[0]===false) {
       $Erreur=$Mdp[1]; 
       echo $Erreur;
    }
    else {
        $RecupClient=$cnx->prepare("SELECT * FROM neuro_compte_Admin WHERE email=:email");
        $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
        $RecupClient->execute();
        $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);
        $NbRowsEmail=$RecupClient->rowCount();

        if ($NbRowsEmail!=1) {
            echo "Cette adresse E-mail ne correspond � aucun compte !<br />";
        }
        elseif ($RecupC->activate!=1) {
            echo "le compte n'est pas activ�, veuillez activer votre compte avant de vous connecter!<br />";
        }

        else {
            $Salt=md5($RecupC->created);
            $Mdp=md5($Mdp);
            $MdpCrypt=crypt($Mdp, $Salt);
        

            $Mdp=$cnx->prepare("SELECT * FROM neuro_compte_Admin WHERE mdp=:mdp AND email=:email");
            $Mdp->bindParam(':mdp', $MdpCrypt, PDO::PARAM_STR);
            $Mdp->bindParam(':email', $Email, PDO::PARAM_STR);
            $Mdp->execute();
            $nb_rows=$Mdp->rowCount();

            if ($nb_rows!=1) { 
                echo "Le mot de passe ne correspond pas � cette adresse e-mail !<br />"; 
            }
            else {  
                session_start();                
                $_SESSION['NeuroClient']=$Email;
                echo "Vous �tes connect� ";
            } 
        }
    }
//}
?>