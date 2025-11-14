<?php

if(isset($_GET["id_img"])){
    include("db.php");
    $id_pic = $_GET["id_img"];
    $requete_img = $db->prepare("select * from IMAGE_ACTIVITE where ID_IMAGE = :id ;");
    $requete_img->execute(["id"=>$id_pic]);
    $id_img = $requete_img->fetchAll(PDO::FETCH_ASSOC);

    if($id_img){
        try{
                $del_img = $db->prepare("delete from IMAGE_ACTIVITE where ID_IMAGE = :id;");
                $del_img->execute(["id"=>$id_pic]);
                $del_img->closeCursor();
                 header('Location: ' . $_SERVER['HTTP_REFERER']);
        }catch(Exception $e){
            echo $e->getMessage();
        }

    
    
    }
}

?>