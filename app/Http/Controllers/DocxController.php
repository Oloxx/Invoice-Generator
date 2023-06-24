<?php

namespace App\Http\Controllers;

use DateTime;
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

        // import clientes.json
        $jsonPath = resource_path('json/clientes.json');
        $jsonData = file_get_contents($jsonPath);
        $clientes = json_decode($jsonData, true);

        // fecha
        $fechaObjeto = DateTime::createFromFormat('d/m/Y', date_format(date_create($request->fecha), 'd/m/Y'));
        $dia = $fechaObjeto->format('d');
        $meses = [
            'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO',
            'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'
        ];
        $mes = $meses[(int)$fechaObjeto->format('m') - 1];
        $año = $fechaObjeto->format('Y');
        $fecha = $dia . ' DE ' . $mes . ' DEL ' . $año;

        $templateProcessor->setValue("fecha", $fecha);
        $templateProcessor->setValue("nfactura", $request->nfactura);
        $templateProcessor->setValue("cliente", $clientes[$request->cliente]);

        // Procesar los valores de "lineas"
        foreach ($request->cars as $ckey => $car) {
            if ($car['fecha']) {
                $templateProcessor->setValue("fecha{$ckey}", date_format(date_create($car['fecha']), 'd/m/y'));
            }
            $templateProcessor->setValue("titulo{$ckey}", $car['titulo']);
            foreach ($car['lineas'] as $lkey => $linea) {
                $templateProcessor->setValue("descripcion{$ckey}{$lkey}", $linea['descripcion']);
                $templateProcessor->setValue("preciounit{$ckey}{$lkey}", $linea['preciounit']);
                $templateProcessor->setValue("total{$ckey}{$lkey}", number_format(floatval($linea['total']), (floatval($linea['total']) != floor(floatval($linea['total']))) ? 2 : 0, ',', '.'));
            }
        }

        for ($i = 0; $i < 4; $i++) {
            $templateProcessor->setValue("fecha{$i}", '');
            $templateProcessor->setValue("titulo{$i}", '');
            for ($j = 0; $j < 3; $j++) {
                $templateProcessor->setValue("descripcion{$i}{$j}", '');
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
