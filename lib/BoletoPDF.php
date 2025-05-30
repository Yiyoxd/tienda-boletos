<?php
require_once 'fpdf.php';

class GeneradorBoletoPDF
{
    private $pdf;

    /* ============  PARÁMETROS GENERALES  ============ */
    const ANCHO  = 80;     // mm
    const ALTO   = 130;    // mm (puedes aumentar si quieres)
    const MARGIN = 5;      // mm
    const COLOR_PRIMARIO   = [0, 48, 105];   // azul corporativo
    const GRIS_SUAVE       = [240,240,240];

    public function __construct()
    {
        $this->pdf = new FPDF('P', 'mm', [self::ANCHO, self::ALTO]);
        $this->pdf->SetMargins(self::MARGIN, self::MARGIN, self::MARGIN);
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->AddPage();
    }

    /* ============  MÉTODO PÚBLICO  ============ */
    public function generar(array $d): FPDF
    {
        $this->encabezado($d);
        $this->lineaPunteada();
        $this->bloquePelicula($d);
        $this->lineaPunteada();
        $this->bloqueTotal($d['total']);
        $this->lineaPunteada();
        $this->bloqueFacturacion($d);
        $this->pie();

        return $this->pdf;
    }

    /* ============  BLOQUES  ============ */

    private function encabezado(array $d)
    {
        [$r,$g,$b] = self::COLOR_PRIMARIO;
        $this->pdf->SetFillColor($r,$g,$b);
        $this->pdf->Rect(0,0,self::ANCHO,15,'F');

        $this->pdf->SetTextColor(255);
        $this->pdf->SetFont('Arial','B',13);
        $this->pdf->SetXY(0,4);
        $this->pdf->Cell(self::ANCHO,6, $this->t('¡GRACIAS POR TU COMPRA!'), 0, 2,'C');

        // Si quieres logo:
        // $this->pdf->Image('logo.png', self::ANCHO-18,2,15);

        $this->pdf->SetTextColor(0);  // restaurar
        $this->pdf->Ln(3);
    }

    private function bloquePelicula(array $d)
    {
        extract($d); // $pelicula, $imagenPelicula, $cineNombre, etc.

        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(0,5,$this->t('PELÍCULA'),0,1,'C');

        /* Imagen cartel */
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();
        if (is_file($imagenPelicula)) {
            $this->pdf->Image($imagenPelicula, $x, $y, 22, 0);   // mantiene proporción
        }

        /* Detalles a la derecha de la imagen */
        $this->pdf->SetXY($x+24,$y);
        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->MultiCell( self::ANCHO - 24 - self::MARGIN,4, $this->t($pelicula) );

        $this->pdf->SetFont('Arial','',8);
        $this->pdf->SetXY($x+24,$this->pdf->GetY()+1);
        $this->pdf->MultiCell(0,4,$this->t("$cineNombre\n$fecha $hora"));

        /* Asientos, sala, cantidad */
        $this->pdf->Ln(1);
        $this->parLabelDato('Boletos',   $cantidad);
        $this->parLabelDato('Asientos',  $asientos);
        $this->parLabelDato('Sala',      $sala);
    }

    private function bloqueTotal(float $total)
    {
        $this->pdf->SetFillColor(...self::GRIS_SUAVE);
        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->Cell(0,7,$this->t('TOTAL (IVA incl.)'),0,1,'L',true);

        $this->pdf->SetFont('Arial','B',12);
        $this->pdf->Cell(0,9,sprintf('$ %.2f',$total),0,1,'R',true);
    }

    private function bloqueFacturacion(array $d)
    {
        extract($d); // $cineID, $transaccion, $metodoPago

        $this->pdf->SetFont('Arial','B',9);
        $this->pdf->Cell(0,6,$this->t('DATOS PARA FACTURAR'),0,1);

        $this->pdf->SetFont('Arial','',8);
        $this->parLabelDato('Cine',          $cineID);
        $this->parLabelDato('Transacción',   $transaccion);
        $this->parLabelDato('Mét. de pago',  $metodoPago);
    }

    private function pie()
    {
        $this->pdf->SetY(-28);
        $this->pdf->SetFont('Arial','I',7);
        $mensaje = "Presenta este boleto digital o impreso al ingresar.\n" .
                   "No se aceptan cambios ni devoluciones una vez iniciada la función.";
        $this->pdf->MultiCell(0,3.5,$this->t($mensaje),'','C');
    }

    /* ============  UTILIDADES  ============ */

    // Texto -> Latin-1 (FPDF no maneja UTF-8)
    private function t($txt){ return utf8_decode($txt); }

    // Línea punteada usando pequeñas líneas
    private function lineaPunteada()
    {
        $y = $this->pdf->GetY();
        for($x=self::MARGIN; $x<self::ANCHO-self::MARGIN; $x+=2){
            $this->pdf->Line($x,$y,$x+1,$y);
        }
        $this->pdf->Ln(3);
    }

    // Par etiqueta : dato alineado
    private function parLabelDato(string $label, string $dato)
    {
        $this->pdf->SetFont('Arial','',8);
        $this->pdf->Cell(25,4,$this->t("$label:"),0,0,'L');
        $this->pdf->Cell(0 ,4,$this->t($dato),0,1,'L');
    }
}

/* ====================  DEMO ==================== */
/*
$datos = [
    'pelicula'       => 'Interestelar',
    'imagenPelicula' => 'interstellar.jpg',
    'cineNombre'     => 'Cinetix Plaza Central',
    'fecha'          => '2024-05-29',
    'hora'           => '19:30',
    'cantidad'       => 2,
    'asientos'       => 'E10, E11',
    'sala'           => 'Sala 3 XD',
    'total'          => 212.00,
    'cineID'         => 'CINETIX001',
    'transaccion'    => 'TX1234567',
    'metodoPago'     => 'Tarjeta terminación 5274'
];

$pdf = (new BoletoPDF)->generar($datos);
$pdf->Output(); // Envío al navegador
*/