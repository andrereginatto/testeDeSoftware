
$(document).ready(function(){
    $('.navbar-xbootstrap').click(function(){
      $('.nav-xbootstrap').toggleClass('visible');
      $('body').toggleClass('cover-bg');
    });
  });
var anterior=[];
var fundo;
var largura = window.innerWidth;
var flag = false;

if(largura <= 480){
    flag=true;
    var botoes = document.getElementsByClassName("btn");
    var classe=null;
    var contador=0;
    
    while(contador < botoes.length){
        classe= botoes[contador].className;
        botoes[contador].className = classe+" btn-sm";
        contador++;
    }
}

window.onresize = function(){
   var largura_antiga = largura;
   largura = document.documentElement.clientWidth;
   var cont = 0;
   
   if(largura > 900){
       fundo='#2980B9';
   }
   else{
       fundo='#03A9F4';
       
   }
   
   var automatico = document.getElementById("automatico1");
   var entradas = document.getElementById("entradas1");
   var gastos = document.getElementById("gastos1");
   var categorias = document.getElementById("categorias1");
   
   if(automatico.style.display == "block"){
       document.getElementById("automatico").style="background-color:"+fundo+"; color:#fff;";
       document.getElementById("menu").className="nav-xbootstrap visible";
       document.getElementById("body").className="cover-bg";
   }
   if(entradas.style.display == "block"){
       document.getElementById("entradas").style="background-color:"+fundo+"; color:#fff; ";
       document.getElementById("menu").className="nav-xbootstrap visible";
       document.getElementById("body").className="cover-bg";
   }
   if(gastos.style.display == "block"){
       document.getElementById("gastos").style="background-color:"+fundo+"; color:#fff; ";
       document.getElementById("menu").className="nav-xbootstrap visible";
       document.getElementById("body").className="cover-bg";
   }
   if(categorias.style.display == "block"){
       document.getElementById("categorias").style="background-color:"+fundo+"; color:#fff; ";
       document.getElementById("menu").className="nav-xbootstrap visible";
       document.getElementById("body").className="cover-bg";
   }
   
   if(largura <= 480 && largura_antiga > 480 && flag ==false){
       flag=true;
        var botoes = document.getElementsByClassName("btn");
        var classe=null;
        var contador=0;
        var index;

        while(contador < botoes.length){
            classe= botoes[contador].className;
            index = classe.indexOf(" btn-sm btn-sm");
            if(index == -1){
                botoes[contador].className = classe+" btn-sm";
            }
            contador++;
        } 
    }
    else if(largura > 480 && flag==true){
        flag=false;
        var botoes = document.getElementsByClassName("btn");
        var classe=null;
        var contador=0;
        var index;
        var index2;
        
        while(contador < botoes.length){
            classe= botoes[contador].className;
            index = classe.indexOf(" btn-sm");
            index2 = classe.indexOf(" btn-sm btn-sm");
            if(index !== -1 && index2 == -1){
                botoes[contador].className = classe.substr(0, ((classe.length)-7));
            }
            contador++;
        }
    }
    
    // força para sempre que for maior que 900px não utilizar as classes do menu mobile
    if(largura >= 900){
       document.getElementById("body").className="";
       document.getElementById("menu").className="nav-xbootstrap";
    }
    
};

