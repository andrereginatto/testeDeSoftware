<?php

class StackTest extends PHPUnit_Framework_TestCase {

    public function testPushAndPop() {
        /* VARIAVEIS AUTO-CADASTRO */
        $email = 'exemplo@email.com';
        $senha = 'testes123456';


        print 'Caso de Teste 07 - Criar Conta
            
  ##### Neste caso de teste esperamos que o usuario logado consiga criar a sua conta #####
  

';
		require_once '../Classes/DAO_PESSOA.php';
		require_once '../Classes/DAO_CONTA.php';
		$c = new DAO_CONTA();
		$p = new DAO_PESSOA();
		
        $usuario = $p->login($email);
            if ($usuario && password_verify($senha, $usuario["SENHA"])) {
                $session_id = $usuario['ID'];
				$nome = $usuario['NOME'];
				$c->insert_conta($session_id,$nome, 'Sicredi');
				
				$conta = $c->select_by_nome('Sicredi',$session_id);
				
                $this->assertEquals('Sicredi', $conta['NOME_CONTA']);
            }
        }
    }

?>