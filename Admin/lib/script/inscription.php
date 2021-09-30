<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
if ((isset($_GET['NeuroSoft']))&&($_GET['NeuroSoft']=="CQDFX301")) {
   if (isset($_POST['inscription'])) {
        $Email=FiltreEmail('email');
        $Mdp=FiltreMDP('mdp');
        $Mdp2=FiltreMDP('mdp2');

        $Entete ='From: "no-reply@kgs-express.fr"<postmaster@kgs-express.fr>'."\r\n";
        $Entete .= 'MIME-Version: 1.0' . "\r\n";                        
        $Entete .='Content-Type: text/html; charset="iso-8859-15"'."\r\n";
        $Entete .='Content-Transfer-Encoding: 8bit'; 
        $Message ="<html><head><title>Validation d'inscription</title>
            </head><body>
            <font color='#9e2053'><H1>Validation d'inscription</H1></font>          
            Veuillez cliquer sur le lien suivant pour valider votre inscription.</p>
            <a href='".$Home."/Admin/Validation/?id=".$Email."&Valid=1'>Cliquez ici</a></p>
            ____________________________________________________</p>
            Cordialement NeuroSoft Team<br />
            www.neuro-soft.fr</p>
            <font color='#FF0000'>Cet e-mail contient des informations confidentielles et / ou prot�g�es par la loi. Si vous n'en �tes pas le v�ritable destinataire ou si vous l'avez re�u par erreur, informez-en imm�diatement son exp�diteur et d�truisez ce message. La copie et le transfert de cet e-mail sont strictement interdits.</font>                 
            </body></html>";

        if ($Email[0]===false) {
           $Erreur=$Email[1];
           echo $Erreur;
        }

        elseif ($Mdp[0]===false) {
           $Erreur=$Mdp[1];
           echo $Erreur;
        }
        elseif ($Mdp2[0]===false) {
           $Erreur=$Mdp2[1]; 
           echo $Erreur;
        }
        elseif ($Mdp2!=$Mdp) {
           $Erreur="Les mots de passe ne sont pas identique !"; 
           echo $Erreur;
        }
        else {
            $RecupClient=$cnx->prepare("SELECT * FROM neuro_compte_Admin WHERE email=:email");
            $RecupClient->bindParam(':email', $Email, PDO::PARAM_STR);
            $RecupClient->execute();
            $RecupC=$RecupClient->fetch(PDO::FETCH_OBJ);
            $NbRowsEmail=$RecupClient->rowCount();

            if ($NbRowsEmail==1) {
                $Erreur="Cette adresse E-mail existe d�j� !<br />"; 
                echo $Erreur;
            }
            else {
                 $InsertUser=$cnx->prepare("INSERT INTO neuro_compte_Admin (email, created) VALUES (:email, NOW())");
                 $InsertUser->bindParam(':email', $Email, PDO::PARAM_STR);
                 $InsertUser->execute();

                 $RecupCreated=$cnx->prepare("SELECT (created) FROM neuro_compte_Admin WHERE email=:email");
                 $RecupCreated->bindParam(':hash_client', $Client, PDO::PARAM_STR);
                 $RecupCreated->execute();
                 $DateCrea=$RecupCreated->fetch(PDO::FETCH_OBJ);

                 $Salt=md5($DateCrea->created);
                 $Mdp2=md5($Mdp2);
                 $MdpCrypt=crypt($Mdp2, $Salt);

                 $UpdateUser=$cnx->prepare("UPDATE neuro_compte_Admin set mdp=:mdp WHERE email=:email");
                 $UpdateUser->bindParam(':email', $Email, PDO::PARAM_STR);
                 $UpdateUser->bindParam(':mdp', $MdpCrypt, PDO::PARAM_STR);
                 $UpdateUser->execute();

                 if ($UpdateUser) {
                      if (!mail($Email, "Validation d'inscription", $Message, $Entete)) {
                            $Erreur="L'e-mail de confirmation n'a pu �tre envoy�, v�rifiez que vous l'avez entr� correctement !<br />"; 
                            echo $Erreur;
                      }
                        
                      else {
                           $Valid="Bonjour,<br />";
                           $Valid.="Merci de vous �tre inscrit<br />";
                           $Valid.="Un E-mail de confirmation vous a �t� envoy� � l'adresse suivante : ".$Email."<br />";
                           $Valid.="Veuillez valider votre adresse e-mail avant de vous connecter !<br />";  
                          echo $Valid;
                      }
                 }
            }
       }
   }
}
?>