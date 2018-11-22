<?php
require_once 'DAO.php';

class DAO_AUTOMATICOS extends DAO{    
    function parcelas_personalizado($conta_id, $valor, $obs, $data, $qtd_parcelas, $tipo, $number_repeat, $tipo_repeat, $categoria,$repetir_indefinidamente){
        $sql = DAO::$db->prepare("SET @data=:data; SET @obs=:obs; SET @valor=:valor; SET @conta_id=:conta_id;"
                . "SET @qtd_parcelas=:qtd_parcelas; SET @tipo=:tipo; SET @number_repeat=:number_repeat; SET @tipo_repeat=:tipo_repeat; SET @categoria= :categoria;  SET @repeat_indefinidamente=:repeat_indefinidamente;"
                . "CALL `parcelas_personalizado`(@data, @obs, @valor, @conta_id, @qtd_parcelas, @tipo, @number_repeat, @tipo_repeat, @categoria, @repeat_indefinidamente);");
        return $sql->execute(array(':conta_id'=>$conta_id,':valor'=>$valor,':obs'=>$obs,':data'=>$data,':qtd_parcelas'=>$qtd_parcelas,':tipo'=>$tipo, ':number_repeat'=>$number_repeat, ':tipo_repeat'=>$tipo_repeat,':categoria'=> $categoria, ':repeat_indefinidamente'=>$repetir_indefinidamente));
    }
    
    function insere_automaticos($id_titular, $data){
        $sql = DAO::$db->prepare("SET @data= :data;SET @id_titular= :id_titular;"
                . "CALL `insere_automaticos`(@data,@id_titular);");
        return $sql->execute(array(':id_titular'=>$id_titular,':data'=>$data));
    }
    
    function update_parcelas_futuras($parcela, $valor,$categoria,$conta_id){
        $sql = DAO::$db->prepare("SET @parcela=:parcela; SET @valor=:valor;SET @categoria=:categoria; SET @conta=:conta;"
                               . "CALL `update_automaticos`(@parcela, @valor, @categoria, @conta);");
        return $sql->execute(array(':parcela'=>$parcela,':valor'=>$valor, ':categoria'=>$categoria, ':conta'=>$conta_id));
    }
    
    function delete_parcelas($parcela, $data){
        $sql = DAO::$db->prepare("SET @parcela=:parcela; SET @data=:data;"
                               . "CALL `delete_parcelas_automaticas`(@parcela, @data);");
        return $sql->execute(array(':parcela'=>$parcela,':data'=>$data));
    }
    
    function delete_parcelas_futuras($parcela){
        $sql = DAO::$db->prepare("SET @parcela=:parcela;"
                               . "CALL `deleta_automaticos`(@parcela);");
        return $sql->execute(array(':parcela'=>$parcela));
    }
    
