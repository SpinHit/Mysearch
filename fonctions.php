<?php

require('connexion.php');

// fonction pour téléversser l'image
 function insereBddDossier($pdo,$pname,$psize,$dest){
    $sql = "INSERT into images (file_name, size , chemin) VALUES ('$pname','$psize','$dest')";
    
     // dossier ou l'on va insserer les images 
    
     
    if ($_FILES['mon_fichier']['error'] > 0) $erreur = "Erreur lors du transfert";
     // upload de l'image dans le dossier 
    $resultat = move_uploaded_file($_FILES['mon_fichier']['tmp_name'],$dest.$_FILES['mon_fichier']['name']);
    
    if ($resultat) $pdo->query($sql) ; echo "Transfert réussi"; header("Refresh:0");
    }

    // fonction permettant de récupérer l'extention d'un fichier
    function recupExtention($fname) {
        return substr(strrchr($fname,'.'),1);
        }





            function count_values($array) {
                $result = array();
               
                foreach ($array as $value) {
                    if (isset($result[$value])) {
                        $result[$value]++;
                    } else {
                        $result[$value] = 1;
                    }
                }
                return $result;
            }

            function Filtre($contenu){
                // on filtre les phrases du fichier
                $contenu = preg_replace('/[^a-zA-Z0-9àâäéèêëîïôöùûüçÀÂÄÉÈÊËÎÏÔÖÙÛÜÇ ]/', ' ', $contenu);
                // on met le contenu dans un tableau
                $contenu = explode(" ", $contenu);
                // on enleve les mots de moins de 3 charactères et on compte les redondances
                foreach ($contenu as $key => $value) {
                    if (strlen($value) < 3) {
                        unset($contenu[$key]);
                    }
                
                
                /*     // on compte les redondances de chaque mot dans le tableau $contenu et on les met dans un tableau $mots 
                    $mots[$value] = (isset($mots[$value])) ? $mots[$value] + 1 : 1; */
                }
                return $contenu;
                
                /*     // on insere les mots dans la bdd
                    $sql = "INSERT into mots (mot, nb) VALUES ('$key','$value')";
                    $pdo->query($sql) ; */
                
                
                }
    

?>