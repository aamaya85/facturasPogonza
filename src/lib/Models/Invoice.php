<?php
	
	namespace App\Models;

	use \SimpleORM\Model;

	class Invoice extends Model {

		protected $id;
		protected $nro_factura;
    protected $fecha;
    protected $nro_cliente;
    protected $cliente;
    protected $descripcion;
    protected $localidad;
    protected $neto;
    protected $iva;
    protected $total_factura;
    protected $vencimiento;
    protected $pagos = array();
    protected $activo;
    protected $tipo;
    protected $letra;
    protected $color;
    
    public function __construct(\mysqli $connection = null){
      return $this;
    }

    //Select the table which the model references
    protected static function getTableName(){
      return 'facturas';
    }

    public function toArray(){
      return array(
        "id" => $this->getId(),
        "nro_factura" => $this->getNroFactura(),
        "fecha" => $this->getFecha(),
        "nro_cliente" => $this->getNroCliente(),
        "cliente" => $this->getCliente(),
        "descripcion" => preg_replace( "/\r|\n/", "", $this->getDescripcion()),
        "localidad" => $this->getLocalidad(),
        "neto" => $this->getNeto(),
        "iva" => $this->getIva(),
        "total_factura" => $this->getTotalFactura(),
        "vencimiento" => $this->getVencimiento(),
        "activo" => $this->isActive(),
        "tipo" => $this->getTipo(),
        "letra" => $this->getLetra(),
        "color" => $this->getColor()
      );
    }
      
    public function hasPayments(){
      return count($this->pagos) > 0;
    }

    public function getTotalPaid(){

      $monto = 0;

      if (count($this->pagos) > 0){
        foreach($this->pagos as $p){
          $monto += (float)$p['monto_pagado'];
        }
      }

      return $monto;

    }

    // GETTERS
    public function getId(){
      return $this->id;
    }

    public function getNroFactura(){
      return $this->nro_factura;
    }

    public function getFecha(){
      return $this->fecha;
    }

    public function getNroCliente(){
      return $this->nro_cliente;
    }

    public function getCliente(){
      return $this->cliente;
    }

    public function getDescripcion(){
      return $this->descripcion;
    }

    public function getLocalidad(){
      return $this->localidad;
    }

    public function getNeto(){
      return $this->neto;
    }

    public function getIva(){
      return $this->iva;
    }

    public function getTotalFactura(){
      return $this->total_factura;
    }

    public function getVencimiento(){
      return $this->vencimiento;
    }

    public function getPagos() : array{
      return $this->pagos;
    }

    public function isActive() : bool{
      return $this->activo;
    }

    public function getTipo(){
      return $this->tipo;
    }

    public function getLetra(){
      return $this->letra;
    }
    
    public function getColor(){
      return $this->color;
    }

    // SETTERS
    public function setId($id){
      $this->id = $id;
    }

    public function setNroFactura($nroFactura){
      $this->nro_factura = $nroFactura;
    }

    public function setFecha($fecha){
      $this->fecha = $fecha;
    }

    public function setNroCliente($nroCliente){
      $this->nro_cliente = $nroCliente;
    }

    public function setCliente($cliente){
      $this->cliente = $cliente;
    }

    public function setDescripcion($descripcion){
      $this->descripcion = $descripcion;
    }

    public function setLocalidad($localidad){
      $this->localidad = $localidad;
    }

    public function setNeto($neto){
      $this->neto = $neto;
    }

    public function setIva($iva){
      $this->iva = $iva;
    }

    public function setTotalFactura($totalFactura){
      $this->total_factura = $totalFactura;
    }

    public function setVencimiento($vencimiento){
      $this->vencimiento = $vencimiento;
    }

    public function setPago($pago){
      $this->pagos[] = $pago;
    }

    public function setActivo($activo){
      $this->activo = $activo;
    }

    public function setTipo($tipo){
      $this->tipo = $tipo;
    }

    public function setLetra($letra){
      $this->letra = $letra;
    }
    
    public function setColor($color){
      $this->color = $color;
    }

	}

?>