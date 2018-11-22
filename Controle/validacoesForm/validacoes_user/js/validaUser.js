 function validaFormCadastro(){
     
     nome = document.getElementById("nome");
     sobrenome = document.getElementById("sobrenome");
     telefone = document.getElementById("fone");
     masculino = document.getElementById("M").checked;
     feminino = document.getElementById("F").checked;
     email = document.getElementById("emailform");
     if(document.getElementById("senhaform") !== null){
        senha = document.getElementById("senhaform");
     }
     aniver = document.getElementById("aniver");
     foto = document.getElementById("foto");
     
     if(document.getElementById("return-banco") !== null){
         document.getElementById("return-banco").style.display="none";
     }
     
     // VARIAVEIS DE CONTROLE
     var erro = false;
     var msg_erro = "";
     var input_focus=null;
     /////////////////////////
     
     
     //VALIDAÇÕES NOME
     if(nome.value.length < 4){
         erro = true;
         msg_erro="- Por favor, informe um nome.<br>";
         input_focus="nome";
     }else if(nome.value.length > 40){
         erro= true;
         msg_erro="- O nome não pode haver mais que 40 caracteres.<br>";
         input_focus="nome";
     }
     //VALIDAÇÕES SOBRENOME
     if(sobrenome.value.length > 40){
         erro = true;
         msg_erro=msg_erro+"- O sobrenome não pode haver mais que 40 caracteres.<br>";
         if(input_focus==null){
             input_focus="sobrenome";
         }
     }
     //VALIDAÇÕES TELEFONE
     telefone = telefone.value.toString();
     telefone = telefone.replace(/[^0-9]/g, "");
     if(telefone.length == 0){
         erro = true;
         msg_erro=msg_erro+"- Por favor, informe um telefone.<br>";
         if(input_focus==null){
             input_focus="fone";
        }
     }else if(telefone.length !== 10 && telefone.length !== 11){
         erro = true;
         msg_erro=msg_erro+"- O telefone deve haver 8 ou 9 caracteres.<br>";
         if(input_focus==null){
             input_focus="fone";
         }
     }
     //VALIDAÇÕES SEXO
     if(masculino==false && feminino==false){
         erro = true;
         msg_erro=msg_erro+"- Por favor, informe seu gênero.<br>";
     }
     //VALIDAÇÕES EMAIL
     if(email.value.length < 5){
         erro = true;
         msg_erro=msg_erro+"- Por favor, informe um email.<br>";
         if(input_focus==null){
             input_focus="emailform";
         }
     }else if(email.value.length > 40){
         erro = true;
         msg_erro=msg_erro+"- O email não pode haver mais que 40 caracteres.<br>";
         if(input_focus==null){
             input_focus="emailform";
         }
     }else if(valida_email(email.value)==false){
         erro = true;
         msg_erro=msg_erro+"- Por favor insira um email válido.<br>";
         if(input_focus==null){
             input_focus="emailform";
         }
     }
     //VALIDAÇÕES SENHA
     if(document.getElementById("senhaform") !== null){
         if(senha.value.length == 0){
            erro = true;
            msg_erro=msg_erro+"- Por favor, informe uma senha.<br>";
            if(input_focus==null){
                input_focus="senha";
            }
         }else if(senha.value.length  < 8 || senha.value.length  > 30){
            erro = true;
            msg_erro=msg_erro+"- A senha deve haver entre 8 e 30 caracteres.<br>";
            if(input_focus==null){
                input_focus="senha";
            }
         }
     }
     //VALIDAÇÕES DATA NASCIMENTO
    if(aniver.value.length == 0){
        erro = true;
        msg_erro=msg_erro+"- Por favor, informe sua data de nascimento.<br>";
        if(input_focus==null){
            input_focus="aniver";
        }
    }else if(aniver.value.length < 10 || aniver.value.length > 10){
        erro = true;
        msg_erro=msg_erro+"- Por favor, informe uma data de nascimento válida.<br>";
        if(input_focus==null){
            input_focus="aniver";
        }
    }else{
        var matches = /(\d{4})[-.\/](\d{2})[-.\/](\d{2})/.exec(aniver.value);
        if (matches == null) {
            return false;
        }
        var dia = matches[3];
        var mes = matches[2] - 1;
        var ano = matches[1];
        
        if(ano < 1902){
            erro = true;
            msg_erro=msg_erro+"- Por favor, o ano deve ser no mínimo 1902.<br>";
            if(input_focus==null){
                input_focus="aniver";
            }
        }else{
            var data = new Date(ano, mes, dia);
            if(!(data.getDate() == dia && data.getMonth() == mes && data.getFullYear() == ano)){
                erro = true;
                msg_erro=msg_erro+"- Por favor, informe uma data de nascimento válida.<br>";
                if(input_focus==null){
                    input_focus="aniver";
                }
            }
        }
        
        
    }    
    //VALIDAÇÕES FOTO
    var array = ["img/usuarios/h1.png","img/usuarios/h2.png","img/usuarios/h3.png","img/usuarios/h4.png","img/usuarios/h5.png",
     "img/usuarios/desconhecido.png","img/usuarios/m1.png","img/usuarios/m2.png","img/usuarios/m3.png","img/usuarios/m4.png","img/usuarios/m5.png"];
    var achou_foto = false;
    var cont=0;
    
    while(cont < 11){
        if(array[cont]==foto.value){
            achou_foto=true;
        }
        cont++;
    }
    if(achou_foto == false){
        erro = true;
        msg_erro=msg_erro+"- Por favor, não altere o código e selecione a foto desejada.<br>";
    }
    if(erro==true){
        document.getElementById("erro_php").innerHTML=msg_erro;
        document.getElementById(input_focus).focus();
        return false;
    }else{
        return true;
    }
    
 }
 
 function masktel(value){
    value = value.length;
    var codigos = [48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105,8];
    for(var i=0; i< codigos.length; i++) {
        if(event.keyCode == codigos[i]){
            if(value==15 && event.keyCode =='8'){
                $('.fone').mask('(00) 0000-00000', {reverse: false});  
            }
            else if(value > 14 && event.keyCode !='8'){
                 $('.fone').mask('(00) 00000-0000', {reverse: false});
            }else if (value==14 && event.keyCode !='8'){
                  $('.fone').mask('(00) 00000-0000', {reverse: false});
            }else {
                 $('.fone').mask('(00) 0000-0000', {reverse: false});
            }
        }
    }
  }
  
