<?php

namespace MoriniereRobinDev\DevoirApp\Model;

use MoriniereRobinDev\WebFramework;

class ViewApp extends WebFramework\View\View {

// ################ Home Page ################ //
    public function makeHomePage($files) {
        if(key_exists('user', $_SESSION)){
            $title = "Bienvenue <br> " . self::htmlesc($_SESSION['user']['prenom']);
        }
        else{
            $title = "Bienvenue !";
        }

        $content = '<section class="homeSection">';

        foreach($files as $key => $value){
            $content .= '<a href="index.php?obj=pdf&action=showDetailsFile&id='.$value.'">';
            $content .= '<h3>'.$value.'</h3>';
            if(file_exists('DevoirApp/Model/Upload/FirstPages/'.$value.'.jpg')){
                $content .= '<img src="DevoirApp/Model/Upload/FirstPages/'.$value.'.jpg" alt="Image doc pdf : '.$value.'">';
            }
            else{
                $content .= '<img src="DevoirApp/Model/Upload/Images/default_pdf_image.jpg" alt="Image">';
            }
            $content .= '</a>';
        }

        $content .= '</section>';

        $this->setPart('title', $title);
        $this->setPart('content', $content);
    }


// ################ Upload ################ //
    public function makeUploadPage(){
        $title = "Page d'upload";

        $content = '<section class="uploadPageSection">';
        $content .= '<form id="dropFileForm"
            action="index.php?obj=pdf&action=upload" method="POST"
            onsubmit="uploadFiles(event)" enctype="multipart/form-data">';

        $content .= '<div id="dropFileDiv"
            ondragover="overrideDefault(event);fileHover();" ondragenter="overrideDefault(event);fileHover();" ondragleave="overrideDefault(event);fileHoverEnd();" ondrop="overrideDefault(event);fileHoverEnd();addFiles(event);">';

        $content .= '<label for="fileInput" id="fileLabel">';
        $content .= '<i class="fas fa-upload"></i> ';
        $content .= '<span id="fileLabelText">Choose a file </span> ';
        $content .= '<span id="uploadStatus"></span> ';
        $content .= '<i class="fas fa-upload"></i>';
        $content .= '</label>';

        $content .= '<input type="file" name="files[]" id="fileInput" multiple onchange="addFiles(event)">';

        $content .= '</div>';
        $content .= '<input type="submit" value="Upload" id="uploadButton">';
        $content .= '</form>';

        // $content .= '<input type="file" name="pdf" id="fileUpload">';
        // $content .= '<progress id="progressBar"></progress>';
        // $content .= '<input type="text" name="titre" placeholder="titre">';
        // $content .= '<button type="submit" id="subFileUpload">Envoyer</button>';
        //
        // $content .= '</form>';
        $content .= '</section>';

        $this->setPart('title', $title);
        $this->setPart('content', $content);
    }



// ################ Display Upload ################ //
    public function displayUploadSucces($filename){
        $this->router->POSTredirect('index.php?obj=pdf&action=showDetailsFile&id='.$filename, '<p class="feedback">Votre fichier '.$filename.' à bien été enregistré</p>');
    }

