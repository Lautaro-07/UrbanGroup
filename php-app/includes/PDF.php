<?php
require_once __DIR__ . '/../fpdf/fpdf.php';

class PDF_UrbanGroup extends FPDF
{
    private $primary = [30, 30, 30];      // Negro suave
    private $accent = [0, 120, 215];      // Azul profesional
    private $lightGray = [245, 245, 245]; // Fondo suave

    private function encodeText($text)
    {
        if (!is_string($text)) return '';
        // Convertir UTF-8 → ISO-8859-1 SOLO UNA VEZ
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);
    }

    /* ---------------- HEADER ---------------- */
    function Header()
    {
        // Fondo
        $this->SetFillColor(250, 250, 250);
        $this->Rect(0, 0, 210, 30, 'F');

        // Línea superior azul
        $this->SetFillColor(0, 120, 215);
        $this->Rect(0, 0, 210, 2, 'F');

        // Título principal
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(0, 120, 215);
        $this->SetXY(10, 8);
        $this->Cell(0, 8, $this->encodeText("URBAN GROUP"), 0, 0, 'L');

        // Subtítulo
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(120, 120, 120);
        $this->SetXY(10, 16);
        $this->Cell(0, 5, $this->encodeText("Portal Inmobiliario Profesional"), 0, 0, 'L');

        $this->SetY(32);
    }

    /* ---------------- FOOTER ---------------- */
    function Footer()
    {
        // Línea superior
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.3);
        $this->Line(10, 278, 200, 278);

        // Texto izquierda
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(150, 150, 150);
        $this->SetXY(10, 280);
        $this->Cell(100, 4, $this->encodeText("Urban Group © " . date("Y") . " - Catálogo de Propiedades"), 0, 0, 'L');

        // Página
        $this->SetXY(160, 280);
        $this->Cell(40, 4, "Página " . $this->PageNo(), 0, 0, 'R');
    }

    /* ---------------- TÍTULO DE SECCIÓN ---------------- */
    function SectionTitle($text)
    {
        $this->Ln(4);

        // Fondo
        $this->SetFillColor(240, 248, 255);
        $this->Rect(10, $this->GetY(), 190, 8, 'F');

        // Barra azul izquierda
        $this->SetFillColor(0, 120, 215);
        $this->Rect(10, $this->GetY(), 3, 8, 'F');

        // Texto
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 120, 215);
        $this->SetXY(14, $this->GetY());
        $this->Cell(0, 8, $this->encodeText($text), 0, 1);

        $this->Ln(2);
    }

    /* ---------------- FILA CON ETIQUETA Y VALOR ---------------- */
    function InfoRow($label, $value)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(70, 6, $this->encodeText($label . ":"), 0, 0);

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(40, 40, 40);
        $this->MultiCell(0, 6, $this->encodeText($value), 0, 'L');
    }

    /* ---------------- FILA CON 2 COLUMNAS ---------------- */
    function TwoColumnRow($label1, $value1, $label2, $value2)
    {
        $x = $this->GetX();
        $y = $this->GetY();

        // Columna 1 Label
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(80, 80, 80);
        $this->SetXY($x, $y);
        $this->Cell(40, 6, $this->encodeText($label1 . ":"), 0, 0);

        // Columna 1 Valor
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(40, 40, 40);
        $this->SetXY($x + 50, $y);
        $this->Cell(40, 6, $this->encodeText($value1), 0, 0);

        // Columna 2 Label
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(80, 80, 80);
        $this->SetXY($x + 100, $y);
        $this->Cell(40, 6, $this->encodeText($label2 . ":"), 0, 0);

        // Columna 2 Valor
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(40, 40, 40);
        $this->SetXY($x + 150, $y);
        $this->Cell(0, 6, $this->encodeText($value2), 0, 1);

        $this->Ln(2);
    }

    /* ---------------- LISTA DE FEATURES ---------------- */
    function FeaturesList($features)
    {
        if (!$features || !is_array($features)) return;

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(50, 50, 50);

        foreach ($features as $feature) {
            $this->SetXY(15, $this->GetY());
            $this->Cell(5, 6, "*", 0, 0);
            $this->SetXY(20, $this->GetY());
            $this->MultiCell(180, 6, $this->encodeText($feature), 0, 'L');
        }

        $this->Ln(2);
    }
}
