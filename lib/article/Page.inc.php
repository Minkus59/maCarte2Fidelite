
<section>
<div id="Center">
    
<?php
if ($Count>0) {

    while($Article=$RecupArticle->fetch(PDO::FETCH_OBJ)) { 

        echo '<article>';

        echo $Article->message;
        if ($Cnx_Admin==true) { 
            echo '<a href="'.$Home.'/Admin/Article/Nouveau/?id='.$Article->id.'"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a><a href="'.$Home.'/Admin/Article/supprimer.php?id='.$Article->id.'"><img src="'.$Home.'/Admin/lib/img/supprimer.png"></a>';
        } 
        echo '</article>';
    }
}
else {
        echo '<article><p>
        Aucun article pour le moment !
        </p>';
        
        if ($Cnx_Admin==true) { 
            echo '<a href="'.$Home.'/Admin/Article/Nouveau/"><img src="'.$Home.'/Admin/lib/img/modifier.png"></a>';
        } 
        echo '</article>';
}
?>

</div>
</section>