    function verifica_parcelas_futuras($parcela){
        $sql = DAO::$db->prepare("SELECT COUNT(*) qtd FROM AUTOMATICOS WHERE PARCELA=:parcela ");
        $sql->execute(array(':parcela'=>$parcela));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function select_by_id($id){
        $sql = DAO::$db->prepare("SELECT * FROM AUTOMATICOS WHERE id=:id");
        $sql->execute(array(':id'=>$id));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function verificaParcelas($parcela){
        $sql = DAO::$db->prepare("SELECT (SELECT COUNT(1) contador FROM automaticos WHERE PARCELA = :parcela)+
                                         (SELECT COUNT(1) contador FROM GASTOS WHERE PARCELA=:parcela)+
                                         (SELECT COUNT(1) contador FROM ENTRADAS WHERE PARCELA=:parcela) as num_parcela");
        $sql->execute(array(':parcela'=>$parcela));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function aUltimaParcela($parcela,$data){
        $sql = DAO::$db->prepare("SELECT (SELECT COUNT(1) contador FROM automaticos WHERE PARCELA = :parcela AND data > STR_TO_DATE(:data,'%d/%m/%Y'))+
                                         (SELECT COUNT(1) contador FROM GASTOS WHERE PARCELA=:parcela AND data > STR_TO_DATE(:data,'%d/%m/%Y'))+
                                         (SELECT COUNT(1) contador FROM ENTRADAS WHERE PARCELA=:parcela AND data > STR_TO_DATE(:data,'%d/%m/%Y')) as num_parcela");
        $sql->execute(array(':parcela'=>$parcela, ':data'=>$data));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function update_to_null($parcela){
        $sql = DAO::$db->prepare("SET @parcela=:parcela;"
                               . "CALL `update_to_null`(@parcela);");
        return $sql->execute(array(':parcela'=>$parcela));
    }
    
    function list_automaticos($id_titular,$ano_mes,$tipo){
        $sql = DAO::$db->prepare("select * from automaticos where conta_id in(SELECT id
                                                                           FROM conta p 
                                                                          WHERE ID_TITULAR=:id_titular) "
                . "and extract(YEAR_MONTH from DATA)=:ano_mes and genero=:tipo order by conta_id, data desc");

        $sql->execute(array(':id_titular'=>$id_titular,':ano_mes'=>$ano_mes,':tipo'=>$tipo));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function lista_AutomaticosFiltro($id_titular, $dt_inicio, $dt_fim, $menor_valor, $maior_valor, $obs, $categoria, $conta, $genero){
        if($categoria ==''){
            $categoria=null;
        }
        if($conta ==''){
            $conta=null;
        }
        if($menor_valor ==''){
            $menor_valor=null;
        }
        if($maior_valor ==''){
            $maior_valor=null;
        }
        if($obs ==''){
            $obs=null;
        }else{
            $obs="%".$obs."%";
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
        $sql = DAO::$db->prepare("select * from automaticos 
                                   where conta_id in(SELECT id
                                                       FROM conta p 
                                                      WHERE ID_TITULAR=:id_titular) 
                   and (DATA >= COALESCE(:dt_inicio,DATA) and DATA <= COALESCE(:dt_fim,DATA))   "
                . "AND (VALOR >= COALESCE(:menor_valor,VALOR) AND VALOR <= COALESCE(:maior_valor,VALOR)) "
                . "AND OBS LIKE(COALESCE(:obs,OBS)) AND GENERO= :genero "
                . "AND categoria = COALESCE(:categoria,categoria) AND conta_id = COALESCE(:conta,conta_id) "
                . "order by conta_id, data desc");
        $sql->execute(array(':id_titular'=> $id_titular,':dt_inicio'=> $dt_inicio,':dt_fim' => $dt_fim,':menor_valor' => $menor_valor, ':maior_valor'=>$maior_valor, ':obs'=> $obs, ':conta'=> $conta, ':categoria'=>$categoria, ':genero'=>$genero));
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function select_automatico_parcela_data($parcela, $data){
        if($data ==''){
            $data=null;
        }
        $sql = DAO::$db->prepare("SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA , GENERO, VEZES_REPEAT, TIPO_REPEAT, TABELA, REPETIR_INDEFINIDAMENTE
		FROM(
		SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA , NULL AS GENERO, VEZES_REPEAT, TIPO_REPEAT, 'GASTOS' AS TABELA, REPETIR_INDEFINIDAMENTE
		FROM GASTOS 
		UNION
		SELECT
			ID, CONTA_ID , VALOR, OBS , CATEGORIA , DATA, PARCELA, GENERO, VEZES_REPEAT,TIPO_REPEAT, 'AUTOMATICOS' AS TABELA, REPEAT_INDEFINIDAMENTE AS REPETIR_INDEFINIDAMENTE
		FROM AUTOMATICOS 
		UNION
		SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA, NULL AS GENERO, VEZES_REPEAT, TIPO_REPEAT, 'ENTRADAS' AS TABELA, REPETIR_INDEFINIDAMENTE
		FROM ENTRADAS) PARCELAS
		WHERE PARCELA = :parcela
				AND data = COALESCE(:data,data)");
        $sql->execute(array(':parcela'=> $parcela,':data'=> $data));
        return $sql->fetch(PDO::FETCH_ASSOC);
        
    }
    
    function select_quantidade_automatico_parcela_data($parcela, $data){
        if($data ==''){
            $data=null;
        }
        $sql = DAO::$db->prepare("SELECT 
			count(ID) as NUMERO_PARCELAS
		FROM(
		SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA , NULL AS GENERO, VEZES_REPEAT, TIPO_REPEAT, 'GASTOS' AS TABELA, REPETIR_INDEFINIDAMENTE
		FROM GASTOS 
		UNION
		SELECT
			ID, CONTA_ID , VALOR, OBS , CATEGORIA , DATA, PARCELA, GENERO, VEZES_REPEAT,TIPO_REPEAT, 'AUTOMATICOS' AS TABELA, REPEAT_INDEFINIDAMENTE AS REPETIR_INDEFINIDAMENTE
		FROM AUTOMATICOS 
		UNION
		SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA, NULL AS GENERO, VEZES_REPEAT, TIPO_REPEAT, 'ENTRADAS' AS TABELA, REPETIR_INDEFINIDAMENTE
		FROM ENTRADAS) PARCELAS
		WHERE PARCELA = :parcela
				AND data >= COALESCE(:data,data)");
        $sql->execute(array(':parcela'=> $parcela,':data'=> $data));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function select_data_primeira_parcela($parcela, $data){
        if($data ==''){
            $data=null;
        }
        $sql = DAO::$db->prepare("SET @CONT = 0;");
        $sql->execute();
        
        $sql = DAO::$db->prepare("select DATA  from(
                SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA , GENERO, VEZES_REPEAT, TIPO_REPEAT, TABELA, REPETIR_INDEFINIDAMENTE, @CONT := @CONT+1 AS CONTADOR
		FROM(
		SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA , NULL AS GENERO, VEZES_REPEAT, TIPO_REPEAT, 'GASTOS' AS TABELA, REPETIR_INDEFINIDAMENTE
		FROM GASTOS 
		UNION
		SELECT
			ID, CONTA_ID , VALOR, OBS , CATEGORIA , DATA, PARCELA, GENERO, VEZES_REPEAT,TIPO_REPEAT, 'AUTOMATICOS' AS TABELA, REPEAT_INDEFINIDAMENTE AS REPETIR_INDEFINIDAMENTE
		FROM AUTOMATICOS 
		UNION
		SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA, NULL AS GENERO, VEZES_REPEAT, TIPO_REPEAT, 'ENTRADAS' AS TABELA, REPETIR_INDEFINIDAMENTE
		FROM ENTRADAS) PARCELAS
		WHERE PARCELA = :parcela
				AND data >= COALESCE(:data,data) order by data asc) tabela where contador = 1");
        $sql->execute(array(':parcela'=> $parcela,':data'=> $data));
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    
    function updateAutomatico($id,$data,$valor,$obs,$categoria, $conta){
        $sql = DAO::$db->prepare("UPDATE automaticos
                                    SET CONTA_ID=:conta,
                                        VALOR=:valor,
                                        DATA=:data,
                                        OBS=:obs,
                                        CATEGORIA=:categoria
                                    WHERE id = :id");
        return $sql->execute(array(':conta'=>$conta, ':categoria'=>$categoria, ':obs'=>$obs, ':valor'=>$valor, ':data'=>$data, ':id'=>$id));
    }
    
    function deleteAutomatico($id){
        $sql = DAO::$db->prepare("DELETE FROM automaticos
                                    WHERE id = :id");
        return $sql->execute(array(':id'=>$id));
    }
    
    function insertIntoAutomatico($conta, $valor, $data,$tipo, $parcela,$obs,$categoria){
        $sql = DAO::$db->prepare("INSERT INTO `automaticos` "
                . "                 (`CONTA_ID`, `VALOR`, `DATA`, `GENERO`, `NUMERO_PARCELA`, `OBS`, `CATEGORIA`, `VEZES_REPEAT`, `TIPO_REPEAT`, `PARCELA`, `REPEAT_INDEFINIDAMENTE`) VALUES "
                . "                 (:conta, :valor, :data, :tipo, '', :obs, :categoria, '1', 'MONTH', :parcela, '0')");
        return $sql->execute(array(':conta' => $conta,':valor' => $valor,':data' => $data,':tipo' => $tipo,':parcela' => $parcela,':obs' => $obs,':categoria' => $categoria));
    }
    
    function returnProximaParcela(){
        $sql = DAO::$db->prepare("
                                SELECT MAX(PARCELA)+1 PARCELA FROM(
                                SELECT COALESCE(MAX(PARCELA),0)PARCELA FROM automaticos
                                UNION
                                SELECT COALESCE(MAX(PARCELA),0) FROM gastos
                                UNION
                                SELECT COALESCE(MAX(PARCELA),0) FROM entradas)TESTE");
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
}

