<header>
    <div id="Center">
    <div id="Logo">    
        <a href='<?php echo $Home; ?>'><img src="<?php echo $ParamLogoHeader->logo; ?>"/></a>
    </div>
        <nav>
            <ul>
                <a href="<?php echo $Home; ?>"><li <?php if ($PageActu==$Home."/") { echo "class='Up'"; } ?>>Accueil</li></a>
                <?php
                while ($Page=$SelectPageActif->fetch(PDO::FETCH_OBJ)) {
                ?>
                    <a href="<?php echo $Home.$Page->lien ?>"><li <?php if ($PageActu==$Home.$Page->lien) { echo "class='Up'"; } ?>><?php echo $Page->libele ?>

                    <?php 
                    $SelectSousMenu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' ORDER BY position ASC");
                    $SelectSousMenu->bindParam(':parrin', $Page->lien, PDO::PARAM_STR);
                    $SelectSousMenu->execute();
                    $CountSousMenu=$SelectSousMenu->rowCount();

                    if ($CountSousMenu>0) {
                        echo "<ul>";
                        while ($SousMenu=$SelectSousMenu->fetch(PDO::FETCH_OBJ)) { ?>
                            <a href="<?php echo $Home.$SousMenu->lien ?>"><li <?php if ($PageActu==$Home.$SousMenu->lien) { echo "class='Up'"; } ?>><?php echo $SousMenu->libele ?></li></a>
                        <?php 
                        }
                        echo "</ul>";
                    }
                    ?></li></a>
                <?php 
                } ?>
                <a href="<?php echo $Home; ?>/Contact/"><li <?php if ($PageActu==$Home."/Contact/") { echo "class='Up'"; } ?>>Contact</li></a>
                <a href="<?php echo $Home; ?>/DashBoard/"><li>Inscription</li></a>
            </ul>
        </nav>
    </div>
</header>