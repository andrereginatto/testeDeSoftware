<?php

class StackTest extends PHPUnit_Framework_TestCase {

    public function testPushAndPop() {
        /* VARIAVEIS AUTO-CADASTRO */
        $nome = null;
        $sobrenome = 'sobrenome2';
        $email = 'testeemail@email.com';
        $senha = 'testes123456';
        $sexo = 'M';
        $aniver = '1985/03/23';
        $fone = '54999999999';


        print 'Caso de Teste 02 - Criar Auto Cadastro (Nao passar todos os campos obrigatorios, nao devera cadastrar)
            
  ##### Neste caso de teste e esperado que o usuario nao consiga se cadastrar porque ele nao esta preenchendo todos os campos obrigatorios #####
  

';
        require_once '../../Classes/DAO_PESSOA.php';
        $p = new DAO_PESSOA();
        $result = $p->insert_pessoa($nome, $sobrenome, $fone, null, $sexo, $email, $senha, $aniver);

        if ($result == 1) {
            print '--------------------                   
-Usuario Cadastrado-
--------------------
  
';
            $usuario = $p->login('testeemail@email.com');
            $this->assertEquals('sobrenome2', $usuario['SOBRENOME']);
        } else {
            $usuario = $p->login('testeemail@email.com');
            $this->assertNull($usuario['SOBRENOME']);

            print '

------------------------------------                   
-Campos obrigatorios nao informados-
------------------------------------
  

';
            
            if ($nome == null){
                print'
                    
                 Nome nao informado
                 
';
            }
            
            if ($sobrenome == null){
                print'
                    
                 Sobrenome nao informado
                 
';
            }
            
            if ($email == null){
                print'
                    
                 Email nao informado
                 
';
            }
            
            if ($senha == null){
                print'
                    
                 Senha nao informada
                 
';
            }
            
            if ($sexo == null){
                print'
                    
                 Sexo nao informado
                 
';
            }
            
            if ($aniver == null){
                print'
                    
                 Aniver nao informado
                 
';
            }

            if ($fone == null){
                print'
                    
                 Fone nao informado
                 
';
            }
        }
    }

}

?>