    public function displayUploadFailure($errors){
        $this->router->POSTredirect("index.php?obj=pdf&action=makeUploadPage", "<p class='feedback'>Erreur lors de l'upload : ".$errors."</p>");
    }


// ################ Details Page ################ //
    public function makeDetailsPage($id, $data, $filePrec = null, $fileSuiv = null){
        $title = "Details fichier : <br> ".$id;

        $content = '<section class="detailsPageSection">';
        // Bouton suivant - précédent
        $content .= '<div>';
        if($filePrec != null){
            $content .= '<a id="option" href="index.php?obj=pdf&action=showDetailsFile&id='.$filePrec.'">Fichier précédent</a>';
        }
        if($fileSuiv != null){
            $content .= '<a id="option" href="index.php?obj=pdf&action=showDetailsFile&id='.$fileSuiv.'">Fichier suivant</a>';
        }
        $content .= '</div>';

        // Image 1er page pdf
        if(file_exists('DevoirApp/Model/Upload/FirstPages/'.$id.'.jpg')){
            $content .= '<img src="DevoirApp/Model/Upload/FirstPages/'.$id.'.jpg" alt="Image doc pdf : '.$id.'">';
        }
        else{
            $content .= '<img src="DevoirApp/Model/Upload/Images/default_pdf_image.jpg" alt="Image">';
        }

        // Metadata IPTC
        $metaIPTC = array('Author', 'Title', 'Language', 'Format', 'Date', 'Creator', 'Producer',  'Contributor', 'Description');

        $content .= '<h3>Metadata de type IPTC</h3>';
        $content .= '<ul>';
        foreach($data as $key => $value){
            if(in_array($key, $metaIPTC) && $value != null){
                if(!is_array($data[$key])){
                    $content .= '<li><strong>'.$key.'</strong> : '.$value.'</li>';
                }
                else{
                    $content .= '<li><strong>'.$key.'</strong> : /';
                    foreach($data[$key] as $k => $v){
                        $content .= '/ '.$v.' /';
                    }
                    $content .= '/</li>';
                }
            }
        }
        $content .= '</ul>';


        // Metadata de type File
        $metaFile = array('FileName', 'FileSize', 'FileModifyDate', 'FileAccessDate', 'FileInodeChangeDate', 'FilePermissions', 'FileType', 'FileTypeExtension');

        $content .= '<h3>Metadata de type File</h3>';
        $content .= '<ul>';
        foreach($data as $key => $value){
            if(in_array($key, $metaFile) && $value != null){
                $content .= '<li><strong>'.$key.'</strong> : '.$value.'</li>';
            }
        }
        $content .= '</ul>';


        // // All Metadata
        // $content .= '<ul>';
        // foreach($data as $key => $value){
        //     if($key != 'Contributor'){
        //         $content .= '<li>'.$key.' : '.$value.'</li>';
        //     }
        // }
        // $content .= '</ul>';

        // Bouton Paiment
        $content .= '<a id="paiement" href="index.php?obj=pdf&action=paiement&id='.$id.'">Acheter ce Document</a>';

        $content .= '</section>';

        $this->setPart('title', $title);
        $this->setPart('content', $content);
    }



// ################ List Page ################ //
    public function makeListPage($files){
        $title = "Page liste fichier";

        $content = '<section class="listPageSection">';

        foreach($files as $key => $value){
            $content .= '<div class="elem">';
            $content .= '<a href="index.php?obj=pdf&action=showDetailsFile&id='.$value.'">'.$value.'</a>';

            $content .= '<a id="option" href="index.php?obj=pdf&action=modificationDetailsFile&id='.$value.'">Modification</a>';

            $content .= '<a id="supprimer" href="index.php?obj=pdf&action=askSuppressionFile&id='.$value.'">Supprimer</a>';

            $content .= '</div>';
        }

        $content .= '</section>';

        $this->setPart('title', $title);
        $this->setPart('content', $content);
    }


// ################ Suppression Page ################ //
    public function makeSuppresionPage($id){
        $title = "Suppression fichier : <br>".$id;

        $content = '<section class="suppressionPageSection">';
        $content .= '<h3>Voulez vous vraiment supprimer ce fichier ?</h3>';
        $content .= '<div>';

        $content .= '<a class="option" href="index.php?obj=pdf&action=showListFiles">Retour</a>';
        $content .= '<a class="supprimer" href="index.php?obj=pdf&action=suppresionFile&id='.$id.'">Supprimer</a>';

        $content .= '</div>';
        $content .= '</section>';

        $this->setPart('title', $title);
        $this->setPart('content', $content);
    }

    public function displaySuppresionFile($id){
        $this->router->POSTredirect("index.php?obj=pdf&action=showListFiles", '<p class="feedback">Suppression du fichier '.$id.' réussi.</p>');
    }


// ################ Modification Page ################ //
    public function makeModificationDetailsPage($id, $data){
        $title = "Modification fichier : <br>".$id;

        $content = '<section class="modificationPageSection">';
        $content .= '<form action="index.php?obj=pdf&action=modification&id='.$id.'" method="POST">';

        $content .= '<label>Title : </label>';
        $content .= '<input type="text" name="Title" placeholder="'.$data['Title'].'" value="">';
        // Affiche Métadonnée sous forme d'input

        $content .= '<button type="submit">Modifier</button>';
        $content .= '</form>';
        $content .= '</section>';

        $this->setPart('title', $title);
        $this->setPart('content', $content);
    }

