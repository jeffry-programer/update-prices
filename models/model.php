<?php 
require_once "../models/connection.php";

class Model{

    public function queryRelationsProdExist($prodId, $bussinessId){
        $stmt = Conexion::conectar()->prepare("SELECT Producto_idProducto FROM producto_has_empresa WHERE Producto_idProducto = :prodId AND Empresa_idEmpresa = :bussinessId");
        $stmt->bindParam(':prodId', $prodId, PDO::PARAM_INT);
        $stmt->bindParam(':bussinessId', $bussinessId, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();
        $stmt = null;
        return $res;
    }

    public function queryProdId($nameProd){
        $stmt = Conexion::conectar()->prepare("SELECT idProducto FROM producto WHERE Nombre = :nameProd");
        $stmt->bindParam(':nameProd', $nameProd, PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch();
        $stmt = null;
        return $res;
    }

    public function insertRelation($prodId, $bussinessId, $price){
        $stmt = Conexion::conectar()->prepare("INSERT INTO producto_has_empresa (Producto_idProducto, Empresa_idEmpresa, precioReal) VALUES (:prodId, :bussienssId, :price)");
        $stmt->bindParam(':prodId', $prodId, PDO::PARAM_INT);
        $stmt->bindParam(':bussinessId', $bussinessId, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $res = $stmt->execute();
        if($res){
            return "ok";
        }else{
            return "error";
        }
        
    }

    public function updateRelation($prodId, $bussinessId, $price){ 
        $stmt = Conexion::conectar()->prepare("UPDATE producto_has_empresa SET precioReal = :price WHERE Empresa_idEmpresa = :bussinessId AND Producto_idProducto = :prodId");
        $stmt->bindParam(':prodId', $prodId, PDO::PARAM_INT);
        $stmt->bindParam(':bussinessId', $bussinessId, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $res = $stmt->execute();
        if($res){
            return "ok";
        }else{
            return "error";
        } 
    }

    public function findBussiness(){
        $stmt = Conexion::conectar()->prepare("SELECT idEmpresa, nombreEmpresa FROM empresa");
        $stmt->execute();
        $res = $stmt->fetchAll();
        $stmt = null;
        return $res;
    }
}