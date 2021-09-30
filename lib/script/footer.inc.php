<footer>
    <div id="Center">
        <div id="Cadre1">  
            <a href='<?php echo $Home; ?>'><img src='<?php echo $ParamLogoFooter->logo; ?>'/></a><BR /><BR />
        <ul>
        </ul>
        </div>
    
        <div id="Cadre2"> 
            <H3>Nos Services</H3><BR />
        <ul>
            <a href="<?php echo $Home; ?>"><li <?php if ($PageActu==$Home."/") { echo "class='Up'"; } ?>>Accueil</li></a>
            <?php
            while ($PageFooter=$SelectPageActifFooter->fetch(PDO::FETCH_OBJ)) {
            ?>
                <a href="<?php echo $Home.$PageFooter->lien ?>"><li <?php if ($PageActu==$Home.$PageFooter->lien) { echo "class='Up'"; } ?>><?php echo $PageFooter->libele ?>

                <?php 
                $SelectSousMenuFooter=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' ORDER BY position ASC");
                $SelectSousMenuFooter->bindParam(':parrin', $PageFooter->lien, PDO::PARAM_STR);
                $SelectSousMenuFooter->execute();
                $CountSousMenu=$SelectSousMenuFooter->rowCount();

                if ($CountSousMenu>0) {
                    echo "<ul>";
                    while ($SousMenuFooter=$SelectSousMenuFooter->fetch(PDO::FETCH_OBJ)) { ?>
                        <a href="<?php echo $Home.$SousMenuFooter->lien ?>"><li <?php if ($PageActu==$Home.$SousMenuFooter->lien) { echo "class='Up'"; } ?>><?php echo $SousMenuFooter->libele ?></li></a>
                    <?php 
                    }
                    echo "</ul>";
                }
            } ?>
            <a href="<?php echo $Home; ?>/Mentions-legales/"><li <?php if ($PageActu==$Home."/Mentions-legales/") { echo "class='Up'"; } ?>>Mentions-légales</li></a>
            <a href="<?php echo $Home; ?>/Contact/"><li <?php if ($PageActu==$Home."/Contact/") { echo "class='Up'"; } ?>>Contact</li></a>
        </ul>
        </div>

        <div id="Cadre3">  
            <H3>Informations</H3> <BR />
        <ul>
        <li><a href="http://www.3donweb.fr" target="_Blank">Développeur</a></li>
        </ul>
        </div>
    </div>
</footer>