<?php
require_once 'DAO.php';

class DAO_CONTA extends DAO{
    function insert_conta($id_titular,$nome_titular,$nome_conta){
        $sql = DAO::$db->prepare("INSERT INTO conta(NOME_TITULAR, SALDO, ID_TITULAR, NOME_CONTA) VALUES (:nome_titular,0,:id_titular,:nome_conta)");
        return $sql->execute(array(':nome_titular'=>$nome_titular, ':id_titular'=>$id_titular, ':nome_conta' => $nome_conta));
    }
    
    function select_nome_conta($id){
        $sql = DAO::$db->prepare("SELECT * FROM conta WHERE ID=:id");
        $sql->execute(array(':id'=>$id));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function select_by_nome($nome,$titular){
        $sql = DAO::$db->prepare("SELECT * FROM conta WHERE NOME_CONTA=:nome_conta and ID_TITULAR=:id_titular");
        $sql->execute(array(':nome_conta'=>$nome, ':id_titular'=> $titular));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function update_conta($id,$nome_conta){
        $sql = DAO::$db->prepare("update conta set nome_conta=:nome_conta where id=:id ");
        return $sql->execute(array(':id'=>$id, ':nome_conta'=>$nome_conta));
    }
    
    function select_by_id($id){
        $sql = DAO::$db->prepare("select * from conta where id=:id");
        $sql->execute(array(':id'=>$id));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function delete_conta($id){
        $sql = DAO::$db->prepare("delete from conta where id=:id ");
        return $sql->execute(array(':id'=>$id));
    }
    
    function list_full_contas_by_id($id_titular){
        $sql = DAO::$db->prepare("select * from conta where id_titular = :id_titular order by nome_conta");
        $sql->execute(array(':id_titular'=>$id_titular));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function select_contas($id){
        $sql = DAO::$db->prepare("SET @row_number = 0;");
        $sql->execute();
        $sql = DAO::$db->prepare("SELECT ids 
                                    FROM(
                                         SELECT ids, (@row_number:=@row_number + 1) AS num
                                           FROM 
                                               (SELECT GROUP_CONCAT(CONCAT('''',p.id,'''') SEPARATOR ', ') ids
                                                  FROM conta p 
                                                 WHERE ID_TITULAR=:id_titular
                                              GROUP BY p.id_titular 
                                                 UNION 
                                                SELECT '''-1''' ids 
                                              ORDER BY ids DESC
                                               )teste 
                                        ) table_externa
                                   WHERE num=1");
        $sql->execute(array(':id_titular'=>$id));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function select_saldo($id){
        $sql = DAO::$db->prepare("SELECT SUM(SALDO)as SALDO FROM CONTA WHERE ID_TITULAR = :id_titular");
        $sql->execute(array(':id_titular'=>$id));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
}

?>