    public function displayModificationSucces($id){
        $this->router->POSTredirect("index.php?obj=pdf&action=showDetailsFile&id=".$id, "<p class='feedback'>Votre modification est enregistré.</p>");
    }

    public function displayModificationFailure($id){
        $this->router->POSTredirect("index.php?obj=pdf&action=modificationDetailsFile&id=".$id, "<p class='feedback'>Echec de la modification.</p>");
    }


// ################ Information Page ################ //
    public function makeInformationPage(){
        $title = "Page d'information devoir";
        $content = "Detail tech";
        $content .= "Login / Password : ";

        $this->setPart('title', $title);
        $this->setPart('content', $content);
    }


// ################ Connexion Page ################ //
    public function makeConnexionPage($builder, $currentPage){
        $title = "Page de connexion";

        $data = $builder->getData();

        $loginRef = $builder->getLoginRef();
        $passwordRef = $builder->getPasswordRef();

        $errLogin = $builder->getErrors($loginRef);
        $errPassword = $builder->getErrors($passwordRef);

        $content = '<section class="connexionPageSection">';

        $content .= '<form class="box" action="index.php?obj=pdf&action=connexion&id='.$currentPage.'" method="POST">';
        $content .= '<input type="text" name="'.$loginRef.'" placeholder="Login" value="'.self::htmlesc($data[$loginRef]).'">';
        if($errLogin !== null){
            $content .= '<span class="errors">'.$errLogin.'</span>';
        }
        $content .= '<input type="password" name="'.$passwordRef.'" placeholder="Password" value="'.self::htmlesc($data[$passwordRef]).'">';
        if($errPassword !== null){
            $content .= '<span class="errors">'.$errPassword.'</span>';
        }
        $content .= '<button type="submit">Se connecter</button>';
        $content .= '</form>';

        $content .= '</section>';

        $this->setPart('title', $title);
        $this->setPart('content', $content);
    }

    public function makeUserConnectedPage(){
        $title = "Bienvenue " . self::htmlesc($_SESSION['user']['prenom']);

        $content = '<p>'.self::htmlesc($_SESSION['user']['nom']).'</p>';
        $content .= '<p>'.self::htmlesc($_SESSION['user']['prenom']).'</p>';
        $content .= '<p>'.self::htmlesc($_SESSION['user']['statut']).'</p>';

        $content .= '<form action="index.php?obj=pdf&action=deconnexion" method="POST">';
        $content .= '<button type="submit">Deconnexion</button>';
        $content .= '</form>';

        $this->setPart('title', $title);
        $this->setPart('content', $content);
    }



// ################ Display Connexion ################ //
    public function displayConnexionSucces(){
        $this->router->POSTredirect("index.php", "<p class='feedback'>Vous êtes bien connecté en tant que ".$_SESSION['user']['statut']."</p>");
    }

    public function displayConnexionSuccesToCurrentPage($currentPage){
        $this->router->POSTredirect("index.php?obj=pdf&action=".$currentPage, "<p class='feedback'>Vous êtes bien connecté en tant que ".$_SESSION['user']['statut']."</p>");
    }

    public function displayRequireConnexion($action){
        $this->router->POSTredirect("index.php?obj=pdf&action=askConnexion&id=".$action, "<p class='feedback'>Connexion requise pour accèder à cette page</p>");
    }

    public function displayDeconnexionSucces(){
        $this->router->POSTredirect("index.php?obj=pdf&action=askConnexion", "<p class='feedback'>Déconnexion réussi</p>");
    }


// ################ Unknown Page ################ //
    public function unknownPdfPage() {
        $title = "Poème inconnu ou non trouvé";
        $content = "Choisir un poème dans la liste.";

        $this->view->setPart('title', $title);
        $this->view->setPart('content', $content);
    }


// ################ Utilitaire ################ //
    public static function htmlesc($str){
        return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }
}