function navbar(elemento_clicado,largura){
    
    if(largura > 900){
        fundo='#2980B9';
    }
    else{
        fundo='#03A9F4';
    }
    var automatico = document.getElementById("automatico1");
    var gastos = document.getElementById("gastos1");
    var entradas = document.getElementById("entradas1");
    var categorias = document.getElementById("categorias1");
    var contas = document.getElementById("contas1");
    var ico_automatico = document.getElementById("ico_automatico");
    var ico_gastos = document.getElementById("ico_gastos");
    var ico_entradas = document.getElementById("ico_entradas");
    var ico_categorias = document.getElementById("ico_categorias");
    var ico_contas = document.getElementById("ico_contas");
   
    if(elemento_clicado !== "automatico"){
        automatico.style.display="none";
        document.getElementById("automatico").style="";
        ico_automatico.className="glyphicon glyphicon-chevron-down iconsize";
    }
    if(elemento_clicado !== "categorias"){
        categorias.style.display="none";
        document.getElementById("categorias").style="";
        ico_categorias.className="glyphicon glyphicon-chevron-down iconsize";
    }
    if(elemento_clicado !== "contas"){
        contas.style.display="none";
        document.getElementById("contas").style="";
        ico_contas.className="glyphicon glyphicon-chevron-down iconsize";
    }
    if(elemento_clicado !== "gastos"){
        gastos.style.display="none";
        document.getElementById("gastos").style="";
        ico_gastos.className="glyphicon glyphicon-chevron-down iconsize";
    }
    if(elemento_clicado !== "entradas"){
        entradas.style.display="none";
        document.getElementById("entradas").style="";
        ico_entradas.className="glyphicon glyphicon-chevron-down iconsize";
    }
   
    var menu = document.getElementById(elemento_clicado+"1");
    if(menu.style.display=="block"){
        menu.style.display="none";
        document.getElementById(elemento_clicado).style="";
        var spm = document.getElementById("ico_"+elemento_clicado);
        spm.className="glyphicon glyphicon-chevron-down iconsize";
    }else{
        menu.style.display="block";
        document.getElementById(elemento_clicado).style="background-color:"+fundo+"; color:#fff";
        var spm = document.getElementById("ico_"+elemento_clicado);
        spm.className="glyphicon glyphicon-chevron-up iconsize";
    }
}

//Function que pega o sexo, mostra e seleciona as imagens
function exibe_foto(foto){
    var array = ["h1","h2","h3","h4","h5","m1","m2","m3","m4","m5"];
    var cont=0;
    
    document.getElementById("h1").style="";
    document.getElementById("h2").style="";
    document.getElementById("h3").style="";
    document.getElementById("h4").style="";
    document.getElementById("h5").style="";
    document.getElementById("m1").style="";
    document.getElementById("m2").style="";
    document.getElementById("m3").style="";
    document.getElementById("m4").style="";
    document.getElementById("m5").style="";
    
    if(foto.value=='F'){
        document.getElementById("mulher").style.display="block";
        document.getElementById("homem").style.display="none";
        document.getElementById("foto").value="img/usuarios/desconhecido.png";
    }else if(foto.value=='M'){
        document.getElementById("homem").style.display="block";
        document.getElementById("mulher").style.display="none";
        document.getElementById("foto").value="img/usuarios/desconhecido.png";
    }
    
    while (cont < 10){
        if(array[cont]==foto){
            if(anterior[0] !== array[cont]){
                document.getElementById(foto).style="box-shadow: 0px 0px 4px 3px #000; background:#03A9F4; border-radius: 5px;";
                anterior[1]="S";
                document.getElementById("foto").value="img/usuarios/"+array[cont]+".png";
            }else{
                if (anterior[1]=="N"){
                    document.getElementById(foto).style="box-shadow: 0px 0px 4px 3px #000; background:#03A9F4; border-radius: 5px;";
                    anterior[1]="S";
                    document.getElementById("foto").value="img/usuarios/"+array[cont]+".png";
                }else{
                    document.getElementById(foto).style="border-radius: 5px;";
                    anterior[1]="N"
                    document.getElementById("foto").value="img/usuarios/desconhecido.png";
                }
            }
            anterior[0]=array[cont];
        }
        cont++;
    }
}

