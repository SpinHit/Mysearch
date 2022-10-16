<?php

require('connexion.php');
     

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
                $motvide = [];

                // on ouvre motvide.txt et on met chaque mot dans $list
                $file = fopen('motvide.txt', 'r');
                while(!feof($file)){
                    $mot = fgets($file);
                    $mot = trim($mot);
                    $motvide[] = $mot;
                }
                fclose($file);

                

                $contenu = str_replace(" / "," ",$contenu);
                $contenu = mb_strtolower($contenu,"UTF-8");
                
                
                // on filtre les phrases du fichier
                $contenu = preg_replace('/[^a-zA-Z0-9àâäéèêëîïôöùûüçÀÂÄÉÈÊËÎÏÔÖÙÛÜÇ ]/', ' ', $contenu);
                // on met le contenu dans un tableau
                $contenu = explode(" ", $contenu);
                // on enleve les mots de moins de 3 charactères et on compte les redondances et que le mot n'est pas dans la liste des mots vides
                foreach ($contenu as $key => $value) {
                    if (strlen($value) < 3 || in_array($value, $motvide)) {
                        unset($contenu[$key]);
                    }

                }
                return $contenu;
                      
                }

            function indexeetaffiche($pname,$contenuClean,$pdo){
                
                //on va afficher le resultat de la fonction count_values et les ajouter dans la bdd
                $result = count_values($contenuClean);
                foreach($result as $mot => $nb){
                    // echo $mot." ".$nb." ".'<br>';
                    $sql = "INSERT into tablemots(mot, redondance, nom_du_fichier) VALUES ('$mot','$nb','$pname')";
                    $pdo->query($sql) ;
                }
                echo'<div id="listePagination">'.'Le fichier '. $pname." a etait ouvert et etait transférer dans la base de donnée avec succes".'</div>' ;
                }

            // on retourne les mots les plus redondants d'un nom de fichier donné
            function motredondant($nom_fichier,$pdo){
                $redondance1=0;
                $taille = 10;
                $chaine='';
                // on récupère le mot dans la bdd par rapport à la recherche par ordre de redondance le plus grand au plus petit
                $sql = "SELECT * FROM tablemots WHERE nom_du_fichier = '$nom_fichier' ORDER BY redondance DESC";
                $result = $pdo->query($sql);
                $result = $result->fetchAll();
                // on mélange le tableau
                shuffle($result);
                // on affiche le mot le plus redondant et on fait grandir la taille de la police en fonction de la redondance
                foreach($result as $key => $value){
                    if($value['redondance'] > 2){
                        $chaine = $chaine.'<span style="font-size:'.$taille*$value['redondance'].'px">'.$value['mot'].'</span>'.' ';
                    }
                    // on affiche les 40 premiers mots a la redondance 1
                    if($value['redondance'] == 1 && $redondance1 < 40){
                        $chaine = $chaine.'<span style="font-size:'.'15'.'px">'.$value['mot'].'</span>'.' ';
                        $redondance1++;   
                    }
                    
                }
                return $chaine;

            }
            // on ouvre un lien html et on scrape le site pour récupérer le texte
            function openhtml($url){
                $html = file_get_contents($url);
                $tags = explode('<',$html);
                foreach ($tags as $tag)
                {
                // on skip les balises scripts
                if (strpos($tag,'script') !== FALSE) continue;
                // get text
                $text = strip_tags('<'.$tag);
                // only if text present remember
                if (trim($text) != '') $texts[] = $text;
                }

                return $texts;
            }



            // fonction permettant de chercher un mot dans la bdd et de l'afficher avec le nombre de redondance et le nom du fichier
            function recherche($mot,$pdo){
                // on récupère le mot dans la bdd par rapport à la recherche par ordre de redondance le plus grand au plus petit
                $sql = "SELECT * FROM tablemots WHERE mot LIKE '%$mot%' ORDER BY redondance DESC";
                $result = $pdo->query($sql);
                $result = $result->fetchAll();
                // on affiche le resultat de la recherche trier par le nombre de redondance le plus grand
                ?> 
                <div class="tbl-content">
                    
                    
                
                
                
                <?php
                foreach($result as $key => $value){
                    // on affiche le nom du fichier en cliquable et le nuage de mots
                    echo $value['mot'].' '.'['.$value['redondance'].']'.' '.'<a href="#">'.$value['nom_du_fichier'].'<div class="tooltipcontainer">'.'<div class="tooltip">'.motredondant($value['nom_du_fichier'],$pdo).'</div>'.'</div>'.'</a>'.'<br>';
                }

                // si le mot n'est pas dans la bdd on affiche un message d'erreur
                if(empty($result)){
                    echo "Nous n'avons pas trouvé le mot recherché";
                }
                ?>
                
                
                
                </div>
                
                
                <?php

            } 




            
                
    

?>