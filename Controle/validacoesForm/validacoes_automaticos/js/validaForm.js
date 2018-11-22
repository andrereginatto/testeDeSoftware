function validaFormCadastroAutomaticos(){
    var valor = document.getElementById("valor");
    var obs = document.getElementById("obs");
    var data = document.getElementById("dia_insert");
    
    // VARIAVEIS DE CONTROLE
     var erro = false;
     var msg_erro = "";
     var input_focus=null;
     /////////////////////////
    
    if(document.getElementById("return-banco") !== null){
         document.getElementById("return-banco").style.display="none";
    }
    
    if(valor.value.length ==0){
        erro = true;
        msg_erro=msg_erro+"- Por favor, informe um valor.<br>";
        if(input_focus==null){
            input_focus="valor";
        }
    }else if(valor.value > 999999999999.99){
        erro = true;
        msg_erro=msg_erro+"- O valor não pode maior que 999999999999.99.<br>";
        if(input_focus==null){
            input_focus="valor";
        }
    }else if(!($.isNumeric(valor.value))){
        erro = true;
        msg_erro=msg_erro+"- Por favor, o valor deve ser um número.<br>";
        if(input_focus==null){
            input_focus="valor";
        }
    }else{
        if(valor.value < 0.1){
            erro = true;
            msg_erro=msg_erro+"- Por favor, o valor deve ser maior que 0.<br>";
            if(input_focus==null){
                input_focus="valor";
            }
        }
    }
    
    if(obs.value.length ==0){
        erro = true;
        msg_erro=msg_erro+"- Por favor, informe uma descrição.<br>";
        if(input_focus==null){
            input_focus="obs";
        }
    }else if(obs.value.length > 60){
        erro = true;
        msg_erro=msg_erro+"- O descrição não pode haver mais que 60 caracteres.<br>";
        if(input_focus==null){
            input_focus="obs";
        }
    }
    
    if(data.value.length ==0){
        erro = true;
        msg_erro=msg_erro+"- Por favor, informe a data da primeira parcela.<br>";
        if(input_focus==null){
            input_focus="dia_insert";
        }
    }else if(data.value.length > 10){
        erro = true;
        msg_erro=msg_erro+"- Por favor, informe uma data válida.<br>";
        if(input_focus==null){
            input_focus="dia_insert";
        }
    }else{
        var matches = /(\d{4})[-.\/](\d{2})[-.\/](\d{2})/.exec(data.value);
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
                input_focus="dia_insert";
            }
        }else{
            var datae = new Date(ano, mes, dia);
            if(!(datae.getDate() == dia && datae.getMonth() == mes && datae.getFullYear() == ano)){
                erro = true;
                msg_erro=msg_erro+"- Por favor, informe uma data válida.<br>";
                if(input_focus==null){
                    input_focus="dia_insert";
                }
            }
        }
    }
    
    if(document.getElementById("qtd_parcelas")!== null && document.getElementById("qtd_parcelas").disabled==0){
        if(document.getElementById("qtd_parcelas").value > 100000 || document.getElementById("qtd_parcelas").value < 1){
            erro = true;
            msg_erro=msg_erro+"- Por favor, a quantidade de parcelas deve ser no máximo 100000 e mínimo 1.<br>";
            if(input_focus==null){
                input_focus="qtd_parcelas";
            }
        }else if(!($.isNumeric(document.getElementById("qtd_parcelas").value))){
            erro = true;
            msg_erro=msg_erro+"- Por favor, a quantidade de parcelas deve ser um número.<br>";
            if(input_focus==null){
                input_focus="qtd_parcelas";
            }
        }
    }
    
    if(document.getElementsByName("repeticao")!== null){
        
        if (!(document.getElementById("mensal").checked==1 || document.getElementById("avancado").checked==1)){
            erro = true;
            msg_erro=msg_erro+"- Por favor, não altere o código.<br>";
            if(input_focus==null){
                input_focus="mensal";
            }
        }else if(document.getElementById("avancado").checked==1){
            if(document.getElementById("vezes_repeat") == null){
                erro = true;
                msg_erro=msg_erro+"- Por favor, não altere o código.<br>";
                if(input_focus==null){
                    input_focus="vezes_repeat";
                }
                
            }else if(document.getElementById("periodo") == null){
                erro = true;
                msg_erro=msg_erro+"- Por favor, não altere o código.<br>";
                if(input_focus==null){
                    input_focus="periodo";
                }
            }
            
            if(document.getElementById("vezes_repeat").value < 1 || document.getElementById("vezes_repeat").value > 10){
                erro = true;
                msg_erro=msg_erro+"- Por favor, não altere o código.<br>";
                if(input_focus==null){
                    input_focus="vezes_repeat";
                }
            }else if(document.getElementById("periodo").value !== 'DAY' &&
                     document.getElementById("periodo").value !== 'WEEK' &&
                     document.getElementById("periodo").value !== 'MONTH' &&
                     document.getElementById("periodo").value !== 'YEAR'){
                    erro = true;
                    msg_erro=msg_erro+"- Por favor, não altere o código.<br>";
                    if(input_focus==null){
                        input_focus="periodo";
                    }
            }
        }
    }
    
    if(erro==true){
        document.getElementById("erro_php").innerHTML=msg_erro;
        document.getElementById(input_focus).focus();
        return false;
    }else{
        return true;
    } 
    
}