function filtra(filtro){
    
    if(document.getElementById('gastos_diferentes') !== null && document.getElementById('receitas_diferentes') !== null){
        if(filtro=='gasto'){
            document.getElementById('gastos_diferentes').value=1;
            document.getElementById('receitas_diferentes').value=0;
        }else if(filtro=='entrada'){
            document.getElementById('gastos_diferentes').value=0;
            document.getElementById('receitas_diferentes').value=1;
        }else{
            document.getElementById('gastos_diferentes').value=1;
            document.getElementById('receitas_diferentes').value=1;
        }
    }
    
    
    
    
    
    if(filtro=='gasto'){
        filtro='entrada';
    }else if(filtro=='entrada'){
        filtro='gasto';
    }

    var qtd = document.getElementsByName('entrada');
    var count=1;
    while(count <= qtd.length){
        document.getElementById('entrada'+count).style.display='block';
        document.getElementById('entrada'+count).style='null';
        count++;
    }
    qtd = document.getElementsByName('gasto');
    count=1;
    while(count <= qtd.length){
        document.getElementById('gasto'+count).style.display='block';
        document.getElementById('gasto'+count).style='null';
        count++;
    }
         
    if(filtro== 'entrada' || filtro=='gasto'){
        var qtd = document.getElementsByName(filtro);
        var count=1;
        while(count <= qtd.length){
            document.getElementById(filtro+count).style.display='none';
            count++;
        }
    }
    
}

function paginacao(acao){
    if(acao=="anterior"){
        document.getElementById("acao").value="anterior";
    }else{
        document.getElementById("acao").value="proximo";
    }
    document.getElementById("paginacao").submit();
}

function automatico(select){
    
    if(select == "1"){
        document.getElementById("qtd_repeat").style.display="none";
        document.getElementById("select_repeat").style.display="none";
        document.getElementById("vezes_repeat").disabled=true;
        document.getElementById("periodo").disabled=true;
        document.getElementById("repetir").style.display="none";
        document.getElementById("check_repetir").disabled=true;
        document.getElementById("quantidade-de-parcelas").style.display="block";
        document.getElementById("qtd_parcelas").disabled=false;
    }else if(select =="2"){
        document.getElementById("qtd_repeat").style.display="block";
        document.getElementById("select_repeat").style.display="block";
        document.getElementById("vezes_repeat").disabled=false;
        document.getElementById("periodo").disabled=false;
        document.getElementById("repetir").style.display="block";
        document.getElementById("check_repetir").disabled=false;
        if(document.getElementById("check_repetir").checked){
           document.getElementById("quantidade-de-parcelas").style.display="none";
            document.getElementById("qtd_parcelas").disabled=true; 
        }
    }else if(select == "3"){
        document.getElementById("qtd_repeat").style.display="block";
        document.getElementById("select_repeat").style.display="block";
        document.getElementById("vezes_repeat").disabled=false;
        document.getElementById("periodo").disabled=false;
        document.getElementById("repetir").style.display="block";
        document.getElementById("check_repetir").disabled=false;
        if(document.getElementById("check_repetir").checked){
            document.getElementById("quantidade-de-parcelas").style.display="none";
            document.getElementById("qtd_parcelas").disabled=true;
        }else{
            document.getElementById("quantidade-de-parcelas").style.display="block";
            document.getElementById("qtd_parcelas").disabled=false;
        }
        
    }
}

function tab(id){
    if(id=="tab_tabela"){
        document.getElementById("tabela").style.display="table";
        document.getElementById("grafico").style.display="none";
        document.getElementById("botoes_filtro").style.visibility="visible";
        document.getElementById("tab_tabela").className="active";
        document.getElementById("tab_grafico").className="";
        document.getElementById("tab_ambos").className="";
    }else if(id=="tab_grafico"){
        document.getElementById("tabela").style.display="none";
        document.getElementById("grafico").style.display="block";
        document.getElementById("tab_tabela").className="";
        document.getElementById("botoes_filtro").style.visibility="hidden";
        document.getElementById("tab_grafico").className="active";
        document.getElementById("tab_ambos").className="";
    }else{
        document.getElementById("tabela").style.display="table";
        document.getElementById("grafico").style.display="block";
        document.getElementById("botoes_filtro").style.visibility="visible";
        document.getElementById("tab_tabela").className="";
        document.getElementById("tab_grafico").className="";
        document.getElementById("tab_ambos").className="active";
    }
}