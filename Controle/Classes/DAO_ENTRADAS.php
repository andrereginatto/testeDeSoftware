<?php
require_once 'DAO.php';
date_default_timezone_set('America/Sao_Paulo');

class DAO_ENTRADAS extends DAO{
    function insert_entrada($valor,$obs,$conta_id,$data,$categoria){
        $sql = DAO::$db->prepare("insert into entradas (conta_id,valor,obs,data,categoria) values (:conta_id, :valor, :obs,:data,:categoria)");
        return $sql->execute(array(':conta_id'=> $conta_id,':valor'=> $valor, ':obs'=> $obs, ':data'=> $data,':categoria'=>$categoria));
    }
            
    function list_entrada($id_titular,$ano_mes){
        $sql = DAO::$db->prepare("select * from entradas where conta_id  in(SELECT id
                                                                              FROM conta p 
                                                                             WHERE ID_TITULAR=:id_titular) "
                . "and extract(YEAR_MONTH from DATA)=:ano_mes order by conta_id, data desc");

        $sql->execute(array(':id_titular'=>$id_titular,':ano_mes'=>$ano_mes));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function lista_entradasFiltro($id_titular, $dt_inicio, $dt_fim, $menor_valor, $maior_valor, $obs, $categoria, $conta){
        if($categoria ==''){
            $categoria=null;
        }
        if($conta ==''){
            $conta=null;
        }        
        if($menor_valor ==''){
            $menor_valor=null;
        }
        if($maior_valor ==''){
            $maior_valor=null;
        }
        if($obs ==''){
            $obs=null;
        }else{
            $obs="%".$obs."%";
        }
        if($dt_inicio == ''){
            $dt_inicio=null;
        }else { 
            $dt_inicio =$dt_inicio.' 00:00:00';
        }
        if($dt_fim ==''){
            $dt_fim=null;
        }else { 
            $dt_fim =$dt_fim.' 23:59:59';
        }
        $sql = DAO::$db->prepare("select * from entradas 
                                   where conta_id in(SELECT id
                                                       FROM conta p 
                                                      WHERE ID_TITULAR=:id_titular)  and (DATA "
                                .">= COALESCE(:dt_inicio,DATA) and DATA <= COALESCE(:dt_fim,DATA))   "
                . "AND (VALOR >= COALESCE(:menor_valor,VALOR) AND VALOR <= COALESCE(:maior_valor,VALOR)) "
                . "AND OBS LIKE(COALESCE(:obs,OBS)) "
                . "AND categoria = COALESCE(:categoria,categoria) AND conta_id = COALESCE(:conta,conta_id) "
                . "order by conta_id, data desc");
        $sql->execute(array(':id_titular'=> $id_titular,':dt_inicio'=> $dt_inicio,':dt_fim' => $dt_fim,':menor_valor' => $menor_valor, ':maior_valor'=>$maior_valor, ':obs'=> $obs, ':conta'=> $conta, ':categoria'=>$categoria));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function update_entrada($valor,$obs,$conta_id,$data,$id,$categoria){
        $sql = DAO::$db->prepare("update entradas set conta_id=:conta_id,valor=:valor,obs=:obs,data=:data, categoria=:categoria where id=:id");
        return $sql->execute(array(':conta_id'=> $conta_id,':valor'=> $valor, ':obs'=> $obs, ':data'=> $data, ':id'=>$id, ':categoria' => $categoria));
    }
    
    function select_entrada_by_id($id){
        $sql = DAO::$db->prepare("select * from entradas where id=:id");
        $sql->execute(array(':id'=>$id));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function delete_entrada_by_id($id){
        $sql = DAO::$db->prepare("delete from entradas where id=:id");
        $sql->execute(array(':id'=>$id));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
}