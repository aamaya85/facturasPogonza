<?php
  
  if ($_SERVER['SERVER_PORT'] == 80) header('location: https://pogonza.tk/facturasApp');
 
?>


<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Facturas</title>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.1.3/css/rowGroup.dataTables.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js" type="text/javascript"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
  <script src="https://cdn.datatables.net/rowgroup/1.1.3/js/dataTables.rowGroup.min.js" type="text/javascript"></script>
  <script src="public/functions.js" type="text/javascript"></script>
</head>
<body>


<div class="container-fluid">
  <div class="p-4">
    <div><input type="hidden" id="pageSelected" value="<?= $page ?>" /></div>
    <table id="invoices-table" class="dataTables_wrapper table table-bordered" style="width:100%">
      <thead class="thead-dark">
        <tr>
          <th></th>
          <th>N°</th>
          <th>Fecha</th>
          <th style="display: none">Cliente</th>
          <th>Descripción</th>
          <th>Total</th>
          <th>Monto pagado</th>
          <th>Pagos realizados</th>
          <th>Registrar pago</th>
        </tr>
      </thead>
      <tbody>
      <?php

        // echo '<pre>';
        // print_r($invoices);
        // echo '<pre>';

        foreach($invoices as $inv){
          echo "<tr>";
          echo "<td><button type='button' class='btn btn-danger btn-sm' onclick='deleteInvoice(" . $inv->getId() . ", this)'><i class='bi bi-trash-fill'></i></button></td>";
          $descTipo = $inv->getTipo() . " " . $inv->getLetra() . " (" . $inv->getColor() .")";
          echo "<td>" . $descTipo . "<br><h5>" . $inv->getNroFactura() . "</h5>" ."</td>";
          echo "<td>" . (date("d-m-Y", strtotime($inv->getFecha()))) . "</td>";
          echo "<td style='display: none'># " . $inv->getCliente() . "</td>";
          echo "<td>" . $inv->getDescripcion() . "</td>";
          $saldo = $inv->getTotalFactura() - $inv->getTotalPaid();
          echo "<td align='right'><b>" . number_format($inv->getTotalFactura(), 2, ',', '') . "</b></td>";
          echo "<td align='right'><b>" . number_format($inv->getTotalPaid(), 2, ',', '') . "</b><p>(" . number_format($saldo, 2, ',', '') . ")</p></td>";

          if ($inv->hasPayments() && $inv->getTipo() === "FACTURA"){
            $viewPaymentsButton = "
              <button type='button' class='btn btn-info'
                onclick='viewPayments(" . $inv->getNroFactura() . ", `" . json_encode($inv->getPagos()) . "`)'>
              <i class='bi bi-receipt'></i>
              </button>";
          } else $viewPaymentsButton = "Sin pagos registrados";
          
          echo "
            <td align='center'>
              $viewPaymentsButton
            </td>";
          echo "
            <td align='center'>
              <button
                type='button'
                class='openModalButton btn btn-success'
                onclick='setPayment(" . $inv->getId() . ", \"" . str_replace("'", "´", $inv->getCliente()) . "\", " . $inv->getNroFactura() . ")'>
                <i class='bi bi-arrow-up-circle'></i>
              </button>
            </td>";
          echo "</tr>";
        }         
      ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODALS -->
<!-- Form -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formModalLabel">Registrar nuevo pago</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="paymentForm">
        <div class="modal-body">
          <div class="form-group">
            <input name="id_factura" type="hidden">
            <p><strong>Factura: </strong><span id="label_factura"></span></p>
            <p><strong>Cliente: </strong><span id="label_client"></span></p>

            <div class="input-group mb-2">
              <span class="input-group-text">Fecha pago:</span>&nbsp;
              <input type="date" id="start" name="fecha_pago" class="form-control">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">$</span>&nbsp;
              <input name="monto_pagado" type="number" class="form-control" aria-label="Monto" required>
            </div>
            <div class="input-group mb-1">
              <textarea class="form-control" placeholder="Observaciones" name="observaciones" rows="4"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="closeModalButton" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="submitButton">Aceptar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Success -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-body">
      <div class="alert alert-success" role="alert">
        <h5 id="success_title" class="alert-heading"></h5>
        <p>El pago se registró exitosamente</p>
        <p style="font-size: 14px">(Aguardá un segundo...)</p>
        <button type="button" class="btn btn-success btn-lg" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Fail -->
<div class="modal fade" id="failModal" tabindex="-1" role="dialog" aria-labelledby="failModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-body">
      <div class="alert alert-danger" role="alert">
        <h5 class="alert-heading"></h5>
        <p>Algo anduvo mal... Intentá nuevamente</p>
        <p>Si el problema persiste llamalo a Agustín</p>
        <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Payments -->
<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" aria-labelledby="paymentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formModalLabel">Listado de pagos registrados</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="payments-container">
          <table style="font-size: 12px" class="table table-stripped" id="payments-table">
            <thead class="thead-light">
              <tr>
                <th>Factura</th>
                <th>Fecha pago</th>
                <th class="text-center">Monto pagado</th>
                <th width="60%">Observaciones</th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="closeModalButton" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>


<style>
  
  .dtrg-group {
    color: #49529B;
    cursor: pointer;
  }

  .dataTables_wrapper {
      font-size: 13px;
  }
  tr {
    border-bottom: 1px solid black;
  }



</style>
</body>
</html>
