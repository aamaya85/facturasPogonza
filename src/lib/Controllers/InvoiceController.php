<?php

namespace App\Controllers;

use \App\Models\Invoice;
use \SimpleORM\Model;
use \League\Plates\Engine;

class InvoiceController {

  public function __construct() {
    Model::config(parse_ini_file(CONFIG));
  }

  public function saveInvoices() {
    $invoices = json_decode(file_get_contents('php://input'));
    foreach ($invoices as $inv) {
      $new_invoice = array(
        "nro_factura" => $inv->nro_factura,
        "nro_cliente" => $inv->nro_cliente,
        "cuit_factura" => $inv->cuit_factura,
        "localidad" => $inv->localidad,
        "fecha_factura" => $inv->fecha_factura,
        "nom_factura" => $inv->nom_factura,
        "desc_factura" => $inv->desc_factura,
        "neto_factura" => $inv->neto_factura,
        "iva_factura" => $inv->iva_factura,
        "total_factura" => $inv->total_factura,
        "venc_factura" => $inv->venc_factura,
        "tipo_factura" => $inv->tipo_factura,
        "letra_factura" => $inv->letra_factura,
        "color_factura" => $inv->color_factura
      );
      $result = Invoice::query()->insert($new_invoice)->execute();
    }
    return "Guardado";
  }

  public function confirmPayment() {
    $data = input()->all();
    try {
      $result = Invoice::query()->update($data)->where('id', '=', $data['id'])->execute();
      if ($result) return true;
    } catch (Exception $e) {
      return false;
    }
  }
}
