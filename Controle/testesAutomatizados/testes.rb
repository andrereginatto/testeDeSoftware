## Fazendo a requsição do GEM do selenium
require 'selenium-webdriver'
# indicando para o selenium qual é o caminho do driver para o chrome
Selenium::WebDriver::Chrome.driver_path="chromedriver.exe"
# Declarando a varável @Driver atribuindo o Webdriver do Chrome
@driver = Selenium::WebDriver.for :chrome
# Pedindo para ir para o endereço da página
@driver.get "http://localhost/Controle"

#	CT01 -- AUTO CADASTRO COM SUCESSO --
#Neste caso de teste é esperado que o Usuário consiga se cadastrar preenchendo todos os campos obrigatórios.
sleep 1
@driver.find_element(:id, "cadastroAgora").click
sleep 1
@driver.find_element(:id, "nome").send_keys("nome1")
sleep 1
@driver.find_element(:id, "sobrenome").send_keys("sobrenome1")
sleep 1
@driver.find_element(:id, "fone").send_keys("54999999999")
sleep 1
@driver.find_element(:id, "Masculino").click
sleep 1
@driver.find_element(:id, "emailform").send_keys("exemplo@email.com")
sleep 1
@driver.find_element(:id, "senhaform").send_keys("testes123456")
sleep 1
@driver.find_element(:id, "aniver").send_keys("23/03/1985")
sleep 1
@driver.find_element(:name, "save").click
sleep 1


#	CT02 -- AUTO CADASTRO COM ERRO --
#Neste caso de teste é esperado que o Usuário não consiga se cadastrar porque ele não está preenchendo todos os campos obrigatórios.
sleep 1
@driver.find_element(:id, "cadastroAgora").click
sleep 1
@driver.find_element(:name, "save").click
sleep 1


#	CT03 -- AUTO CADASTRO COM EMAIL JÁ CADASTRADO --
#Neste caso de teste é esperado seja exibida uma mensagem de email já cadastrado logo que o usuário preencher o email e não permitir o cadastro.
@driver.get "http://localhost/Controle/cadastro.php"
sleep 1
@driver.find_element(:id, "nome").send_keys("nome2")
sleep 1
@driver.find_element(:id, "sobrenome").send_keys("sobrenome2")
sleep 1
@driver.find_element(:id, "fone").send_keys("54999999999")
sleep 1
@driver.find_element(:id, "Masculino").click
sleep 1
@driver.find_element(:id, "emailform").send_keys("exemplo@email.com")
sleep 1
@driver.find_element(:id, "senhaform").send_keys("testes123456")
sleep 1
@driver.find_element(:id, "aniver").send_keys("23/03/1985")
sleep 1
@driver.find_element(:name, "save").click
sleep 5


#	CT04 -- REALIZAR LOGIN, NÃO INFORMAR EMAIL OU SENHA --
#Neste caso de teste é esperado que o usuário não consiga acessar o sistema por não estar informando os dados obrigatórios.
@driver.get "http://localhost/Controle/"
sleep 1
@driver.find_element(:id, "btn_entrar").click
sleep 5


#	CT05 -- REALIZAR LOGIN, EMAIL OU SENHA INVALIDOS --
#Neste caso de teste é esperado que o usuário não consiga acessar o sistema por não estar informando os dados válidos.
@driver.find_element(:id, "email22").send_keys("exemplo@email.com")
sleep 1
@driver.find_element(:name, "senha").send_keys("qualquersenha")
sleep 1
@driver.find_element(:id, "btn_entrar").click
sleep 5


#	CT06 -- REALIZAR LOGIN, SUCESSO --
#Neste caso de teste é esperado que o usuário cadastrado no CT01 consiga acessar o sistema.
@driver.find_element(:name, "senha").send_keys("testes123456")
sleep 1
@driver.find_element(:id, "btn_entrar").click
sleep 5

#	CT07 -- MANTER CONTA - Criar conta --
#Neste caso de teste é esperado que o usuário que esteja logado no sistema, consiga criar uma conta.
@driver.find_element(:id, "conta").send_keys("Sicredi")
sleep 1
@driver.find_element(:name, "save").click
sleep 5 


