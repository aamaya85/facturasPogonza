<?php
  
  namespace App\Controllers;

  use \App\Models\Invoice;
  use \App\Models\Payment;
  use \SimpleORM\Model;
  use \League\Plates\Engine;
  
  class MainController {

    public function __construct(){
      Model::config(parse_ini_file(CONFIG));
    }
    
    public function getIndexData($page = 0){
      
      // Get all invoices
      $query_invoices = Invoice::all()->execute();
      
      // Get all payments
      $query_payments = Payment::all()->execute();

      // Build invoices list
      while($query_invoices->loop()){
        if ($query_invoices->activo){
          $invoice = new Invoice();
          $invoice->setId($query_invoices->id);
          $invoice->setNroFactura($query_invoices->nro_factura);
          $invoice->setFecha($query_invoices->fecha_factura);
          $invoice->setNroCliente($query_invoices->nro_cliente);
          $invoice->setCliente($query_invoices->nom_factura);
          $invoice->setDescripcion($query_invoices->desc_factura);
          $invoice->setLocalidad($query_invoices->localidad);
          $invoice->setNeto($query_invoices->neto_factura);
          $invoice->setIva($query_invoices->iva_factura);
          $invoice->setTotalFactura($query_invoices->total_factura);
          $invoice->setVencimiento($query_invoices->venc_factura);
          $invoice->setTipo($query_invoices->tipo_factura);
          $invoice->setLetra($query_invoices->letra_factura);
          $invoice->setColor($query_invoices->color_factura);
          $invoice->setActivo($query_invoices->activo);
          
          $arr_invoices[$query_invoices->id] = $invoice;

        }

      }

      // Add payments to invoices
      while($query_payments->loop()){
        
        $payment = new Payment();
        $payment->setId($query_payments->id);
        $payment->setIdFactura($query_payments->id_factura);
        $payment->setFechaPago($query_payments->fecha_pago);
        $payment->setMontoPagado($query_payments->monto_pagado);
        $payment->setObservaciones($query_payments->observaciones);

        if (isset($arr_invoices[$query_payments->id_factura])){
          $arr_invoices[$query_payments->id_factura]->setPago($payment->toArray());  
        }
              
      }
     
      // Render template
      $template = new Engine(TEMPLATES_PATH);
      return $template->render('index', [
          "invoices" => $arr_invoices,
          "page" => $page
        ]
      );

    }

    public function getLastInvoice($tipo, $letra, $color){
      try {
        $result = Invoice::all()->where()->andFilter([
          ["tipo_factura", "=", strtoupper($tipo)],
          ["letra_factura", "=", strtoupper($letra)],
          ["color_factura", "=", strtolower($color)]
        ])->order("id", "DESC")->get(1)->execute();
      } catch (Exception $e){
        return "Hubo un error en la consulta";
      }

      return json_encode($result->nro_factura);
    }

    public function saveInvoices(){
      $invoices = json_decode(file_get_contents('php://input'));
      foreach($invoices as $inv){
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
      return "  @@ DATOS REGISTRADOS\n\n";
    }

    public function confirmPayment(){
      $data = input()->all();

      $insert = array(
        "id_factura" => $data['id_factura'],
        "fecha_pago" => $data['fecha_pago'],
        "monto_pagado" => $data['monto_pagado'],
        "observaciones" => addslashes(preg_replace( "/\r|\n/", "<br>", $data['observaciones']))
      );

      try {
        $result = Payment::query()->insert($insert)->execute();  
        if ($result) return true;
      } catch (Exception $e) {
        return false;
      }
    }

    public function deleteInvoice(){
      
      $data = input()->all();
      $id_factura = $data['id_factura'];

      try {
        $result = Invoice::query()->update(array("activo" => 0))->where()->andFilter('id', '=', $id_factura)->execute(); //
        if ($result) return true;
      } catch (Exception $e) {
        return false;
      }
    }

  }

?>
