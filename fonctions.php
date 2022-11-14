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
                echo'<div id="listePagination">'.'Le fichier '. $pname." a etait ouvert et etait transférer dans la base de donnée avec succes".'</div>'. '<br>';
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



            // on va récuperer le contenu de la page et le mettre dans un tableau avec comme info le titre, la description, les keywords, le contenu
            function getMeta($url){
                $html = file_get_contents($url);
                $dom = new DOMDocument();
                $keywords ='';
                $description ='';
                @$dom->loadHTML($html);
                $metas = $dom->getElementsByTagName('title');
                if ($metas->length > 0) {
                    $meta = $metas->item(0);
                    $title = $meta->nodeValue;
                }
                else{
                    $title = $url;
                }
                $metas = $dom->getElementsByTagName('meta');
                for ($i = 0; $i < $metas->length; $i++)
                {
                    $meta = $metas->item($i);
                    if($meta->getAttribute('name') == 'description')
                        $description = $meta->getAttribute('content');
                    if($meta->getAttribute('name') == 'keywords')
                        $keywords = $meta->getAttribute('content');
                }
                $body = $dom->getElementsByTagName('body');
                $body = $body->item(0);
                $p = $body->getElementsByTagName('p');
                $chaine = '';
                foreach($p as $key => $value){
                    $chaine = $chaine.$value->nodeValue;
                }
                $contenu = $chaine;

                $tableau = array('title' => $title, 'description' => $description, 'keywords' => $keywords, 'contenu' => $contenu);
                return $tableau;
            }

            function getMetaFile($path){
                $fp = fopen($path, 'r');
                //on récupére le contenu du body
                $contenu = stream_get_contents($fp);
                $dom = new DOMDocument();
                $dom->loadHTML($contenu);
                $metas = $dom->getElementsByTagName('title');
                $meta = $metas->item(0);
                $title = $meta->nodeValue;
                $metas = $dom->getElementsByTagName('meta');
                for ($i = 0; $i < $metas->length; $i++)
                {
                    $meta = $metas->item($i);
                    if($meta->getAttribute('name') == 'description')
                        $description = $meta->getAttribute('content');
                    if($meta->getAttribute('name') == 'keywords')
                        $keywords = $meta->getAttribute('content');
                }
                $body = $dom->getElementsByTagName('body');
                $body = $body->item(0);
                $p = $body->getElementsByTagName('p');
                $chaine = '';
                foreach($p as $key => $value){
                    $chaine = $chaine.$value->nodeValue;
                }
                $contenu = $chaine;
                $tableau = array('title' => $title, 'description' => $description, 'keywords' => $keywords, 'contenu' => $contenu);
                fclose($fp);
                return $tableau;
            }

            function indexeUrlEtFichier($pdo,$url){
                // si c'est un string qui commence par http 
                if(is_string($url) && substr($url,0,4) == 'http'){
                    $Listebalises = getMeta($url);
                }
                else{
                    $Listebalises = getMetaFile($url);
                }
                // on met le contenu de 'contenu' dans la liste $Listebalises
                $contenuClean = filtre($Listebalises['contenu']);
                $contenu = $Listebalises['contenu'];
                $titre = $Listebalises['title'];
                // descirption va valoir la description de la page et si il n'ya pas de decrpition alors on va mettre les 150 premiers mots de la page suivit de ...
            
                // si la liste est null alors on l'initialise a vide
                if($Listebalises['keywords'] == null){
                    $keywords = " ";
                }else{
                    $keywords = $Listebalises['keywords'];
                }
            
                // si la liste est null alors on insert les 25 premiers mots du contenu de la page
                if($Listebalises['description'] == null){
                    $description = implode(" ",array_slice($contenu,0,25))."...";
                }else{
                    $description = $Listebalises['description'];
                }
                
                foreach($contenuClean as $mot){
                    $poid = poidsMot($mot,$contenuClean,$titre,$description,$keywords);
                        // on regarde si le mot et l'url sont deja dans la bdd
                        $sql = "SELECT * FROM tableurl WHERE url = :url AND mot = :mot";
                        $req = $pdo->prepare($sql);
                        $req->execute(array(
                            'url' => $url,
                            'mot' => $mot
                        ));
                        $result = $req->fetch();
                        if($result == false){
                            $sql = "INSERT INTO tableurl (url, title, description, keywords, mot, poid) VALUES (:url, :title, :description, :keywords, :mot, :poid)";
                        $req = $pdo->prepare($sql);
                        $req->execute(array(
                            'url' => $url,
                            'title' => $titre,
                            'description' => $description,
                            'keywords' => $keywords,
                            'mot' => $mot,
                            'poid' => $poid
                        ));
                        }
                }
                echo 'le lien ou le fichier '.$url.' a bien été indexé <br>';
            }

            // on va récuperer le contenu de la page et le mettre dans un tableau avec comme info le titre, la description, les keywords, le contenu d'une page HTML local donné en paramètre
            

            // fonction permettant de chercher un mot dans la bdd et de l'afficher avec le nombre de redondance et le nom du fichier
            function recherche($mot,$pdo){
                // on récupère le mot dans la bdd par rapport à la recherche par ordre de de poid sans utiliser LIKE
                $sql = "SELECT * FROM tableurl WHERE mot = :mot ORDER BY poid DESC";
                $req = $pdo->prepare($sql);
                $req->execute(array(
                    'mot' => $mot
                ));
                $result = $req->fetchAll();
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