#	CT08 -- MANTER CONTA - Editar conta --
#Neste caso de teste é esperado que o usuário que esteja logado no sistema, consiga alterar uma conta.
@driver.find_element(:id, "editar_conta").click
sleep 1
@driver.execute_script("document.getElementById('conta').value=''");
sleep 1
@driver.find_element(:id, "conta").send_keys("Banrisul")
sleep 1
@driver.find_element(:name, "save").click
sleep 5


#	CT09 -- MANTER GASTOS - Adicionar gasto [ERRO EM CAMPOS OBRIGATORIOS] --
#Neste caso de teste é esperado que o usuário que esteja logado no sistema, não consiga adicionar um gasto.
@driver.get "http://localhost/Controle/adicionar_gasto.php"
sleep 1
@driver.find_element(:name, "save").click
sleep 5


#	CT10 -- MANTER GASTOS - Adicionar gasto --
#Neste caso de teste é esperado que o usuário que esteja logado no sistema,  consiga adicionar um gasto.
@driver.find_element(:id, "valor").send_keys("100")
sleep 1
@driver.find_element(:id, "obs").send_keys("teste1")
sleep 1
@driver.find_element(:id, "data").send_keys("01/11/2018")
sleep 1
@driver.find_element(:name, "save").click
sleep 5


#	CT11 -- MANTER GASTOS - Editar gasto [ERRO EM CAMPOS OBRIGATORIOS]--
#Neste caso de teste é esperado que o usuário que esteja logado no sistema, não consiga alterar um gasto por não estar passando todos os campos obrigatórios.
@driver.get "http://localhost/Controle/editar_gasto.php"
sleep 1
@driver.find_element(:id, "teste1").click
sleep 1
@driver.execute_script("document.getElementById('valor').value=''");
sleep 1
@driver.find_element(:id, "valor").send_keys("")
sleep 1
@driver.find_element(:name, "save").click
sleep 5


#	CT12 -- MANTER GASTOS - Editar gasto --
#Neste caso de teste é esperado que o usuário que esteja logado no sistema, consiga alterar um gasto.
@driver.find_element(:id, "valor").send_keys("110")
sleep 1
@driver.find_element(:name, "save").click
sleep 5


#	CT13 -- MANTER GASTOS - Excluir gasto --
#Neste caso de teste é esperado que o usuário que esteja logado no sistema, consiga excluir um gasto.
@driver.get "http://localhost/Controle/editar_gasto.php"
sleep 1
@driver.find_element(:id, "teste1").click
sleep 1
@driver.execute_script("window.confirm = function () {return true}")
sleep 1
@driver.execute_script("document.getElementById('mensagem_erro').innerHTML='Nesta tela haveria um CONFIRM ao clicar em excluir, porém foi adicionado um script para todos os confirms serem confirmados automaticamente';")
sleep 5
@driver.find_element(:name, "delete").click
sleep 5


#	CT14 -- MANTER CONTAS - Excluir conta --
#Neste caso de teste é esperado que o usuário que esteja logado no sistema, consiga excluir uma conta.
@driver.get "http://localhost/Controle/editar_conta.php"
sleep 1
@driver.find_element(:id, "editar_conta").click
sleep 1
@driver.execute_script("window.confirm = function () {return true}")
sleep 1
@driver.execute_script("document.getElementById('mensagem_erro').innerHTML='Nesta tela haveria um CONFIRM ao clicar em excluir, porém foi adicionado um script para todos os confirms serem confirmados automaticamente';")
sleep 5
@driver.find_element(:name, "delete").click
sleep 5


#	CT15 -- REALIZAR LOGOUT --
#Neste caso de teste é esperado que o usuário que esteja logado no sistema, ao clicar no botão "Sair" seja deslogado do sistema.
@driver.get "http://localhost/Controle/"
sleep 1
@driver.find_element(:id, "sair").click
sleep 2
