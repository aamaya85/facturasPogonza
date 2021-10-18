<?php
  
  namespace App\Models;

  use \SimpleORM\Model;

  class Payment extends Model {

    protected $id;
    protected $id_factura;
    protected $nro_factura;
    protected $fecha_pago;
    protected $monto_pagado;
    protected $observaciones;

    //Select the table which the model references
    public static function getTableName(){
      return 'pagos';
    }

    public function __construct(\mysqli $connection = null){
      return $this;
    }

    public function toArray(){
      return array(
        'id' => $this->getId(),
        'id_factura' => $this->getIdFactura(),
        'nro_factura' => $this->getNroFactura(),
        'fecha_pago' => $this->getFechaPago(),
        'monto_pagado' => $this->getMontoPagado(),
        'observaciones' => $this->getObservaciones()
      );
    }

    // GETTERS

    public function getId(){
      return $this->id;
    }
    
    public function getIdFactura(){
      return $this->id_factura;
    }

    public function getNroFactura(){
      return $this->nro_factura;
    }
    
    public function getFechaPago(){
      return $this->fecha_pago;
    }
    
    public function getMontoPagado(){
      return $this->monto_pagado;
    }
    
    public function getObservaciones(){
      return $this->observaciones;
    }

    // SETTERS

    public function setId($id){
      $this->id = $id;
    }
    
    public function setIdFactura($idFactura){
      $this->id_factura = $idFactura;
    }

    public function setNroFactura($nroFactura){
      $this->nro_factura = $nroFactura;
    }
    
    public function setFechaPago($fechaPago){
      $this->fecha_pago = $fechaPago;
    }
    
    public function setMontoPagado($montoPagado){
      $this->monto_pagado = $montoPagado;
    }
    
    public function setObservaciones($observaciones){
      $this->observaciones = $observaciones;
    }
    

  }
  

?>