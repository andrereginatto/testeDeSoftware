<?php
require_once 'DAO.php';
class DAO_MOVIMENTOS extends DAO{
    function lista_movimentos($id_titular,$ano_mes,$gastos,$entradas){
        
        if($gastos==1){
            $gastos=null;
        }
        if($entradas==1){
            $entradas=null;
        }
        
        $sql = DAO::$db->prepare("SET @row_number = 0;");
        $sql->execute();
        $sql = DAO::$db->prepare("select * from movimentos 
                                   where conta_id in(SELECT id
                                                  FROM conta p 
                                                 WHERE ID_TITULAR=:id) and (GASTOS_ID = COALESCE(:gastos,GASTOS_ID) OR ENTRADAS_ID = COALESCE(:entradas,ENTRADAS_ID))"
                . "and extract(YEAR_MONTH from DATA_MOVIMENTO)=:ano_mes order by conta_id,data_movimento desc");
        $sql->execute(array(':id'=>$id_titular, ':ano_mes'=> $ano_mes,':gastos'=>$gastos, ':entradas'=> $entradas));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
   
    function lista_movimentosFiltro($id_titular, $dt_inicio, $dt_fim, $menor_valor, $maior_valor, $obs, $gastos, $receitas, $categoria, $conta){
        if($menor_valor ==''){
            $menor_valor=null;
        }
        if($maior_valor ==''){
            $maior_valor=null;
        }
        if($categoria ==''){
            $categoria=null;
        }
        if($conta ==''){
            $conta=null;
        }
        if($obs ==''){
            $obs=null;
        }else{
            $obs="%".$obs."%";
        }
        if($gastos==1){
            $gastos=null;
        }
        if($receitas==1){
            $receitas=null;
        }
        if($dt_inicio == ''){
            $dt_inicio=null;
        }else { 
            $dt_inicio =$dt_inicio.' 00:00:00';
        }
        if($dt_fim ==''){
            $dt_fim=null;
        }else { 
            $dt_fim =$dt_fim.' 23:59:59';
        }
        $sql = DAO::$db->prepare("select * from movimentos where conta_id in(SELECT id
                                                  FROM conta p 
                                                 WHERE ID_TITULAR=:id_titular) and (DATA_MOVIMENTO "
                                .">= COALESCE(:dt_inicio,DATA_MOVIMENTO) and DATA_MOVIMENTO <= COALESCE(:dt_fim,DATA_MOVIMENTO)) "
                . "AND (REPLACE(VALOR,'-','') >= COALESCE(:menor_valor,REPLACE(VALOR,'-','')) AND REPLACE(VALOR,'-','') <= COALESCE(:maior_valor,REPLACE(VALOR,'-',''))) "
                . "AND OBS LIKE(COALESCE(:obs,OBS)) "
                . "AND ( GASTOS_ID = COALESCE(:gastos, GASTOS_ID) OR ENTRADAS_ID = COALESCE(:receitas, ENTRADAS_ID)) "
                . "AND categoria = COALESCE(:categoria,categoria) AND conta_id = COALESCE(:conta,conta_id)"
                . "order by conta_id, data_movimento desc");
        $sql->execute(array(':id_titular'=> $id_titular,':dt_inicio'=> $dt_inicio,':dt_fim' => $dt_fim,':menor_valor' => $menor_valor, ':maior_valor'=>$maior_valor, ':obs'=> $obs, ':gastos'=> $gastos, ':receitas'=>$receitas, ':conta'=> $conta, ':categoria'=>$categoria));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /////TESTE
    
    function lista_movimentosTotalFiltro($id_titular, $dt_inicio, $dt_fim, $menor_valor, $maior_valor, $obs, $gastos, $receitas, $categoria, $conta){
        if($menor_valor ==''){
            $menor_valor=null;
        }
        if($maior_valor ==''){
            $maior_valor=null;
        }
        if($categoria ==''){
            $categoria=null;
        }
        if($obs ==''){
            $obs=null;
        }else{
            $obs="%".$obs."%";
        }
        if($gastos==1){
            $gastos=null;
        }
        if($receitas==1){
            $receitas=null;
        }
        if($dt_inicio == ''){
            $dt_inicio=null;
        }else { 
            $dt_inicio =$dt_inicio.' 00:00:00';
        }
        if($dt_fim ==''){
            $dt_fim=null;
        }else { 
            $dt_fim =$dt_fim.' 23:59:59';
        }
        $sql = DAO::$db->prepare("select SUM(CASE WHEN GASTOS_ID IS NOT NULL THEN VALOR ELSE 0 END) GASTOS, SUM(CASE WHEN GASTOS_ID IS NULL THEN VALOR ELSE 0 END) ENTRADAS
                    from movimentos 
                    where conta_id in
                         (SELECT id FROM conta p WHERE ID_TITULAR=:id_titular) 
                    and (DATA_MOVIMENTO "
                                .">= COALESCE(:dt_inicio,DATA_MOVIMENTO) and DATA_MOVIMENTO <= COALESCE(:dt_fim,DATA_MOVIMENTO)) "
                . "AND (REPLACE(VALOR,'-','') >= COALESCE(:menor_valor,REPLACE(VALOR,'-','')) AND REPLACE(VALOR,'-','') <= COALESCE(:maior_valor,REPLACE(VALOR,'-',''))) "
                . "AND OBS LIKE(COALESCE(:obs,OBS)) "
                . "AND ( GASTOS_ID = COALESCE(:gastos, GASTOS_ID) OR ENTRADAS_ID = COALESCE(:receitas, ENTRADAS_ID)) "
                . "AND categoria = COALESCE(:categoria,categoria) AND conta_id = COALESCE(:conta,conta_id)"
                . "order by conta_id, data_movimento desc");
        $sql->execute(array(':id_titular'=> $id_titular,':dt_inicio'=> $dt_inicio,':dt_fim' => $dt_fim,':menor_valor' => $menor_valor, ':maior_valor'=>$maior_valor, ':obs'=> $obs, ':gastos'=> $gastos, ':receitas'=>$receitas, ':conta'=> $conta, ':categoria'=>$categoria));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function lista_TotalMovimentos($ano_mes,$conta){
        
        $sql = DAO::$db->prepare("select SUM(CASE WHEN GASTOS_ID IS NOT NULL THEN VALOR ELSE 0 END) GASTOS, SUM(CASE WHEN GASTOS_ID IS NULL THEN VALOR ELSE 0 END) ENTRADAS
                                   from movimentos 
                                   where conta_id = :conta_id  "
                . "and extract(YEAR_MONTH from DATA_MOVIMENTO)=:ano_mes order by conta_id,data_movimento desc");
        $sql->execute(array(':ano_mes'=> $ano_mes, ':conta_id'=>$conta));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function lista_TotalMovimentosPDF($ano_mes,$conta,$gastos,$entradas){
        if($gastos==1){
            $gastos=null;
        }
        if($entradas==1){
            $entradas=null;
        }
        
        $sql = DAO::$db->prepare("select COALESCE(SUM(CASE WHEN GASTOS_ID IS NOT NULL THEN VALOR ELSE 0 END),0.00) GASTOS, COALESCE(SUM(CASE WHEN GASTOS_ID IS NULL THEN VALOR ELSE 0 END),0.00) ENTRADAS
                                   from movimentos 
                                   where conta_id = :conta_id AND (GASTOS_ID = COALESCE(:gastos,GASTOS_ID) OR ENTRADAS_ID = COALESCE(:entradas,ENTRADAS_ID)) "
                . "and extract(YEAR_MONTH from DATA_MOVIMENTO)=:ano_mes order by conta_id,data_movimento desc");
        $sql->execute(array(':ano_mes'=> $ano_mes, ':conta_id'=>$conta, ':gastos'=>$gastos, ':entradas'=> $entradas));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function selectGrafico($dataInicio, $dataFim, $id){
        $sql = DAO::$db->prepare("SELECT COALESCE(SUM(CASE WHEN GASTOS_ID IS NOT NULL THEN VALOR ELSE 0 END),0.00) gastos, COALESCE(SUM(CASE WHEN ENTRADAS_ID IS NOT NULL THEN VALOR ELSE 0 END),0.00) entradas "
                               . "  FROM movimentos "
                               . " WHERE DATA_MOVIMENTO >= :dt_ini and DATA_MOVIMENTO <= :dt_fim AND conta_id IN(SELECT id "
                                                                                                                    . " FROM conta "
                                                                                                                    ." WHERE id_titular = :id_titular)");
        $sql->execute(array(':dt_ini'=>$dataInicio,':dt_fim'=>$dataFim,':id_titular'=>$id));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
}