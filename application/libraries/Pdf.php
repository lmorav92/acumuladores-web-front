<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Librería PDF Simple para CodeIgniter
 * Basada en FPDF - Versión CORREGIDA sin dependencias de fuentes
 */

// Incluir FPDF
if (file_exists(APPPATH . 'libraries/fpdf/fpdf.php')) {
    require_once APPPATH . 'libraries/fpdf/fpdf.php';
} else if (file_exists(APPPATH . 'third_party/fpdf/fpdf.php')) {
    require_once APPPATH . 'third_party/fpdf/fpdf.php';
} else {
    die('ERROR: FPDF no encontrado. Por favor descarga FPDF desde http://www.fpdf.org/ y colócalo en application/libraries/fpdf/');
}

class Pdf extends FPDF {
    
    private $title = 'Reporte';
    private $headerTitle = 'Sistema de Gestion de Turnos';
    
    /**
     * Constructor
     */
    public function __construct($orientation = 'P', $unit = 'mm', $size = 'A4') {
        parent::__construct($orientation, $unit, $size);
        
        // Configuración por defecto
        $this->SetMargins(15, 25, 15);
        $this->SetAutoPageBreak(true, 20);
    }
    
    /**
     * OVERRIDE: Método SetFont para manejar fuentes core sin archivos
     */
    public function SetFont($family, $style='', $size=0) {
        // Mapear fuentes a core fonts
        $family = strtolower($family);
        
        // Convertir Arial/Helvetica a courier (que siempre funciona)
        if ($family == 'arial' || $family == 'helvetica' || $family == '') {
            $family = 'courier';
        }
        
        // Solo usar fuentes core que NO requieren archivos
        $corefonts = array('courier', 'times', 'symbol', 'zapfdingbats');
        
        if (!in_array($family, $corefonts)) {
            $family = 'courier';
        }
        
        // Llamar al método padre
        parent::SetFont($family, $style, $size);
    }
    
    /**
     * Establecer título del documento
     */
    public function setDocumentTitle($title) {
        $this->title = $this->convertText($title);
        parent::SetTitle($title);
        return $this;
    }
    
    /**
     * Establecer título del header
     */
    public function setHeaderTitle($title) {
        $this->headerTitle = $this->convertText($title);
        return $this;
    }
    
    /**
     * Convertir texto para PDF (sin tildes problemáticas)
     */
    private function convertText($text) {
        // Reemplazos de caracteres especiales
        $replacements = array(
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
            'ü' => 'u', 'Ü' => 'U',
            '°' => 'o', 'º' => 'o', 'ª' => 'a'
        );
        
        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
    
    /**
     * Header del PDF
     */
    function Header() {
        $logo = FCPATH . 'ui/assets/images/logo.png';
        if (file_exists($logo)) {
            try {
                // Configuración del logo
                $logoWidth = 30;  // Ancho del logo en mm
                $logoHeight = 0;  // 0 = mantener proporción
                
                // Calcular posición X centrada
                $pageWidth = $this->GetPageWidth();
                $logoX = ($pageWidth - $logoWidth) / 2;
                
                // Insertar logo centrado
                $this->Image($logo, $logoX, 10, $logoWidth, $logoHeight);
                
                // Saltar espacio después del logo
                $this->Ln(25);
            } catch (Exception $e) {
                // Continuar sin logo
                $this->Ln(5);
            }
        } else {
            $this->Ln(5);
        }
        
        // Título
        $this->SetFont('courier', 'B', 16);
        $this->SetTextColor(68, 114, 196);
        $this->Cell(0, 10, $this->headerTitle, 0, 1, 'C');
        
        // Subtítulo
        $this->SetFont('courier', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, $this->title, 0, 1, 'C');
        
        // Línea
        $this->SetDrawColor(68, 114, 196);
        $this->SetLineWidth(0.5);
        $this->Line(15, $this->GetY() + 2, 195, $this->GetY() + 2);
        
        $this->Ln(5);
        $this->SetTextColor(0, 0, 0);
    }
    
    /**
     * Footer del PDF
     */
    function Footer() {
        $this->SetY(-15);
        
        // Línea
        $this->SetDrawColor(68, 114, 196);
        $this->SetLineWidth(0.5);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        
        // Texto del footer
        $this->SetFont('courier', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        
        // Fecha
        $this->Cell(60, 10, 'Generado: ' . date('d/m/Y H:i'), 0, 0, 'L');
        
        // Página
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . ' de {nb}', 0, 0, 'R');
        
        $this->SetTextColor(0, 0, 0);
    }
    
    /**
     * Agregar título de sección
     */
    public function addSectionTitle($title, $fontSize = 14) {
        $this->SetFont('courier', 'B', $fontSize);
        $this->SetTextColor(68, 114, 196);
        $this->Cell(0, 8, $this->convertText($title), 0, 1, 'L');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
        return $this;
    }
    
    /**
     * Crear tabla desde arrays
     */
    public function createTable($headers, $data, $widths = null) {
        // Si no se especifican anchos, distribuir equitativamente
        if ($widths === null) {
            $totalWidth = 180;
            $colWidth = $totalWidth / count($headers);
            $widths = array_fill(0, count($headers), $colWidth);
        }
        
        // Encabezados
        $this->SetFillColor(68, 114, 196);
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(68, 114, 196);
        $this->SetLineWidth(0.3);
        $this->SetFont('courier', 'B', 9);
        
        foreach ($headers as $index => $header) {
            $this->Cell($widths[$index], 7, $this->convertText($header), 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Restaurar colores para datos
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('courier', '', 8);
        
        // Datos
        $fill = false;
        foreach ($data as $row) {
            // Verificar si necesitamos nueva página
            if ($this->GetY() > 250) {
                $this->AddPage();
                
                // Repetir encabezados
                $this->SetFillColor(68, 114, 196);
                $this->SetTextColor(255, 255, 255);
                $this->SetFont('courier', 'B', 9);
                
                foreach ($headers as $index => $header) {
                    $this->Cell($widths[$index], 7, $this->convertText($header), 1, 0, 'C', true);
                }
                $this->Ln();
                
                $this->SetFillColor(240, 240, 240);
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('courier', '', 8);
            }
            
            foreach ($row as $index => $cell) {
                $this->Cell($widths[$index], 6, $this->convertText($cell), 'LR', 0, 'L', $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }
        
        // Línea final
        $this->Cell(array_sum($widths), 0, '', 'T');
        $this->Ln(5);
        
        return $this;
    }
    
    /**
     * Agregar información clave-valor
     */
    public function addInfo($label, $value) {
        $this->SetFont('courier', 'B', 9);
        $this->Cell(40, 5, $this->convertText($label) . ':', 0, 0, 'L');
        $this->SetFont('courier', '', 9);
        $this->Cell(0, 5, $this->convertText($value), 0, 1, 'L');
        return $this;
    }
    
    /**
     * Descargar PDF
     */
    public function download($filename = 'document.pdf') {
        if (!preg_match('/\.pdf$/i', $filename)) {
            $filename .= '.pdf';
        }
        
        $this->AliasNbPages();
        $this->Output('D', $filename);
        exit;
    }
    
    /**
     * Mostrar en navegador
     */
    public function preview($filename = 'document.pdf') {
        $this->AliasNbPages();
        $this->Output('I', $filename);
        exit;
    }
    
    /**
     * Guardar en servidor
     */
    public function save($filepath) {
        $this->AliasNbPages();
        $this->Output('F', $filepath);
        return $this;
    }
}