window.onload = function(){
    if(document.getElementById("M") || document.getElementById("F") !== null){
        masculino = document.getElementById("M").checked;
        feminino = document.getElementById("F").checked;

        var flag= false;
        if(masculino==true){
            document.getElementById("M").click();
            flag=true;
        }else if(feminino==true){
            document.getElementById("F").click();
            flag=true;
        }     
        if(flag==true){
            foto = document.getElementById("saitrouxa").value;
            var array = ["h1","h2","h3","h4","h5","m1","m2","m3","m4","m5"];
            for(var i=0; i< array.length; i++) {
                if(foto.indexOf(array[i]) > -1){
                   document.getElementById(array[i]).click();
                }
            }
        }
    }
}

 $(document).ready(function() {
     if(document.getElementById("fone") !== null){
        var tamanho = document.getElementById("fone").value;
        tamanho = tamanho.length;
        if(tamanho < 15){
            $('.fone').mask('(00) 0000-0000', {reverse: false});
        }else{
            $('.fone').mask('(00) 00000-0000', {reverse: false});
        }
     }
 });
 
function valida_email(email){
    var verifica = /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i;
    var flag = verifica.exec(email);
    if (flag==null){
        return false;
    }else{
        return true;
    }
}
 
 
 /*
  *  REQUISIÇÕES AJAX
  */
 
 /**
  * Função para enviar os dados
  */
 function getVerificaEmail() {
      
     // Declaração de Variáveis
     var email   = document.getElementById("emailform").value;
     var flag = valida_email(email);
     
     if(flag== true){
        var xmlreq = CriaRequest();

        // Exibi a imagem de progresso
        document.getElementById("emailform").style = "margin-top: -7px; padding-right: 35px; background: url(validacoesForm/validacoes_user/loading.gif) no-repeat right center;";

        // Iniciar uma requisição
        xmlreq.open("GET", "validacoesForm/validacoes_user/valida_email.php?emailform=" + email, true);

        // Atribui uma função para ser executada sempre que houver uma mudança de ado
        xmlreq.onreadystatechange = function(){

            // Verifica se foi concluído com sucesso e a conexão fechada (readyState=4)
            if (xmlreq.readyState == 4) {
                // Verifica se o arquivo foi encontrado com sucesso
                if (xmlreq.status == 200) {
                    var erro_php = document.getElementById("erro_php").innerHTML;
                    var string ='- Este email já está em uso.<br>';
                    var string2 = '- Por favor insira um email válido.<br>';
                    if(xmlreq.responseText == "0"){
                        if(erro_php.indexOf(string) !== -1){
                            document.getElementById("erro_php").innerHTML= document.getElementById("erro_php").innerHTML.substr(0,erro_php.indexOf(string));
                            document.getElementById("erro_php").innerHTML=document.getElementById("erro_php").innerHTML+erro_php.substr(erro_php.indexOf(string)+string.length, erro_php.length);
                        }
                        else if(erro_php.indexOf(string2) !== -1){
                            document.getElementById("erro_php").innerHTML= document.getElementById("erro_php").innerHTML.substr(0,erro_php.indexOf(string2));
                            document.getElementById("erro_php").innerHTML=document.getElementById("erro_php").innerHTML+erro_php.substr(erro_php.indexOf(string2)+string2.length, erro_php.length);
                        }
                        
                        document.getElementById("mensagem_erro").innerHTML="";
                        document.getElementById("emailform").style = "margin-top: -7px; padding-right: 35px; background: url(validacoesForm/validacoes_user/ok.png) no-repeat right center;";
                    }else{
                        if(erro_php.indexOf(string) !== -1){
                            document.getElementById("erro_php").innerHTML= document.getElementById("erro_php").innerHTML.substr(0,erro_php.indexOf(string));
                            document.getElementById("erro_php").innerHTML=document.getElementById("erro_php").innerHTML+erro_php.substr(erro_php.indexOf(string)+string.length, erro_php.length);
                        }else if(erro_php.indexOf(string2) !== -1){
                            document.getElementById("erro_php").innerHTML= document.getElementById("erro_php").innerHTML.substr(0,erro_php.indexOf(string2));
                            document.getElementById("erro_php").innerHTML=document.getElementById("erro_php").innerHTML+erro_php.substr(erro_php.indexOf(string2)+string2.length, erro_php.length);
                        }
                        document.getElementById("mensagem_erro").innerHTML="- Este email já está em uso.<br>";
                        document.getElementById("emailform").style = "margin-top: -7px; padding-right: 35px; border: solid red 1px; background: url(validacoesForm/validacoes_user/error.png) no-repeat right center;";
                    }
                }
            }
        };
        xmlreq.send(null);
    }else{
        document.getElementById("mensagem_erro").innerHTML="";
        document.getElementById("emailform").style = "margin-top: -7px; padding-right: 35px; border: solid red 1px; background: url(validacoesForm/validacoes_user/error.png) no-repeat right center;";
    }
 }
 
 function alterarSenha(){
     var senha = document.getElementById("novasenha").value;
     if(senha.length < 8 || senha.length >30){
        document.getElementById("erro_php").innerHTML="- A senha deve ter entre 8 e 30 caracteres.";
        document.getElementById("novasenha").focus();
        return false;
     }else{
         return true;
     }
 }