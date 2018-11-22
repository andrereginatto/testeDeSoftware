<?php

class StackTest extends PHPUnit_Framework_TestCase {

    public function testPushAndPop() {
        /* VARIAVEIS AUTO-CADASTRO */
        $nome = 'nome1';
        $sobrenome = 'sobrenome1';
        $email = 'exemplo@email.com';
        $senha = 'testes123456';
        $sexo = 'M';
        $aniver = '1985/03/23';
        $fone = '54999999999';


        
        print 'Caso de Teste 01 - Criar Auto Cadastro (Passar todos os campos obrigatorios, devera cadastrar)
            
  ##### #Neste caso de teste e esperado que o usuario consiga se cadastrar preenchendo todos os campos obrigatorios #####
  

';
        require_once '../../Classes/DAO_PESSOA.php';
        $p = new DAO_PESSOA();
        $result = $p->insert_pessoa($nome, $sobrenome, $fone, null, $sexo, $email, $senha, $aniver);

        if ($result == 1) {
            print '--------------------                   
-Usuario Cadastrado-
--------------------
  
';
            $usuario = $p->login('exemplo@email.com');
            $this->assertEquals('nome1', $usuario['NOME']);
        } else {
            print '---------------------                   
-Email ja Cadastrado-
---------------------
  

';
            $this->assertTrue(false);
        }
    }

}

?>