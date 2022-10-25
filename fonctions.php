<?php

require('connexion.php');
     

    function recupExtention($fname) {
        return substr(strrchr($fname,'.'),1);
        }

            // fonction qui permet de compter le nombre de fois qu'un mot apparait dans un tableau
            function count_Values($array, $value) {
                $count = 0;
                foreach ($array as $v) {
                    if ($v == $value) {
                        $count++;
                    }
                }
                return $count;
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
                // on remplace les caractères spéciaux par des espaces en laissant les lettres avec les accents
                $contenu = preg_replace('/[^a-zA-Z0-9À-ÿ\'` ]/u', ' ', $contenu);
                // on met tout en minuscule
                $contenu = mb_strtolower($contenu,"UTF-8");    
                $contenu = explode(" ", $contenu);
                // on enleve les mots de moins de 3 charactères et on compte les redondances et que le mot n'est pas dans la liste des mots vides
                foreach ($contenu as $key => $value) {
                    if (strlen($value) < 3 || in_array($value, $motvide)) {
                        unset($contenu[$key]);
                    }
                }
                return $contenu;
                }

            // la fonction indexeetaffiche va prendre en parametre le nom du fichier et de son contenu et va l'indexer mot par mot et afficher le resultat
            function indexeetaffiche($pname,$contenuClean,$pdo){
                
                //on va afficher le resultat de la fonction count_values et les ajouter dans la bdd
                $result = count_values($contenuClean);
                foreach($result as $mot => $nb){
                    // echo $mot." ".$nb." ".'<br>';
                    $sql = 'INSERT into tablemots(mot, redondance, nom_du_fichier) VALUES (:mot, :nb, :pname)';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['mot' => $mot, 'nb' => $nb, 'pname' => $pname]);
                
                    //$pdo->query($sql) ;
                }
                echo'<div id="listePagination">'.'Le fichier '. $pname." a etait ouvert et etait transférer dans la base de donnée avec succes".'</div>' ;
                }

            // on retourne les mots les plus redondants d'un nom de fichier donné
            function nuageMots($url,$pdo){
                $redondance1=0;
                $chaine='';
                $pixelvaleur=15;
                // on récupère le mot dans la bdd par rapport à la recherche par ordre de redondance le plus grand au plus petit
                $sql = "SELECT * FROM tableurl WHERE url = '$url' ORDER BY poid DESC";
                $result = $pdo->query($sql);
                $result = $result->fetchAll();
                // on mélange le tableau
                shuffle($result);

                // on calcule une valeur pour la multiplier par la taille de la police pour avoir une taille de police qui varie en fonction du poid et que la taille ne soit pas trop grande ou trop petite
                $max = $result[0]['poid'];
                $min = $result[count($result)-1]['poid'];
                $diff = $max - $min;
                $diff = $diff/10;
                $diff = $diff + 1;
                // on affiche le mot le plus redondant et on fait grandir la taille de la police en fonction de la redondance

                foreach($result as $key => $value){
                    $taille = $value['poid']/$diff;
                    $taille = round($taille);
                    // on prend une couleur au hasard dans le tableau
                    $couleur = array('red','blue','green','yellow','orange','purple','pink','brown','grey','black');
                    $couleur = $couleur[array_rand($couleur)];
                    if($value['poid'] > 2){
                        $chaine = $chaine.'<span style="font-size:'.$taille+$pixelvaleur.'px; color:'.$couleur.';">'.$value['mot'].'</span> ';
                    }
                    // on affiche les 5 premiers mots a la redondance 1
                    if($value['poid'] == 1 && $redondance1 < 5){
                        $chaine = $chaine.'<span style="font-size:'.$taille+$pixelvaleur.'px; color:'.$couleur.'">'.$value['mot'].'</span>'.' ';
                        $redondance1++;   
                    }
                    
                }


                return $chaine;

            }
            // fonction pour calculer la taille du mot pour pas qu'il soit trop grand ou trop petit par rapport au poids, on va prendre le poids le plus grand et le plus petit et on va calculer la taille du mot en fonction de la taille de la police


            // fonction qui retourne le poids d'un mot en fonction de sa redondance dans un contenu donné et de sa redondance dans contenu2 donné et de contenu3 donné et de contenu4 donné
            function poidsMot($mot,$contenu,$title,$description,$keywords){
                $poids = 0;
                $poids = $poids + count_values($contenu,$mot);
                $poids = $poids + (count_values(Filtre($title),$mot)*3);
                $poids = $poids + (count_values(Filtre($description),$mot)*2);
                $poids = $poids + (count_values(Filtre($keywords),$mot)*2);
                return $poids;
            }


            // fonction ou on récupére ce qu'il y a dans une balise meta title et si il n'y a rien on retourne le nom du fichier
            function getMetaTitle($url){
                $html = file_get_contents($url);
                $dom = new DOMDocument();
                @$dom->loadHTML($html);
                $metas = $dom->getElementsByTagName('title');
                if ($metas->length > 0) {
                    $meta = $metas->item(0);
                    return $meta->nodeValue;
                }
                else{
                    return $url;
                }
            }

            // fonction ou on récupére ce qu'il y a dans une balise meta description et si il n'y a rien on retourne rien
            function getMetaDescription($url){
                // on ouvre le fichier
                $html = file_get_contents($url);
                // on crée un objet DOMDocument
                $dom = new DOMDocument();
                // on charge le contenu du fichier dans l'objet
                @$dom->loadHTML($html);
                // on récupére les balises meta description
                $metas = $dom->getElementsByTagName('meta');
                // on parcours les balises meta 
                for ($i = 0; $i < $metas->length; $i++)
                {
                    // on récupére le nom de la balise
                    $meta = $metas->item($i);
                    // si le nom de la balise est description on retourne le contenu de la balise
                    if($meta->getAttribute('name') == 'description')
                        return $meta->getAttribute('content');
                }
                // si il n'y a pas de balise description on retourne rien
                return '';
            }

            // fonction ou on récupére ce qu'il y a dans une balise meta keywords et si il n'y a rien on retourne rien
            function getMetaKeywords($url){
                // on ouvre le fichier
                $html = file_get_contents($url);
                // on crée un objet DOMDocument
                $dom = new DOMDocument();
                // on charge le contenu du fichier dans l'objet
                @$dom->loadHTML($html);
                // on récupére les balises meta keywords
                $metas = $dom->getElementsByTagName('meta');
                // on parcours les balises meta 
                for ($i = 0; $i < $metas->length; $i++)
                {
                    // on récupére le nom de la balise
                    $meta = $metas->item($i);
                    // si le nom de la balise est keywords on retourne le contenu de la balise
                    if($meta->getAttribute('name') == 'keywords')
                        return $meta->getAttribute('content');
                }
                // si il n'y a pas de balise keywords on retourne rien
                return '';
            }

            //fonction pour récupérer le texte dans les balises p du body et le retourner
            function getBody($url){
                $html = file_get_contents($url);
                $dom = new DOMDocument();
                @$dom->loadHTML($html);
                $body = $dom->getElementsByTagName('body');
                $body = $body->item(0);
                $p = $body->getElementsByTagName('p');
                $chaine = '';
                foreach($p as $key => $value){
                    $chaine = $chaine.$value->nodeValue;
                }
                return $chaine;
            }

            // fonction permettant de chercher un mot dans la bdd et de l'afficher avec le nombre de redondance et le nom du fichier
            function recherche($mot,$pdo){
                // on récupère le mot dans la bdd par rapport à la recherche par ordre de redondance le plus grand au plus petit
                $sql = "SELECT * FROM tableurl WHERE mot LIKE '%$mot%' ORDER BY poid DESC";
                $result = $pdo->query($sql);
                $result = $result->fetchAll();
                // on affiche le resultat de la recherche trier par le nombre de redondance le plus grand
                ?> 
                <div class="tbl-content">
                <?php
                foreach($result as $key => $value){
                    // on affiche le nom du fichier en cliquable et le nuage de mots
                    echo $value['mot'].' '.'['.$value['poid'].']'.' '.'<a href="'.$value['url'].'">'.$value['title'].'<div class="invisiblecontainer">'.'<div class="invisible">'.nuageMots($value['url'],$pdo).'</div>'.'</div>'.'</a>'.'<br>'.'<p>'.$value['description'].'</p>'.'<br>';
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