<?php
class PDF extends FPDF
{
    function EAN13($x, $y, $barcode, $h=16, $w=.60) {
        $this->Barcode($x,$y,$barcode,$h,$w,13);
    }
    
    function Barcode($x, $y, $barcode, $h, $w, $len) {
        //Padding
        $barcode=str_pad($barcode,$len-1,'0',STR_PAD_LEFT);
        //Convert digits to bars
        $codes=array(
            'A'=>array(
                '0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
                '5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
            'B'=>array(
                '0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
                '5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
            'C'=>array(
                '0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
                '5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
            );
        $parities=array(
            '0'=>array('A','A','A','A','A','A'),
            '1'=>array('A','A','B','A','B','B'),
            '2'=>array('A','A','B','B','A','B'),
            '3'=>array('A','A','B','B','B','A'),
            '4'=>array('A','B','A','A','B','B'),
            '5'=>array('A','B','B','A','A','B'),
            '6'=>array('A','B','B','B','A','A'),
            '7'=>array('A','B','A','B','A','B'),
            '8'=>array('A','B','A','B','B','A'),
            '9'=>array('A','B','B','A','B','A')
            );
        $code='101';
        $p=$parities[$barcode[0]];
        for($i=1;$i<=6;$i++)
            $code.=$codes[$p[$i-1]][$barcode[$i]];
        $code.='01010';
        for($i=7;$i<=12;$i++)
            $code.=$codes['C'][$barcode[$i]];
        $code.='101';
        //Draw bars
        for($i=0;$i<strlen($code);$i++)
        {
            if($code[$i]=='1')
                $this->Rect($x+$i*$w,$y,$w,$h,'F');
        }
        //Print text uder barcode
        $this->SetFont('Arial','',12);
        $this->Text($x,$y+$h+11/$this->k,substr($barcode,-$len));
    }
    
    function setTailleText($New_Indice1){
        $this->TailleText = $New_Indice1;
    }
    function setWidth($New_Indice2){
        $this->Width = $New_Indice2;
    }
    function setTypo($New_Indice3){
        $this->Typo = $New_Indice3;
    }
    function setColorText($New_Indice4){
        $this->ColorText = $New_Indice4;
    }
    function setColorText2($New_Indice5){
        $this->ColorText2 = $New_Indice5;
    }
    function setColorFont1($New_Indice6){
        $this->ColorFont1 = $New_Indice6;
    }
    function setColorFont2($New_Indice7){
        $this->ColorFont2 = $New_Indice7;
    }
    function setColorFont3($New_Indice8){
        $this->ColorFont3 = $New_Indice8;
    }
    function setClient($New_Indice9){
        $this->Client = $New_Indice9;
    }
    function setSociete($New_Indice10){
        $this->Societe = $New_Indice10;
    }
    function setValidite($New_Indice11){
        $this->Validite = $New_Indice11;
    }
    function setGencode($New_Indice12){
        $this->Gencode = $New_Indice12;
    }
    function setCadeau($New_Indice13){
        $this->Cadeau = $New_Indice13;
    }
    function setMessage($New_Indice15){
        $this->Message = $New_Indice15;
    }
    function setSocieteNom($New_Indice16){
        $this->SocieteNom = $New_Indice16;
    }
    function setGencodeClient($New_Indice16){
        $this->GencodeClient = $New_Indice16;
    }
    function setEAN13($New_Indice16){
        $this->EAN13 = $New_Indice16;
    }

    // En-tête
    function Header()
    {
        $this->SetTextColor($this->ColorText1);
        $this->SetFont( $this->Typo, "", $this->TailleText);
        $this->SetFillColor($this->ColorFont1, $this->ColorFont2, $this->ColorFont3);
        
        $this->SetXY( 10, 40);
        $this->MultiCell(95, 4, $this->Client, "0", "L", false);
        
        $this->SetXY( 120, 60);
        $this->MultiCell(95, 4, $this->Societe, "0", "L", false);
        
        $this->SetXY( 10, 100);
        $this->SetTextColor(0, 204, 0);
        $this->SetFont( $this->Typo, "B", 27);
        $this->Cell(200, $this->Width, "Félicitations !", 0, 0, "L", false);
        $this->Ln(20);
        $this->SetTextColor($this->ColorText1);
        $this->SetFont( $this->Typo, "", $this->TailleText);
        $this->MultiCell(200, $this->Width, "Nous vous remercions de votre fidélité et nous sommes heureux de vous faire parvenir aujourd'hui", 0, "L", false);
        $this->Ln();
        $this->SetTextColor(0, 204, 0);
        $this->MultiCell(200, $this->Width, "Votre chèque de fidélité de ".$this->Cadeau." Euro valable jusqu'au ".$this->Validite, 0, "L", false);
        $this->Ln();
        $this->SetTextColor($this->ColorText1);
        $this->MultiCell(200, $this->Width, "Pensez à présenter votre carte de fidélité à chacune de vos visites, pour cumuler des points sur vos achats et recevoir ainsi un nouveau chèque de fidélité !", 0, "L", false);
        $this->Ln(10);
        $this->SetTextColor(0, 204, 0);
        $this->SetFont( $this->Typo, "B", 14);
        $this->Cell(200, $this->Width, "Venez découvrir nos nouveautés", 0, 0, "C", false);
        $this->Ln(20);
        $this->SetTextColor($this->ColorText1);
        $this->SetFont( $this->Typo, "", $this->TailleText);
        $this->Cell(190, $this->Width, "Nous espérons vous revoir très bientôt", 0, 0, "R", false);
        $this->Ln();
        $this->Cell(190, $this->Width, "Cordialement ".$this->SocieteNom, 0, 0, "R", false);
        
        $this->Line(10, 210, 200, 210);
        
        $this->SetXY( 10, 220);
        $this->SetTextColor(0, 204, 0);
        $this->SetFont( $this->Typo, "B", 14);
        $this->Cell(190, $this->Width, "Chèque de fidélité ", 0, 0, "L", false);
        $this->Ln(10);
               
        $this->SetX(80);
        $this->SetTextColor($this->ColorText1);
        $this->SetFont( $this->Typo, "B", $this->TailleText);
        $this->MultiCell(70, $this->Width, "Bénéficiaire", 0, "L", false);
        $this->SetX(80);
        $this->SetFont( $this->Typo, "", $this->TailleText);
        $this->MultiCell(70, $this->Width, $this->Client, 0, "L", false);
        $this->Ln();
        $this->SetFont( $this->Typo, "B", $this->TailleText);
        $this->Cell(70, $this->Width, "Numéro de carte", 0, 0, "L", false);
        $this->Ln();
        $this->SetFont( $this->Typo, "", $this->TailleText);
        $this->Cell(70, $this->Width, $this->GencodeClient, 0, 0, "L", false);
        $this->Ln();
    }
}
?>