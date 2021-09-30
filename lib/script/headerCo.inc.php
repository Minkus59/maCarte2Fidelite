<header>
<div id="Center">
<div id="logo1">  
<?php
$ParamFideliteLogo=$cnx->prepare("SELECT * FROM ".$Prefix."Fidelite_Param WHERE client=:client");    
$ParamFideliteLogo->bindParam(':client', $SessionCompteClient, PDO::PARAM_STR);
$ParamFideliteLogo->execute(); 
$ParamLogo=$ParamFideliteLogo->fetch(PDO::FETCH_OBJ);
?>

<?php if ($Cnx_CompteClient==false) { 
    echo "<a href='".$Home."/DashBoard'><img src='".$Home."/lib/logo/logoType.png'/></a></p>";
}
else {
    echo "<a href='".$Home."/DashBoard'><img src='".$ParamLogo->logo."'/></a></p>";
}
?>
</div>

<nav>
<div id='cssmenu'>
<ul>
    <a href="<?php echo $Home; ?>/DashBoard/Encaissement/"><li <?php if ($PageActu==$Home."/DashBoard/Encaissement/") { echo "class='UpBleu'"; } else { echo "class='Bleu'"; } ?>>Encaissement</li></a>
    
    
    <li <?php if (($PageActu==$Home."/DashBoard/Fidelite/")||($PageActu==$Home."/DashBoard/Fidelite/Nouveau/")||($PageActu==$Home."/DashBoard/Fidelite/Historique/")||($PageActu==$Home."/DashBoard/Fidelite/Gestion/")||($PageActu==$Home."/DashBoard/Fidelite/Mailing/")) { echo "class='UpRose'"; } else { echo "class='Rose'"; } ?>>Fidelité
    <ul>
        <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Historique/"><li <?php if ($PageActu==$Home."/DashBoard/Fidelite/Historique/") { echo "class='UpRose'"; } else { echo "class='Rose'"; } ?>>Historique</li></a>
        <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Nouveau/"><li <?php if ($PageActu==$Home."/DashBoard/Fidelite/Nouveau/") { echo "class='UpRose'"; } else { echo "class='Rose'"; } ?>>Nouveau client</li></a>
        <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Gestion/"><li <?php if ($PageActu==$Home."/DashBoard/Fidelite/Gestion/") { echo "class='UpRose'"; } else { echo "class='Rose'"; } ?>>Gestion des clients</li></a>
        <a href="<?php echo $Home; ?>/DashBoard/Fidelite/Mailing/"><li <?php if ($PageActu==$Home."/DashBoard/Fidelite/Mailing/") { echo "class='UpRose'"; } else { echo "class='Rose'"; } ?>>Mailing</li></a>
    </ul></li>
    
    
    <li <?php if (($PageActu==$Home."/DashBoard/Stock/")||($PageActu==$Home."/DashBoard/Stock/Nouveau/")||($PageActu==$Home."/DashBoard/Stock/Categorie/")||($PageActu==$Home."/DashBoard/Stock/Gestion/")) { echo "class='UpRouge'"; } else { echo "class='Rouge'"; } ?>>Stock
    <ul>
    <a href="<?php echo $Home; ?>/DashBoard/Stock/Nouveau/"><li <?php if ($PageActu==$Home."/DashBoard/Stock/Nouveau/") { echo "class='UpRouge'"; } else { echo "class='Rouge'"; } ?>>Nouveau produit</li></a>
    <a href="<?php echo $Home; ?>/DashBoard/Stock/Gestion/"><li <?php if ($PageActu==$Home."/DashBoard/Stock/Gestion/") { echo "class='UpRouge'"; } else { echo "class='Rouge'"; } ?>>Gestion des stocks</li></a>
    <a href="<?php echo $Home; ?>/DashBoard/Stock/Categorie/"><li <?php if ($PageActu==$Home."/DashBoard/Stock/Categorie/") { echo "class='UpRouge'"; } else { echo "class='Rouge'"; } ?>>Catégorie</li></a>
    </ul></li>
   
    
    <li <?php if (($PageActu==$Home."/DashBoard/Compte/Informations/")||($PageActu==$Home."/DashBoard/Compte/Parametre/Point/")||($PageActu==$Home."/DashBoard/Compte/Parametre/Cheque/")||($PageActu==$Home."/DashBoard/Compte/Parametre/Email/")||($PageActu==$Home."/DashBoard/Compte/Parametre/Logo/")||($PageActu==$Home."/DashBoard/Compte/Parametre/Mailing/")||($PageActu==$Home."/DashBoard/Compte/Parametre/Mode/")||($PageActu==$Home."/DashBoard/Compte/Boutique/")||($PageActu==$Home."/DashBoard/Compte/Informations/")||($PageActu==$Home."/DashBoard/Compte/Multi-boutique/")) { echo "class='UpOrange'"; } else { echo "class='Orange'"; } ?>>Mon compte
    <ul>
        <a href="<?php echo $Home; ?>/DashBoard/Compte/Informations/"><li <?php if ($PageActu==$Home."/DashBoard/Compte/Informations/") { echo "class='UpOrange'"; } else { echo "class='Orange'"; } ?>>Informations personnelles</li></a>
        <a href="<?php echo $Home; ?>/DashBoard/Compte/Parametre/Point/"><li <?php if ($PageActu==$Home."/DashBoard/Compte/Parametre/Point/") { echo "class='UpOrange'"; } else { echo "class='Orange'"; } ?>>Paramètre</li></a>
        <a href="<?php echo $Home; ?>/DashBoard/Compte/Boutique/"><li <?php if ($PageActu==$Home."/DashBoard/Compte/Boutique/") { echo "class='UpOrange'"; } else { echo "class='Orange'"; } ?>>Boutique</li></a>
        <a href="<?php echo $Home; ?>/DashBoard/Compte/Multi-boutique/Boutique/"><li <?php if ($PageActu==$Home."/DashBoard/Compte/Multi-boutique/") { echo "class='UpOrange'"; } else { echo "class='Orange'"; } ?>>Multi-boutique</li></a>
    </ul></li>
    
    <a href="<?php echo $Home; ?>/DashBoard/Assistance/"><li <?php if ($PageActu==$Home."/DashBoard/Assistance/") { echo "class='UpJaune'"; } else { echo "class='Jaune'"; } ?>>Assistance</li></a>
</ul>
</div>
</nav>

<div id="logo2">  
<a href="<?php echo $Home; ?>"><img src="<?php echo $Home; ?>/lib/img/logo.png"></a>
</div>

</div>
</header>