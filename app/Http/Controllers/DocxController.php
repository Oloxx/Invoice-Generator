<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;


class DocxController extends Controller
{
    /**
     * Generate the docx file.
     */
    public function generate(Request $request)
    {
        // Crear un nuevo objeto PhpWord
        $phpWord = new PhpWord();
        
        // Crear una sección
        $section = $phpWord->addSection();
        // Agregar el contenido al documento Word
        $section->addText($request->fecha);
        $section->addText($request->titulo);
        foreach ($request->lineas as $key => $value) {
            $section->addText($value['descripcion']);
            $section->addText($value['cantidad']);
            $section->addText($value['preciounit']);
            $section->addText($value['total']);
        }
        $section->addText($request->subtotal);
        $section->addText($request->iva);
        $section->addText($request->total);
        

        // Guardar el documento Word
        $filename = 'archivo.docx';
        $phpWord->save($filename);

        // Descargar el archivo
        return response()->download($filename);
    }
}
