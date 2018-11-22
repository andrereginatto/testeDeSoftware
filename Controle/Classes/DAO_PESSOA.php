<?php
require_once 'DAO.php';
class DAO_PESSOA extends DAO{
    function insert_pessoa($nome,$sobrenome,$fone,$img,$sexo,$email,$senha,$aniver){
        $sql = DAO::$db->prepare("insert into pessoa (nome,sobrenome,telefone,img,sexo,email,senha,aniversario) values "
                              . " (:nome, :sobrenome, :fone, :img, :sexo, :email, :senha, :aniver)");
        return $sql->execute(array(':nome'=> $nome,':sobrenome'=> $sobrenome, ':fone'=> $fone, ':img'=> $img,':sexo'=> $sexo,
                                   ':email'=> $email, ':senha' => password_hash($senha, PASSWORD_DEFAULT), ':aniver' => $aniver));
    }
    function login($email){
        $sql = DAO::$db->prepare("select * from pessoa where email = :email");
        $sql->execute(array(':email'=>$email));
        return $sql->fetch();
    }
    function select_user($id){
        $sql = DAO::$db->prepare("select * from pessoa where id = :id");
        $sql->execute(array(':id'=>$id));
        return $sql->fetch();
    }
    
    function delete_user($id){
        $sql = DAO::$db->prepare("delete from pessoa where id = :id");
        return $sql->execute(array(':id'=>$id));
    }
    
    function verifica_email($email){
        $sql = DAO::$db->prepare("select count(1) as cont from pessoa where email =:email");
        $sql->execute(array(':email'=>$email));
        return $sql->fetch();
    }
    
    function verifica_alterar_email($email,$email_antigo){
        $sql = DAO::$db->prepare("select count(1) as cont from pessoa where email =:email and email <> :email_antigo");
        $sql->execute(array(':email'=>$email,':email_antigo'=>$email_antigo));
        return $sql->fetch();
    }
    
    function update_pessoa($nome,$sobrenome,$fone,$img,$sexo,$email,$aniver,$id){
        $sql = DAO::$db->prepare("update pessoa set "
                                                    . "nome = :nome,"
                                                    . "sobrenome = :sobrenome, "
                                                    . "telefone = :fone, "
                                                    . "img = :img, "
                                                    . "sexo = :sexo, "
                                                    . "email = :email, "
                                                    . "aniversario = :aniver "
                                                    . "where id= :id");
        return $sql->execute(array(':nome'=> $nome,':sobrenome'=> $sobrenome, ':fone'=> $fone, ':img'=> $img,':sexo'=> $sexo,
                                   ':email'=> $email, ':id' => $id, ':aniver' => $aniver));
    }
    
    function update_senha($senha,$id){
        $sql = DAO::$db->prepare("update pessoa set senha=:senha where id =:id");
        return $sql->execute(array(':senha' => password_hash($senha, PASSWORD_DEFAULT),':id'=>$id));
    }
}