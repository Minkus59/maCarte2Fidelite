<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

if ($Cnx_User!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$SelectMail=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Mail ORDER BY id DESC");
$SelectMail->execute();

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Now=time();

if ((isset($_POST['Envoyer']))&&($_POST['Envoyer']=="Envoyer")) {  
    $Retour=FiltreEmail('email');
    if ((isset($_POST['destinataire']))&&(!empty($_POST['destinataire']))) {
        if ((isset($_POST['objet']))&&(!empty($_POST['objet']))) {
            if ((isset($_POST['message']))&&(!empty($_POST['message']))) {               
                if (preg_match("#^[A-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['retour'])) { 
                    
                    $Destinataire2=$_POST['destinataire'];
                    $Objet=$_POST['objet'];
                    $Message=$_POST['message'];
                    $Retour=$_POST['retour'];
                    
                    if ((isset($_FILES['fichier1']['name']))&&(!empty($_FILES['fichier1']['name']))) {
                    
                        $Fichier1=$_FILES['fichier1']['name'];
                        $FichierTmp1=$_FILES['fichier1']['tmp_name'];
                        $NomFichier1=basename($Fichier1);
                        $Taille1=filesize($FichierTmp1);
                        $ExtOrigin1=strchr($Fichier1, '.');
                        $TailleMax="20000000";
                        
                        $Code1=md5(uniqid(rand(), true));
                        $Hash1=substr($Code1, 0, 8);

                        $RepInt=$_SERVER['DOCUMENT_ROOT']."/lib/Mail/Document/";
                        $RepExt=$Home."/lib/Mail/Document/";
                        
                        //upload fichier
                        
                        $Upload1 = move_uploaded_file($FichierTmp1, $RepInt.$Hash1.$ExtOrigin1);

                        if ($Upload1==FALSE) {
                            $Erreur="Erreur de téléchargement du fichier, veuillez réassayer ultèrieurement";
                        }
                        else {
                            $CheminFichier1 = $RepInt.$Hash1.$ExtOrigin1;
                            // Pièce jointe
                            $content1 = file_get_contents($CheminFichier1);
                            $content1 = chunk_split(base64_encode($content1));
                        }
                    }
                    
                    if ((isset($_FILES['fichier2']['name']))&&(!empty($_FILES['fichier2']['name']))) {
                    
                        $Fichier2=$_FILES['fichier2']['name'];
                        $FichierTmp2=$_FILES['fichier2']['tmp_name'];
                        $NomFichier2=basename($Fichier2);
                        $Taille2=filesize($FichierTmp2);
                        $ExtOrigin2=strchr($Fichier2, '.');
                        $TailleMax="20000000";
                        
                        $Code2=md5(uniqid(rand(), true));
                        $Hash2=substr($Code2, 0, 8);

                        $RepInt=$_SERVER['DOCUMENT_ROOT']."/lib/Mail/Document/";
                        $RepExt=$Home."/lib/Mail/Document/";
                        
                        //upload fichier
                        
                        $Upload2 = move_uploaded_file($FichierTmp2, $RepInt.$Hash2.$ExtOrigin2);

                        if ($Upload2==FALSE) {
                            $Erreur="Erreur de téléchargement du fichier, veuillez réassayer ultèrieurement";
                        }
                        else {
                            $CheminFichier2 = $RepInt.$Hash2.$ExtOrigin2;
                            // Pièce jointe
                            $content2 = file_get_contents($CheminFichier2);
                            $content2 = chunk_split(base64_encode($content2));
                        }
                    }
                    
                    if ((isset($_FILES['fichier3']['name']))&&(!empty($_FILES['fichier3']['name']))) {
                    
                        $Fichier3=$_FILES['fichier3']['name'];
                        $FichierTmp3=$_FILES['fichier3']['tmp_name'];
                        $NomFichier3=basename($Fichier3);
                        $Taille3=filesize($FichierTmp3);
                        $ExtOrigin3=strchr($Fichier3, '.');
                        $TailleMax="20000000";
                        
                        $Code3=md5(uniqid(rand(), true));
                        $Hash3=substr($Code3, 0, 8);

                        $RepInt=$_SERVER['DOCUMENT_ROOT']."/lib/Mail/Document/";
                        $RepExt=$Home."/lib/Mail/Document/";
                        
                        //upload fichier
                        
                        $Upload3 = move_uploaded_file($FichierTmp3, $RepInt.$Hash3.$ExtOrigin3);

                        if ($Upload3==FALSE) {
                            $Erreur="Erreur de téléchargement du fichier, veuillez réassayer ultèrieurement";
                        }
                        else {
                            $CheminFichier3 = $RepInt.$Hash3.$ExtOrigin3;
                            // Pièce jointe
                            $content3 = file_get_contents($CheminFichier3);
                            $content3 = chunk_split(base64_encode($content3));
                        }
                    }
                    
                    if ((isset($_FILES['fichier4']['name']))&&(!empty($_FILES['fichier4']['name']))) {
                    
                        $Fichier4=$_FILES['fichier4']['name'];
                        $FichierTmp4=$_FILES['fichier4']['tmp_name'];
                        $NomFichier4=basename($Fichier4);
                        $Taille4=filesize($FichierTmp4);
                        $ExtOrigin4=strchr($Fichier4, '.');
                        $TailleMax="20000000";
                        
                        $Code4=md5(uniqid(rand(), true));
                        $Hash4=substr($Code4, 0, 8);

                        $RepInt=$_SERVER['DOCUMENT_ROOT']."/lib/Mail/Document/";
                        $RepExt=$Home."/lib/Mail/Document/";
                        
                        //upload fichier
                        
                        $Upload4 = move_uploaded_file($FichierTmp4, $RepInt.$Hash4.$ExtOrigin4);

                        if ($Upload4==FALSE) {
                            $Erreur="Erreur de téléchargement du fichier, veuillez réassayer ultèrieurement";
                        }
                        else {
                            $CheminFichier4 = $RepInt.$Hash4.$ExtOrigin4;
                            // Pièce jointe
                            $content4 = file_get_contents($CheminFichier4);
                            $content4 = chunk_split(base64_encode($content4));
                        }
                    }
                    
                    if ((isset($_FILES['fichier5']['name']))&&(!empty($_FILES['fichier5']['name']))) {
                    
                        $Fichier5=$_FILES['fichier5']['name'];
                        $FichierTmp5=$_FILES['fichier5']['tmp_name'];
                        $NomFichier5=basename($Fichier5);
                        $Taille5=filesize($FichierTmp5);
                        $ExtOrigin5=strchr($Fichier5, '.');
                        $TailleMax="20000000";
                        
                        $Code5=md5(uniqid(rand(), true));
                        $Hash5=substr($Code5, 0, 8);

                        $RepInt=$_SERVER['DOCUMENT_ROOT']."/lib/Mail/Document/";
                        $RepExt=$Home."/lib/Mail/Document/";
                        
                        //upload fichier
                        
                        $Upload5 = move_uploaded_file($FichierTmp5, $RepInt.$Hash5.$ExtOrigin5);

                        if ($Upload5==FALSE) {
                            $Erreur="Erreur de téléchargement du fichier, veuillez réassayer ultèrieurement";
                        }
                        else {
                            $CheminFichier5 = $RepInt.$Hash5.$ExtOrigin5;
                            // Pièce jointe
                            $content5 = file_get_contents($CheminFichier5);
                            $content5 = chunk_split(base64_encode($content5));
                        }
                    }
                    
                    $boundary = md5(uniqid(mt_rand()));
                    
                    $Entete = "MIME-Version: 1.0\n";
                    $Entete .= "Content-Type:multipart/mixed; boundary=\"$boundary\"\n";
                    $Entete .= "From: \"$Societe\"<\"$Serveur\">\n";
                    $Entete .= "Reply-to: \"$Societe\"<\"$Retour\">\n";
                    $Entete .= "\n";
                    
                    $message="Ce message est au format MIME.\n";
                    
                    $message.="--$boundary\n";
                    $message.= "Content-Type: text/html; charset=ISO-5988-15\n";  
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
                                        
                    if ((isset($_FILES['fichier1']['name']))&&(!empty($_FILES['fichier1']['name']))) {
                        if ($Upload1==TRUE) {
                            if (in_array($ExtOrigin1, array(".jpg", ".jpeg", ".jpe", ".JPG", ".JPEG", ".JPE"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            if (in_array($ExtOrigin1, array(".png", ".PNG"))) {                  
                                $message.= "Content-Type: image/png;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            if (in_array($ExtOrigin1, array(".gif", ".GIF"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            if (in_array($ExtOrigin1, array(".doc", ".DOC"))) {                  
                                $message.= "Content-Type: application/msword;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            if (in_array($ExtOrigin1, array(".pdf", ".PDF"))) {                  
                                $message.= "Content-Type: application/pdf;name=\"$Hash1$ExtOrigin1\"\n";
                            }  
                            if (in_array($ExtOrigin1, array(".rtf", ".RFT"))) {                  
                                $message.= "Content-Type: application/rtf;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            if (in_array($ExtOrigin1, array(".xls", ".XLS"))) {                  
                                $message.= "Content-Type: application/vnd.ms-excel;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            if (in_array($ExtOrigin1, array(".ppt", ".PPT"))) {                  
                                $message.= "Content-Type: application/vnd.ms-powerpoint;name=\"$Hash1$ExtOrigin1\"\n";
                            }      
                            if (in_array($ExtOrigin1, array(".zip", ".ZIP"))) {                  
                                $message.= "Content-Type: application/zip;name=\"$Hash1$ExtOrigin1\"\n";
                            } 
                            if (in_array($ExtOrigin1, array(".tif", ".tiff", "TIF", "TIFF"))) {                  
                                $message.= "Content-Type: image/tiff;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            if (in_array($ExtOrigin1, array(".avi", ".AVI"))) {                  
                                $message.= "Content-Type: video/msvideo;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            if (in_array($ExtOrigin1, array(".mov", ".qt", ".MOV", ".QT"))) {                  
                                $message.= "Content-Type: video/quicktime;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            if (in_array($ExtOrigin1, array(".mpeg", ".mpg", ".mpe", ".MPEG", ".MPG", ".MPE"))) {                  
                                $message.= "Content-Type: video/mpeg;name=\"$Hash1$ExtOrigin1\"\n";
                            }
                            
                            $message.= "Content-Transfer-Encoding: base64\n";
                            $message.= "Content-Disposition:attachment;filename=\"$Hash1$ExtOrigin1\"\n";

                            $message.="\n";
                            
                            $message.="$content1\n";   
                            
                            $message.="\n\n";   
                            $message.="--$boundary\n";                       
                        }
                    }
                    
                    if ((isset($_FILES['fichier2']['name']))&&(!empty($_FILES['fichier2']['name']))) {
                        if ($Upload2==TRUE) {
                            if (in_array($ExtOrigin2, array(".jpg", ".jpeg", ".jpe", ".JPG", ".JPEG", ".JPE"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            if (in_array($ExtOrigin2, array(".png", ".PNG"))) {                  
                                $message.= "Content-Type: image/png;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            if (in_array($ExtOrigin2, array(".gif", ".GIF"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            if (in_array($ExtOrigin2, array(".doc", ".DOC"))) {                  
                                $message.= "Content-Type: application/msword;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            if (in_array($ExtOrigin2, array(".pdf", ".PDF"))) {                  
                                $message.= "Content-Type: application/pdf;name=\"$Hash2$ExtOrigin2\"\n";
                            }  
                            if (in_array($ExtOrigin2, array(".rtf", ".RFT"))) {                  
                                $message.= "Content-Type: application/rtf;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            if (in_array($ExtOrigin2, array(".xls", ".XLS"))) {                  
                                $message.= "Content-Type: application/vnd.ms-excel;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            if (in_array($ExtOrigin2, array(".ppt", ".PPT"))) {                  
                                $message.= "Content-Type: application/vnd.ms-powerpoint;name=\"$Hash2$ExtOrigin2\"\n";
                            }      
                            if (in_array($ExtOrigin2, array(".zip", ".ZIP"))) {                  
                                $message.= "Content-Type: application/zip;name=\"$Hash2$ExtOrigin2\"\n";
                            } 
                            if (in_array($ExtOrigin2, array(".tif", ".tiff", "TIF", "TIFF"))) {                  
                                $message.= "Content-Type: image/tiff;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            if (in_array($ExtOrigin2, array(".avi", ".AVI"))) {                  
                                $message.= "Content-Type: video/msvideo;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            if (in_array($ExtOrigin2, array(".mov", ".qt", ".MOV", ".QT"))) {                  
                                $message.= "Content-Type: video/quicktime;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            if (in_array($ExtOrigin2, array(".mpeg", ".mpg", ".mpe", ".MPEG", ".MPG", ".MPE"))) {                  
                                $message.= "Content-Type: video/mpeg;name=\"$Hash2$ExtOrigin2\"\n";
                            }
                            
                            $message.= "Content-Transfer-Encoding: base64\n";
                            $message.= "Content-Disposition:attachment;filename=\"$Hash2$ExtOrigin2\"\n";

                            $message.="\n";
                            
                            $message.="$content2\n";   
                            
                            $message.="\n\n";   
                            $message.="--$boundary\n";                           
                        }
                    }
                    
                    if ((isset($_FILES['fichier3']['name']))&&(!empty($_FILES['fichier3']['name']))) {
                        if ($Upload3==TRUE) {
                            if (in_array($ExtOrigin3, array(".jpg", ".jpeg", ".jpe", ".JPG", ".JPEG", ".JPE"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            if (in_array($ExtOrigin3, array(".png", ".PNG"))) {                  
                                $message.= "Content-Type: image/png;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            if (in_array($ExtOrigin3, array(".gif", ".GIF"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            if (in_array($ExtOrigin3, array(".doc", ".DOC"))) {                  
                                $message.= "Content-Type: application/msword;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            if (in_array($ExtOrigin3, array(".pdf", ".PDF"))) {                  
                                $message.= "Content-Type: application/pdf;name=\"$Hash3$ExtOrigin3\"\n";
                            }  
                            if (in_array($ExtOrigin3, array(".rtf", ".RFT"))) {                  
                                $message.= "Content-Type: application/rtf;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            if (in_array($ExtOrigin3, array(".xls", ".XLS"))) {                  
                                $message.= "Content-Type: application/vnd.ms-excel;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            if (in_array($ExtOrigin3, array(".ppt", ".PPT"))) {                  
                                $message.= "Content-Type: application/vnd.ms-powerpoint;name=\"$Hash3$ExtOrigin3\"\n";
                            }      
                            if (in_array($ExtOrigin3, array(".zip", ".ZIP"))) {                  
                                $message.= "Content-Type: application/zip;name=\"$Hash3$ExtOrigin3\"\n";
                            } 
                            if (in_array($ExtOrigin3, array(".tif", ".tiff", "TIF", "TIFF"))) {                  
                                $message.= "Content-Type: image/tiff;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            if (in_array($ExtOrigin3, array(".avi", ".AVI"))) {                  
                                $message.= "Content-Type: video/msvideo;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            if (in_array($ExtOrigin3, array(".mov", ".qt", ".MOV", ".QT"))) {                  
                                $message.= "Content-Type: video/quicktime;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            if (in_array($ExtOrigin3, array(".mpeg", ".mpg", ".mpe", ".MPEG", ".MPG", ".MPE"))) {                  
                                $message.= "Content-Type: video/mpeg;name=\"$Hash3$ExtOrigin3\"\n";
                            }
                            
                            $message.= "Content-Transfer-Encoding: base64\n";
                            $message.= "Content-Disposition:attachment;filename=\"$Hash3$ExtOrigin3\"\n";

                            $message.="\n";
                            
                            $message.="$content3\n";   
                            
                            $message.="\n\n";   
                            $message.="--$boundary\n";                            
                        }
                    }
                    
                    if ((isset($_FILES['fichier4']['name']))&&(!empty($_FILES['fichier4']['name']))) {
                        if ($Upload4==TRUE) {
                            if (in_array($ExtOrigin4, array(".jpg", ".jpeg", ".jpe", ".JPG", ".JPEG", ".JPE"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            if (in_array($ExtOrigin4, array(".png", ".PNG"))) {                  
                                $message.= "Content-Type: image/png;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            if (in_array($ExtOrigin4, array(".gif", ".GIF"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            if (in_array($ExtOrigin4, array(".doc", ".DOC"))) {                  
                                $message.= "Content-Type: application/msword;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            if (in_array($ExtOrigin4, array(".pdf", ".PDF"))) {                  
                                $message.= "Content-Type: application/pdf;name=\"$Hash4$ExtOrigin4\"\n";
                            }  
                            if (in_array($ExtOrigin4, array(".rtf", ".RFT"))) {                  
                                $message.= "Content-Type: application/rtf;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            if (in_array($ExtOrigin4, array(".xls", ".XLS"))) {                  
                                $message.= "Content-Type: application/vnd.ms-excel;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            if (in_array($ExtOrigin4, array(".ppt", ".PPT"))) {                  
                                $message.= "Content-Type: application/vnd.ms-powerpoint;name=\"$Hash4$ExtOrigin4\"\n";
                            }      
                            if (in_array($ExtOrigin4, array(".zip", ".ZIP"))) {                  
                                $message.= "Content-Type: application/zip;name=\"$Hash4$ExtOrigin4\"\n";
                            } 
                            if (in_array($ExtOrigin4, array(".tif", ".tiff", "TIF", "TIFF"))) {                  
                                $message.= "Content-Type: image/tiff;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            if (in_array($ExtOrigin4, array(".avi", ".AVI"))) {                  
                                $message.= "Content-Type: video/msvideo;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            if (in_array($ExtOrigin4, array(".mov", ".qt", ".MOV", ".QT"))) {                  
                                $message.= "Content-Type: video/quicktime;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            if (in_array($ExtOrigin4, array(".mpeg", ".mpg", ".mpe", ".MPEG", ".MPG", ".MPE"))) {                  
                                $message.= "Content-Type: video/mpeg;name=\"$Hash4$ExtOrigin4\"\n";
                            }
                            
                            $message.= "Content-Transfer-Encoding: base64\n";
                            $message.= "Content-Disposition:attachment;filename=\"$Hash4$ExtOrigin4\"\n";

                            $message.="\n";
                            
                            $message.="$content4\n";   
                            
                            $message.="\n\n";   
                            $message.="--$boundary\n";                            
                        }
                    }
                    
                    if ((isset($_FILES['fichier5']['name']))&&(!empty($_FILES['fichier5']['name']))) {
                        if ($Upload5==TRUE) {
                            if (in_array($ExtOrigin5, array(".jpg", ".jpeg", ".jpe", ".JPG", ".JPEG", ".JPE"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            if (in_array($ExtOrigin5, array(".png", ".PNG"))) {                  
                                $message.= "Content-Type: image/png;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            if (in_array($ExtOrigin5, array(".gif", ".GIF"))) {                  
                                $message.= "Content-Type: image/jpeg;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            if (in_array($ExtOrigin5, array(".doc", ".DOC"))) {                  
                                $message.= "Content-Type: application/msword;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            if (in_array($ExtOrigin5, array(".pdf", ".PDF"))) {                  
                                $message.= "Content-Type: application/pdf;name=\"$Hash5$ExtOrigin5\"\n";
                            }  
                            if (in_array($ExtOrigin5, array(".rtf", ".RFT"))) {                  
                                $message.= "Content-Type: application/rtf;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            if (in_array($ExtOrigin5, array(".xls", ".XLS"))) {                  
                                $message.= "Content-Type: application/vnd.ms-excel;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            if (in_array($ExtOrigin5, array(".ppt", ".PPT"))) {                  
                                $message.= "Content-Type: application/vnd.ms-powerpoint;name=\"$Hash5$ExtOrigin5\"\n";
                            }      
                            if (in_array($ExtOrigin5, array(".zip", ".ZIP"))) {                  
                                $message.= "Content-Type: application/zip;name=\"$Hash5$ExtOrigin5\"\n";
                            } 
                            if (in_array($ExtOrigin5, array(".tif", ".tiff", "TIF", "TIFF"))) {                  
                                $message.= "Content-Type: image/tiff;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            if (in_array($ExtOrigin5, array(".avi", ".AVI"))) {                  
                                $message.= "Content-Type: video/msvideo;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            if (in_array($ExtOrigin5, array(".mov", ".qt", ".MOV", ".QT"))) {                  
                                $message.= "Content-Type: video/quicktime;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            if (in_array($ExtOrigin5, array(".mpeg", ".mpg", ".mpe", ".MPEG", ".MPG", ".MPE"))) {                  
                                $message.= "Content-Type: video/mpeg;name=\"$Hash5$ExtOrigin5\"\n";
                            }
                            
                            $message.= "Content-Transfer-Encoding: base64\n";
                            $message.= "Content-Disposition:attachment;filename=\"$Hash5$ExtOrigin5\"\n";

                            $message.="\n";
                            
                            $message.="$content5\n";      
                            
                            $message.="\n\n";   
                            $message.="--$boundary\n";                         
                        }
                    }

                    if (mail($Destinataire2, $Objet, $message, $Entete)===FALSE) {
                        $Erreur = "L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement !";
                    }
                    else {
                        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Mail (destinataire, objet, message, retour, created) VALUES(:destinataire, :objet, :message, :retour, :created)");
                        $Insert->BindParam(":destinataire", $Destinataire2, PDO::PARAM_STR);
                        $Insert->BindParam(":objet", $Objet, PDO::PARAM_STR);
                        $Insert->BindParam(":message", $Message, PDO::PARAM_STR);
                        $Insert->BindParam(":retour", $Retour, PDO::PARAM_STR);
                        $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                        $Insert->execute();
                        
                        if ($Insert===FALSE) {
                            $Erreur="Erreur de base de donnée, veuillez contacter l'administrateur du site Internet";
                        }
                        else {
                            if ((isset($_FILES['fichier1']['name']))&&(!empty($_FILES['fichier1']['name']))) {
                                unlink($RepInt.$Hash1.$ExtOrigin1); 
                            }
                            if ((isset($_FILES['fichier2']['name']))&&(!empty($_FILES['fichier2']['name']))) {
                                unlink($RepInt.$Hash2.$ExtOrigin2); 
                            }
                            if ((isset($_FILES['fichier3']['name']))&&(!empty($_FILES['fichier3']['name']))) {
                                unlink($RepInt.$Hash3.$ExtOrigin3); 
                            }
                            if ((isset($_FILES['fichier4']['name']))&&(!empty($_FILES['fichier4']['name']))) {
                                unlink($RepInt.$Hash4.$ExtOrigin4); 
                            }
                            if ((isset($_FILES['fichier5']['name']))&&(!empty($_FILES['fichier5']['name']))) {
                                unlink($RepInt.$Hash5.$ExtOrigin5); 
                            }
                                                  
                            $Valid="Votre message a bien été envoyé !";
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

<!doctype html>
<html>
<head>
<meta charset="ISO-5988-15">

<title>NeuroSoft Team - Accès PRO</title>
  
<meta name="robots" content="noindex, nofollow">

<meta name="author".content="NeuroSoft Team">
<meta name="publisher".content="Helinckx Michael">
<meta name="reply-to" content="contact@neuro-soft.fr">

<meta name="viewport" content="width=device-width" >                                                            

<link rel="shortcut icon" href="<?php echo $Home; ?>/Admin/lib/img/icone.ico">

<link rel="stylesheet" type="text/css" media="screen AND (max-width: 480px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatel.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 480px) AND (max-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpatab.css" />
<link rel="stylesheet" type="text/css" media="screen AND (min-width: 960px)" href="<?php echo $Home; ?>/Admin/lib/css/misenpapc.css" >

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

<H1>Envoyer un e-mail</H1></p>

<form name="form_mail" action="" method="POST" enctype="multipart/form-data">

<input type="text" placeholder="à€ :" name="destinataire" require="required"/></p>

<input type="text" placeholder="Objet :" name="objet" require="required"/></p>

<input type="text" placeholder="Adresse de retour" name="retour" value="<?php echo $Destinataire; ?>" require="required"/></p>

<input type="file"  placeholder="pièce jointe 1" name="fichier1"/><BR />
<input type="file"  placeholder="pièce jointe 2" name="fichier2"/><BR />
<input type="file"  placeholder="pièce jointe 3" name="fichier3"/><BR />
<input type="file"  placeholder="pièce jointe 4" name="fichier4"/><BR />
<input type="file"  placeholder="pièce jointe 5" name="fichier5"/></p>

<textarea id="message" name="message" placeholder="Message*" require="required">
    <p><a href="<?php echo $Home; ?>/Admin/"><img src="<?php echo $Home; ?>/Admin/lib/img/En-tete.png"/></a></p>

    <p></p>
    
    <p>Nous restons à  votre disposition pour tous renseignements.</p>

    <p>Cordialement Mr Helinckx Michael<BR />
    ----------------------------------------------<BR />
    NeuroSoft Team<BR />
    Les artisans du web<BR />
    Téléphone : 03 20 64 54 22<BR />
    E-mail : contact@neuro-soft.fr<BR />
    Internet : http://www.neuro-soft.fr</p>
</textarea></p>

<input type="submit" name="Envoyer" value="Envoyer"/>
</form>

<p><HR /></p>

<H1>Liste des e-mails envoyé</H1></p>

<table>
<tr><th>Destinataire</th><th>Objet</th><th>Message</th><th>Date</th></tr>
<?php

while ($Mail=$SelectMail->fetch(PDO::FETCH_OBJ)) {
?>
   <tr>
   <td><?php echo $Mail->destinataire; ?></td>
   <td><?php echo $Mail->objet; ?></td>
   <td><?php echo $Mail->message; ?></td>
   <td><?php echo date("d-m-Y", $Mail->created); ?></td>
<?php
}
?>
</table>

</article>
</section>
</div>
</CENTER>
</body>

</html>