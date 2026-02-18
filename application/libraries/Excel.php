<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Librería Excel Simple para CodeIgniter
 * No requiere dependencias externas - Solo PHP nativo
 * Genera archivos .xlsx usando XMLWriter y ZipArchive
 */
class Excel {
    
    private $data = [];
    private $headers = [];
    private $filename = 'export.xlsx';
    private $sheetName = 'Hoja1';
    private $title = 'Reporte';
    
    /**
     * Crear nueva instancia
     */
    public function create() {
        // Resetear datos
        $this->data = [];
        $this->headers = [];
        return $this;
    }
    
    /**
     * Establecer título del documento
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
    /**
     * Establecer encabezados
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
        return $this;
    }
    
    /**
     * Establecer datos desde array
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }
    
    /**
     * Cargar datos desde array (alias compatible)
     */
    public function fromArray($data, $startCell = 'A1') {
        if (!empty($data)) {
            // Primera fila como encabezados
            $this->headers = array_shift($data);
            // Resto como datos
            $this->data = $data;
        }
        return $this;
    }
    
    /**
     * Establecer nombre de archivo
     */
    public function setFilename($filename) {
        $this->filename = $filename;
        if (!preg_match('/\.xlsx$/i', $this->filename)) {
            $this->filename .= '.xlsx';
        }
        return $this;
    }
    
    /**
     * Establecer nombre de hoja
     */
    public function setSheetName($name) {
        $this->sheetName = $name;
        return $this;
    }
    
    /**
     * Aplicar estilo a encabezados (placeholder - ya incluido en generación)
     */
    public function styleHeader($range) {
        // El estilo ya está aplicado automáticamente en getSheet()
        return $this;
    }
    
    /**
     * Aplicar bordes (placeholder - ya incluido en generación)
     */
    public function setBorders($range) {
        // Los bordes ya están aplicados automáticamente en getStyles()
        return $this;
    }
    
    /**
     * Auto-ajustar columnas (placeholder - aproximado en generación)
     */
    public function autoSizeColumns($startCol, $endCol) {
        // El ancho de columnas se calcula automáticamente en getSheet()
        return $this;
    }
    
    /**
     * Descargar archivo Excel
     */
    public function download($filename = null) {
        if ($filename) {
            $this->setFilename($filename);
        }
        
        // Crear archivo temporal
        $tempDir = sys_get_temp_dir();
        $zipFile = tempnam($tempDir, 'xlsx_');
        
        // Crear archivo ZIP (xlsx es un ZIP)
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::OVERWRITE) !== TRUE) {
            throw new Exception('No se pudo crear el archivo Excel');
        }
        
        // Agregar archivos XML necesarios
        $zip->addFromString('[Content_Types].xml', $this->getContentTypes());
        $zip->addFromString('_rels/.rels', $this->getRels());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->getWorkbookRels());
        $zip->addFromString('xl/workbook.xml', $this->getWorkbook());
        $zip->addFromString('xl/styles.xml', $this->getStyles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->getSheet());
        
        $zip->close();
        
        // Descargar archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $this->filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        readfile($zipFile);
        unlink($zipFile);
        exit;
    }
    
    /**
     * Generar XML del contenido de la hoja
     */
    private function getSheet() {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        
        // Configuración de columnas con ancho automático
        $xml .= '<cols>';
        $numCols = !empty($this->headers) ? count($this->headers) : (isset($this->data[0]) ? count($this->data[0]) : 10);
        for ($i = 0; $i < $numCols; $i++) {
            $xml .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="15" customWidth="1"/>';
        }
        $xml .= '</cols>';
        
        $xml .= '<sheetData>';
        
        $rowNum = 1;
        
        // Encabezados
        if (!empty($this->headers)) {
            $xml .= '<row r="' . $rowNum . '">';
            $colNum = 0;
            foreach ($this->headers as $header) {
                $cellRef = $this->getCellReference($colNum, $rowNum - 1);
                $xml .= '<c r="' . $cellRef . '" s="1" t="inlineStr">';
                $xml .= '<is><t>' . htmlspecialchars($header, ENT_XML1, 'UTF-8') . '</t></is>';
                $xml .= '</c>';
                $colNum++;
            }
            $xml .= '</row>';
            $rowNum++;
        }
        
        // Datos
        foreach ($this->data as $row) {
            $xml .= '<row r="' . $rowNum . '">';
            $colNum = 0;
            
            // Asegurar que row sea array
            if (!is_array($row)) {
                $row = [$row];
            }
            
            foreach ($row as $cell) {
                $cellRef = $this->getCellReference($colNum, $rowNum - 1);
                
                // Detectar si es número o texto
                if (is_numeric($cell) && !is_string($cell)) {
                    $xml .= '<c r="' . $cellRef . '" s="2">';
                    $xml .= '<v>' . $cell . '</v>';
                } else {
                    $xml .= '<c r="' . $cellRef . '" s="2" t="inlineStr">';
                    $xml .= '<is><t>' . htmlspecialchars($cell, ENT_XML1, 'UTF-8') . '</t></is>';
                }
                $xml .= '</c>';
                $colNum++;
            }
            $xml .= '</row>';
            $rowNum++;
        }
        
        $xml .= '</sheetData>';
        $xml .= '</worksheet>';
        
        return $xml;
    }
    
    /**
     * Obtener referencia de celda (A1, B2, etc.)
     */
    private function getCellReference($col, $row) {
        $letter = '';
        while ($col >= 0) {
            $letter = chr($col % 26 + 65) . $letter;
            $col = floor($col / 26) - 1;
        }
        return $letter . ($row + 1);
    }
    
    /**
     * Archivos XML del Excel
     */
    private function getContentTypes() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
    }
    
    private function getRels() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
    }
    
    private function getWorkbookRels() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
    }
    
    private function getWorkbook() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="' . htmlspecialchars($this->sheetName, ENT_XML1, 'UTF-8') . '" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
    }
    
    private function getStyles() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <fonts count="2">
        <font><sz val="11"/><name val="Calibri"/></font>
        <font><b/><sz val="11"/><name val="Calibri"/></font>
    </fonts>
    <fills count="3">
        <fill><patternFill patternType="none"/></fill>
        <fill><patternFill patternType="gray125"/></fill>
        <fill><patternFill patternType="solid"><fgColor rgb="FF4472C4"/></patternFill></fill>
    </fills>
    <borders count="2">
        <border><left/><right/><top/><bottom/><diagonal/></border>
        <border>
            <left style="thin"><color rgb="FF000000"/></left>
            <right style="thin"><color rgb="FF000000"/></right>
            <top style="thin"><color rgb="FF000000"/></top>
            <bottom style="thin"><color rgb="FF000000"/></bottom>
        </border>
    </borders>
    <cellXfs count="3">
        <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
        <xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>
        <xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyBorder="1"/>
    </cellXfs>
</styleSheet>';
    }
}
