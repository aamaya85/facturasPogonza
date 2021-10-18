$(document).ready(function() {

  // Esta variable es para iniciar la tabla agrupada colapsada
  var collapsedGroups = {};

  $('form').validate();
  
  // Inicializar tabla
  var table = $('#invoices-table').DataTable({
    order: [[3, 'asc']],
    pageLength: 15,
    rowGroup: {
      // Uses the 'row group' plugin
      dataSrc: 3,
      startRender: function (rows, group) {
          var totalFacturas = 0;
          var totalPagado = 0;

          var collapsed = !!collapsedGroups[group];
         
          rows.nodes().each(function (r) {
            // Total factura:
            totalFacturas = totalFacturas + parseFloat(r.childNodes[5].innerText);
            totalPagado = totalPagado + parseFloat(r.childNodes[6].innerText);
            r.style.display = collapsed ? 'none' : '';
          });    

          // Add category name to the <tr>. NOTE: Hardcoded colspan
          return $('<tr/>')
              .append('<td colspan=4>' + group + ' (' + rows.count() + ')</td>')
              .append('<td align="right">' + totalFacturas.toFixed(2).replace(".", ",") + '</td>')
              .append('<td align="right">' + totalPagado.toFixed(2).replace(".", ",") + '</td>')
              .append('<td colspan=3></td>')
              .attr('data-name', group)
              .toggleClass('collapsed', collapsed);
      }
    }
  });

  // Draw selected page in URL
  table.page(parseInt($("#pageSelected").val())).draw('page');
  
  table.column(3).data().unique().each( function ( d, j ){
    collapsedGroups[d] = !collapsedGroups[d];
  });
  
  table.draw(false);

  // Collapse group
  $('tbody').on('click', 'tr.dtrg-start', function () {
      var name = $(this).data('name');
      collapsedGroups[name] = !collapsedGroups[name];
      table.draw(false);
  });

  // FUNCTIONS
  viewPayments = function(payments){
    console.log(payments);
  }

  setPayment = function(id, cliente, nroFactura){
    console.log(id, cliente, nroFactura)
    $('#formModal').modal('show');

    // Set Labels
    $('#label_client').text(cliente);
    $('#label_factura').text(nroFactura);
    
    // Set date
    const now = new Date(Date.now());
    var date = { 
      day: now.getDate().toString().padStart(2, '0'),
      month: (now.getMonth()+1).toString().padStart(2, '0'),
      year: now.getFullYear().toString()
    }
    
    // Set Values
    $('input[name="id_factura"]').val(id);
    $('input[name="fecha_pago"]').val(`${date.year}-${date.month}-${date.day}`);

  }

  viewPayments = function(nroFactura, jsonString){
    
    const payments = JSON.parse(jsonString);
    var fechaPago;
    
    $('#payments-table>tbody').empty();
    
    $('#paymentsModal').modal('show');
    
    for (pay of payments){

      $('#payments-table>tbody').append(`<tr>`);
      $('#payments-table>tbody').append(`<td>${nroFactura}</td>`);

      var fechaPago = pay.fecha_pago.split("-");
      $('#payments-table>tbody').append(`<td>${fechaPago[2]}-${fechaPago[1]}-${fechaPago[0]}</td>`);
      
      var div = `<div style="float: right; margin-left: 15px;">${pay.monto_pagado.toFixed(2)}</div>`;
      $('#payments-table>tbody').append(`<td>$ ${div}</td>`);
      $('#payments-table>tbody').append(`<td>${pay.observaciones}</td>`);
      $('#payments-table>tbody').append(`</tr>`);

    }

  }

  deleteInvoice = function(idFactura, el){

    if (confirm("Seguro que querés borrar la factura?")) {
      
      const URL = 'http://gorila-testing.tk/facturasApp/api/deleteInvoice';
      
      $.post(URL, { id_factura: idFactura }, function(response){
        if (response == true){
          table
            .row(el.closest('tr'))
            .remove()
            .draw();
        }
      });
    } else return false;

  }

  // Send data
  $('#submitButton').click(function(){

    if ($('form').valid()){
    
      const form = new FormData(document.querySelector('#paymentForm'));
      const values = Object.fromEntries(form.entries());
      
      if (values.fecha_pago === "") {
        alert('Fecha inválida!');
        return; 
      }
      
      const data = {
        id_factura: values.id_factura,
        fecha_pago: values.fecha_pago,
        monto_pagado: parseFloat(values.monto_pagado),
        observaciones: values.observaciones
      }

      const URL = 'http://gorila-testing.tk/facturasApp/api/confirmPayment';
      
      $.post(URL, data, function(response){
        console.log('data: ', data);
        console.log('response: ', response);
        
        if (response == true){
          // Close modal
          $('#closeModalButton').click();
          $('#successModal').modal('show');
          setTimeout(function(){
            window.location.href = '/facturasApp/' + (table.page.info().page).toString();
          }, 2000);
        } else {
          $('#failModal').modal('show');
        }


      });
    
    } else {
      alert('Ingresar datos válidos!');
      return false;
    }

  })


} );
