<?php
require_once 'DAO.php';

class DAO_CATEGORIAS extends DAO{
    function select_by_id_user($id){
        $sql = DAO::$db->prepare("SELECT null as ID,'Selecione uma Categoria' as CATEGORIA,'0' as QUEM_CRIOU  UNION ALL (SELECT * FROM `categorias` where QUEM_CRIOU=:id ORDER BY CATEGORIA asc)");
        $sql->execute(array(':id'=>$id));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function select_by_id_and_full($id){
        $sql = DAO::$db->prepare("SELECT null as ID,'Sem Categoria' as CATEGORIA,'0' as QUEM_CRIOU  UNION ALL (SELECT * FROM `categorias` where QUEM_CRIOU=:id or QUEM_CRIOU=0 ORDER BY CATEGORIA asc)");
        $sql->execute(array(':id'=>$id));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function select_by_id_cat($id){
        $sql = DAO::$db->prepare("select * from categorias where id=:id");
        $sql->execute(array(':id'=>$id));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function insert_categoria($quem_criou,$categoria){
        $sql = DAO::$db->prepare("insert into categorias (categoria,quem_criou) values (:categoria,:quem_criou)");
        return $sql->execute(array(':quem_criou'=>$quem_criou, ':categoria'=>$categoria));
    }
    
    function update_categoria($id,$categoria){
        $sql = DAO::$db->prepare("update categorias set categoria=:categoria where id=:id ");
        return $sql->execute(array(':id'=>$id, ':categoria'=>$categoria));
    }
    
    function delete_categoria($id){
        $sql = DAO::$db->prepare("delete from categorias where id=:id ");
        return $sql->execute(array(':id'=>$id));
    }
}
