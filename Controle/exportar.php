<?php	
session_start();
if (isset($_SESSION['conta'])) {
require_once './Classes/DAO_MOVIMENTOS.php';
require_once './Classes/DAO_CONTA.php';
require_once './Classes/DAO_CATEGORIAS.php';
$c = new DAO_CATEGORIAS();
$contass = new DAO_CONTA();
$p = new DAO_MOVIMENTOS();
$teste = new DAO_MOVIMENTOS();



    
    $categorias = $c->select_by_id_and_full($_SESSION['id']);
    $contasUser = $contass->list_full_contas_by_id($_SESSION['id']);
    
    if (!isset($_POST['dt_inicio'], $_POST['dt_fim'])) {
        $movimentos = $p->lista_movimentos($_SESSION['id'], $_SESSION['ano'] . $_SESSION['mes'],$_POST['gastos'],$_POST['receitas'] );
    } else {
        $movimentos = $p->lista_movimentosFiltro($_SESSION['id'], $_POST['dt_inicio'], $_POST['dt_fim'],$_POST['menor_valor'],$_POST['maior_valor'], $_POST['obs'],$_POST['gastos'],$_POST['receitas'],$_POST['categoria'],$_POST['conta']);
    }

switch ($_SESSION['mes']) {
    case '01':
        $mes_ano = 'Janeiro de ' . $_SESSION['ano'];
        break;
    case '02':
        $mes_ano = 'Fevereiro de ' . $_SESSION['ano'];
        break;
    case '03':
        $mes_ano = 'Março de ' . $_SESSION['ano'];
        break;
    case '04':
        $mes_ano = 'Abril de ' . $_SESSION['ano'];
        break;
    case '05':
        $mes_ano = 'Maio de ' . $_SESSION['ano'];
        break;
    case '06':
        $mes_ano = 'Junho de ' . $_SESSION['ano'];
        break;
    case '07':
        $mes_ano = 'Julho de ' . $_SESSION['ano'];
        break;
    case '08':
        $mes_ano = 'Agosto de ' . $_SESSION['ano'];
        break;
    case '09':
        $mes_ano = 'Outubro de ' . $_SESSION['ano'];
        break;
    case '10':
        $mes_ano = 'Setembro de ' . $_SESSION['ano'];
        break;
    case '11':
        $mes_ano = 'Novembro de ' . $_SESSION['ano'];
        break;
    case '12':
        $mes_ano = 'Dezembro de ' . $_SESSION['ano'];
        break;
}
if(isset($_POST['dt_inicio'])){
    $titulo = 'Movimentos com data Persolnalizada';
}

if (isset($mes_ano) && !isset($_POST['dt_inicio'], $_POST['dt_fim'])) {
    $titulo= 'Movimentos de ' . $mes_ano;
} elseif (isset($_POST['dt_inicio'], $_POST['dt_fim'])) {
    $titulo = 'Movimentos com Busca Personalizada';
} else {
    $titulo= 'Controle seu dinheiro AGORA';
}


	$html = '<html lang="pt-br">
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <!-- As 3 meta tags acima *devem* vir em primeiro lugar dentro do `head`; qualquer outro conteúdo deve vir *após* essas tags -->
                    <title>Meu Controle</title>

                    <!-- HTML5 shim e Respond.js para suporte no IE8 de elementos HTML5 e media queries -->
                    <!-- ALERTA: Respond.js não funciona se você visualizar uma página file:// -->
                    <!--[if lt IE 9]>
                      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
                      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
                    <![endif]-->
                      <link href="https://fonts.googleapis.com/css?family=Cabin:700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Tajawal" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tema opcional -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css" >
    <link href="css/inicial.css" rel="stylesheet">
<!--FINAL INCLUSAO CSS -->

<!--INICIO INCLUSAO JAVASCRIPT -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <!-- Inclui todos os plugins compilados (abaixo), ou inclua arquivos separadados se necessário -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/navbar.js"></script>
                </head>
                <body id="body" style="background:#FFF;"> 
                <div class="container">
                <div class="titulo_div">
                        <strong style="margin-left: 4px; margin-right: 40px;">'.$titulo.'
                        </strong>
                </div>
                <table class="table" style="margin-top: 0px;">
                        <tr class="cabecalho-table">
                            <td >Data</td>
                            <td class="obs">Descrição</td>
                            <td >Categoria</td>
                            <td >Valor</td>
                        </tr>
                            <tbody>';
	$gasto = 0;
                                $entrada = 0;
                                $total_gasto = 0;
                                $total_entrada = 0;
                                $conta_anterior = null;
                                $conta_atual = null;

                                foreach ($movimentos as $linha) {
                                    if ($linha['ENTRADAS_ID'] == null) {
                                        $cor = 'red';
                                        $tipo = 'gasto';
                                        $gasto++;
                                        $id = $tipo . $gasto;
                                        $total_gasto = $total_gasto + $linha['VALOR'];
                                    } else {
                                        $cor = 'green';
                                        $tipo = 'entrada';
                                        $entrada++;
                                        $id = $tipo . $entrada;
                                        $total_entrada = $total_entrada + $linha['VALOR'];
                                    }
                                    $linha['DATA_MOVIMENTO'] = new DateTime($linha['DATA_MOVIMENTO']);
                                    $data = $linha['DATA_MOVIMENTO']->format('d/m/Y');
                                    $conta_atual = $linha['CONTA_ID'];
                                    if($conta_anterior == null){
                                        $nome_conta = $contass ->select_nome_conta($linha['CONTA_ID']);
                                        if(isset($_POST['dt_inicio'])){
                                            $total_movimentos = $teste->lista_movimentosTotalFiltro($_SESSION['id'], $_POST['dt_inicio'], $_POST['dt_fim'],$_POST['menor_valor'],$_POST['maior_valor'], $_POST['obs'],$_POST['gastos'],$_POST['receitas'],$_POST['categoria'],$linha['CONTA_ID']);
                                            $html .= '<tr class="nome_conta"><td>'.$nome_conta['NOME_CONTA'].'</td><td class="obs"></td>';
                                            $html .= '<td style="margin-top:-30px;"> Gastos: <text style="color:red;">'.$total_movimentos['GASTOS'].'</text></td>';
                                            $html .= '<td style="margin-top:-30px;"> Receitas: <text style="color:green;">'.$total_movimentos['ENTRADAS'].'</text></td></tr>';
                                        }else{
                                            $total_movimentos = $teste->lista_TotalMovimentosPDF($_SESSION['ano'] . $_SESSION['mes'],$linha['CONTA_ID'],$_POST['gastos'],$_POST['receitas']);
                                            $html .= '<tr class="nome_conta"><td>'.$nome_conta['NOME_CONTA'].'</td><td class="obs"></td>';
                                            $html .= '<td style="margin-top:-30px;"> Gastos: <text style="color:red;">'.$total_movimentos['GASTOS'].'</text></td>';
                                            $html .= '<td style="margin-top:-30px;"> Receitas: <text style="color:green;">'.$total_movimentos['ENTRADAS'].'</text></td></tr>';
                                        }
                                    }else if($conta_anterior !== $conta_atual){
                                        $nome_conta = $contass ->select_nome_conta($linha['CONTA_ID']);
                                        if(isset($_POST['dt_inicio'])){
                                            $total_movimentos = $teste->lista_movimentosTotalFiltro($_SESSION['id'], $_POST['dt_inicio'], $_POST['dt_fim'],$_POST['menor_valor'],$_POST['maior_valor'], $_POST['obs'],$_POST['gastos'],$_POST['receitas'],$_POST['categoria'],$linha['CONTA_ID']);
                                            $html .= '<tr class="nome_conta"><td>'.$nome_conta['NOME_CONTA'].'</td><td class="obs"></td>';
                                            $html .= '<td style="margin-top:-30px;"> Gastos: <text style="color:red;">'.$total_movimentos['GASTOS'].'</text></td>';
                                            $html .= '<td style="margin-top:-30px;"> Receitas: <text style="color:green;">'.$total_movimentos['ENTRADAS'].'</text></td></tr>';
                                        }else{
                                            $total_movimentos = $teste->lista_TotalMovimentos($_SESSION['ano'] . $_SESSION['mes'], $linha['CONTA_ID']);
                                            $html .= '<tr class="nome_conta"><td>'.$nome_conta['NOME_CONTA'].'</td><td class="obs"></td>';
                                            $html .= '<td style="margin-top:-30px;"> Gastos: <text style="color:red;">'.$total_movimentos['GASTOS'].'</text></td>';
                                            $html .= '<td style="margin-top:-30px;"> Receitas: <text style="color:green;">'.$total_movimentos['ENTRADAS'].'</text></td></tr>';
                                        }
                                        
                                    }
                                    $conta_anterior = $conta_atual;
                                  
                                $html .='<tr style="font-size:13px;" name="'.$tipo.'" id="'.$id.'" class="'.$tipo.'">';
                                $html .='<td>'.$data.'</td>';
                                $html .='<td class="obs">'.$linha['NUMERO_PARCELA'].$linha['OBS'].'</td>';
                                $html .='<td>'.$linha['CATEGORIA'].'</td>';
                                $html .='<td><strong style="color:'.$cor.'">'.$linha['VALOR'].'</strong></td></tr>';
                                }
                                $verificaPonto = strpos($total_gasto, '.');
                                if (!$verificaPonto) {
                                    $total_gasto = $total_gasto . ".00";
                                } else {
                                    $rest = substr($total_gasto, $verificaPonto + 1, strlen($total_gasto));
                                    if (strlen($rest) < 2) {
                                        $total_gasto = $total_gasto . '0';
                                    }
                                }
                                $verificaPonto = strpos($total_entrada, '.');
                                if (!$verificaPonto) {
                                    $total_entrada = $total_entrada . ".00";
                                } else {
                                    $rest = substr($total_entrada, $verificaPonto + 1, strlen($total_entrada));
                                    if (strlen($rest) < 2) {
                                        $total_entrada = $total_entrada . '0';
                                    }
                                }
                               $html .=' </tbody>
                        </table>';
                               $html .='<center>
                            <div class="esquerda" style="float: left">
                                <text class="total_gastos">Total de Gastos do Período: </text>
                                <strong class="total_gastos" style="color:red;">R$ '.$total_gasto.'</strong><br>
                                <text class="total_entradas">Total de Receitas do Período: </text>
                                <strong class="total_entradas" style="color:green;">R$ '.$total_entrada.'</strong>
                            </div></center>
                            </div></body></html>';
          }else{
              $html="Você Precisa estar Logado!";
          }                          
                                    
	//referenciar o DomPDF com namespace
	use Dompdf\Dompdf;

	// include autoloader
	require_once("dompdf/autoload.inc.php");

	//Criando a Instancia
	$dompdf = new DOMPDF();
        
	// Carrega seu HTML
	$dompdf->load_html($html);

	//Renderizar o html
	$dompdf->render();

	//Exibibir a página
	$dompdf->stream(
		$_SESSION['nome']." - ".$titulo.".pdf", 
		array(
			"Attachment" => false //Para realizar o download somente alterar para true
		)
	);
     
?>