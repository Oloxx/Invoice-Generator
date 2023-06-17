<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;


class DocxController extends Controller
{
    /**
     * Generate the docx file.
     */
    public function generate(Request $request)
    {
        // Cargar el archivo de plantilla
        $templateFile = storage_path('app/template.docx');
        $templateProcessor = new TemplateProcessor($templateFile);


        // Procesar los valores de "lineas"
        foreach ($request->cars as $ckey => $car) {
            $templateProcessor->setValue("fecha{$ckey}", date_format(date_create($car['fecha']), 'd/m/y'));
            $templateProcessor->setValue("titulo{$ckey}", $car['titulo']);
            foreach ($car['lineas'] as $lkey => $linea) {
                $templateProcessor->setValue("descripcion{$ckey}{$lkey}", $linea['descripcion']);
                $templateProcessor->setValue("cantidad{$ckey}{$lkey}", '. Cantidad: ' . $linea['cantidad']);
                $templateProcessor->setValue("preciounit{$ckey}{$lkey}", $linea['preciounit']);
                $templateProcessor->setValue("total{$ckey}{$lkey}", number_format(floatval($linea['total']), (floatval($linea['total']) != floor(floatval($linea['total']))) ? 2 : 0, ',', '.'));
            }
        }

        for ($i = 0; $i < 4; $i++) {
            $templateProcessor->setValue("fecha{$i}", '');
            $templateProcessor->setValue("titulo{$i}", '');
            for ($j = 0; $j < 3; $j++) {
                $templateProcessor->setValue("descripcion{$i}{$j}", '');
                $templateProcessor->setValue("cantidad{$i}{$j}", '');
                $templateProcessor->setValue("preciounit{$i}{$j}", '');
                $templateProcessor->setValue("total{$i}{$j}", '');
            }
        }

        $templateProcessor->setValue('subtotal', number_format(floatval($request->subtotal), (floatval($request->subtotal) != floor(floatval($request->subtotal))) ? 2 : 0, ',', '.'));
        $templateProcessor->setValue('iva', number_format(floatval($request->iva), (floatval($request->iva) != floor(floatval($request->iva))) ? 2 : 0, ',', '.'));
        $templateProcessor->setValue('total', number_format(floatval($request->total), (floatval($request->total) != floor(floatval($request->total))) ? 2 : 0, ',', '.'));

        // Guardar el documento modificado
        $outputFile = storage_path('app/factura.docx');
        $templateProcessor->saveAs($outputFile);
    }

    public function download()
    {
        $file = storage_path('app/factura.docx');

        return response()->download($file);
    }
}
