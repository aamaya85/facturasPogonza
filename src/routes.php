<?php

  use \Pecee\SimpleRouter\SimpleRouter;
  use \App\Controllers\MainController;
    
  SimpleRouter::group(['prefix' => '/facturasApp'], function () {
    SimpleRouter::get('/{page?}', function($page = 0){
      $p = is_numeric($page) ? $page : 0;
      $mainController = new MainController();
      return $mainController->getIndexData($p);
    });
    SimpleRouter::get('/api/lastInvoice/{tipo}/{letra}/{color}', [MainController::class, 'getLastInvoice']);
    SimpleRouter::post('/api/saveInvoices', [MainController::class, 'saveInvoices']);
    SimpleRouter::post('/api/deleteInvoice', [MainController::class, 'deleteInvoice']);
    SimpleRouter::post('/api/confirmPayment', [MainController::class, 'confirmPayment']);
  });
