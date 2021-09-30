<?php 
$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

session_start();
?>

<section>
<div id="Center">
<article>
<?php 
if (isset($Erreur)) { echo "<font color='#FF0000'>".$Erreur."</font></p>"; } 
if (isset($Valid)) { echo "<font color='#00CC00'>".$Valid."</font></p>"; }
?>

<H1>Contact</H1></p>

<div id="Colone2">

Pour toutes questions commerciales ou techniques </p>

Bureau : <b>03 20 64 54 22</b><BR />

E-mail : <b><?php echo $Destinataire; ?></b> ou via le <b>formulaire ci-dessous</b> </p>

<font color='#FF0000'>Merci de bien vouloir préciser vos coordonnées et votre demande.</font></p> 
<form name="form_contact" id="form_contact" action="<?php echo $Home; ?>/lib/script/contact.php" method="POST">

<input type="text" value="<?php if (isset($_SESSION['nom'])) { echo $_SESSION['nom']; } ?>" name="nom" placeholder="Nom / Prénom*" required="required"><BR />
<input type="text" value="<?php if (isset($_SESSION['tel'])) { echo $_SESSION['tel']; } ?>" name="tel" placeholder="Numero de téléphone*" required="required"/><BR />
<input type="text" value="<?php if (isset($_SESSION['cp'])) { echo $_SESSION['cp']; } ?>" name="cp" placeholder="Code postal*" required="required"/><BR />
<input type="text" value="<?php if (isset($_SESSION['sujet'])) { echo $_SESSION['sujet']; } ?>" name="sujet" placeholder="Sujet*" required="required"/></p>
<textarea cols="40" rows="10" name="message" placeholder="Message ou détailles pour devis*" required="required"><?php if (isset($_SESSION['message'])) { echo $_SESSION['message']; } ?></textarea><BR />
<input type="email" value="<?php if (isset($_SESSION['email'])) { echo $_SESSION['email']; } ?>" name="email" placeholder="Votre adresse e-mail*" required="required"/></p>
<input type="submit" name="Envoyer" value="Envoyer"/>

</form></p>

<font color='#FF0000'>*</font> : Informations requises</p>
</div>

<div id="Colone2">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3169.9370902026567!2d3.1568475159413696!3d50.681904478540915!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c328f934d0a3ed%3A0x343a66bf2369fd2a!2s324+Rue+Jean+Jaur%C3%A8s%2C+59170+Croix!5e1!3m2!1sfr!2sfr!4v1459864299038" width="100%" height="500" frameborder="0" style="border:0"></iframe>
</div>
</article>
</div>
</section>

