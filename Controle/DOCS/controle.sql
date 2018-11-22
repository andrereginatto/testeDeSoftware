-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 16-Nov-2018 às 20:25
-- Versão do servidor: 10.1.32-MariaDB
-- PHP Version: 5.6.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `controle`
--
CREATE DATABASE IF NOT EXISTS `controle` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `controle`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `deleta_automaticos`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleta_automaticos` (IN `parcela_param` INT)  BEGIN
	DECLARE PARCELA_FUTURA INT(255);
	DECLARE var_id INT(255);
	DECLARE EXISTE_MAIS_LINHAS INT DEFAULT 0;
	DECLARE CUR_PARCELAS_FUTURAS CURSOR FOR SELECT `ID`  FROM automaticos where PARCELA = parcela_param AND PARCELA IS NOT NULL;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET EXISTE_MAIS_LINHAS=1;
	
	SET PARCELA_FUTURA = (SELECT COUNT(1) FROM AUTOMATICOS WHERE PARCELA = parcela_param AND PARCELA IS NOT NULL);
	
	IF(PARCELA_FUTURA > 0) THEN
		OPEN CUR_PARCELAS_FUTURAS;
		
		meuLoop: LOOP
			FETCH CUR_PARCELAS_FUTURAS INTO var_id;
			IF EXISTE_MAIS_LINHAS = 1 THEN
				LEAVE meuLoop;
			END IF;
			
			DELETE FROM AUTOMATICOS
			WHERE PARCELA = parcela_param AND ID = var_id;
			
		END LOOP meuLoop;
	END IF;
         
END$$

DROP PROCEDURE IF EXISTS `delete_parcelas_automaticas`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_parcelas_automaticas` (INOUT `var_parcela` INT, INOUT `var_data_parcela` DATE)  BEGIN
	DECLARE var_tabela VARCHAR(20);
	DECLARE var_id INT(255);
	DECLARE EXISTE_MAIS_LINHAS INT DEFAULT 0;
	
	DECLARE CUR_PARCELAS CURSOR FOR 
		SELECT 
			ID, TABELA
		FROM(
		SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA , NULL AS GENERO, NULL AS VEZES_REPEAT, NULL AS TIPO_REPEAT, 'GASTOS' AS TABELA
		FROM GASTOS 
		UNION
		SELECT
			ID, CONTA_ID , VALOR, OBS , CATEGORIA , DATA, PARCELA, GENERO, VEZES_REPEAT,TIPO_REPEAT, 'AUTOMATICOS' AS TABELA
		FROM AUTOMATICOS 
		UNION
		SELECT 
			ID, CONTA_ID , VALOR, OBS, CATEGORIA, DATA, PARCELA, NULL AS GENERO, NULL AS VEZES_REPEAT, NULL AS TIPO_REPEAT, 'ENTRADAS' AS TABELA
		FROM ENTRADAS) PARCELAS
		WHERE PARCELA = var_parcela
				AND data >= COALESCE(var_data_parcela,data)
		ORDER BY data ASC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET EXISTE_MAIS_LINHAS=1;

	
		OPEN CUR_PARCELAS;
		
		meuLoop: LOOP
			FETCH CUR_PARCELAS INTO var_id, var_tabela;
			IF EXISTE_MAIS_LINHAS = 1 THEN
				LEAVE meuLoop;
			END IF;
			
			IF var_tabela = 'GASTOS' THEN
				DELETE FROM GASTOS
				WHERE ID = var_id;
			ELSEIF var_tabela = 'ENTRADAS' THEN
				DELETE FROM ENTRADAS
				WHERE ID = var_id;
			ELSE
				DELETE FROM AUTOMATICOS
				WHERE ID = var_id;
			END IF;
			
		END LOOP meuLoop;
         
END$$

DROP PROCEDURE IF EXISTS `insere_automaticos`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insere_automaticos` (IN `data_param` DATE, IN `var_id_titular` VARCHAR(3000))  BEGIN
	DECLARE var_id 		 	INT;
	DECLARE var_conta_id 	INT;
    DECLARE var_valor 	 	DOUBLE(50,2);
    DECLARE var_data 	 	DATE;
    DECLARE var_tipo 	 	VARCHAR(15);
	DECLARE var_tipo_repeat VARCHAR(15);
    DECLARE var_obs 	 	VARCHAR(100);
    DECLARE var_repetir  	INT(2);
    DECLARE var_parcela 	INT;
    DECLARE var_categoria   VARCHAR(20);
	DECLARE existe_mais_linhas INT DEFAULT 0;
	DECLARE var_repetir_indefinidamente  	BOOLEAN;
	DECLARE var_numero_parcela  	VARCHAR(50);
    
	-- CRIA UM CURSOR BUSCANDO APENAS PARCELAS QUE ESTÁ NA TABELA AUTOMATICOS E SÃO DA CONTA DO CARA QUE FEZ LOGIN
	DECLARE cur_automaticos CURSOR FOR SELECT `ID`, `CONTA_ID`, `VALOR`, `DATA`, `GENERO`, `OBS`, `VEZES_REPEAT`,`TIPO_REPEAT`,`PARCELA`,`CATEGORIA`,`REPEAT_INDEFINIDAMENTE`,`NUMERO_PARCELA`  FROM automaticos where CONTA_ID in(SELECT ID FROM CONTA WHERE ID_TITULAR= var_id_titular) AND DATA <=  data_param;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET existe_mais_linhas=1;
	 
	OPEN cur_automaticos;
    
    meuLoop: LOOP
		FETCH cur_automaticos INTO var_id, var_conta_id, var_valor, var_data, var_tipo, var_obs, var_repetir, var_tipo_repeat,var_parcela, var_categoria, var_repetir_indefinidamente, var_numero_parcela;
 		IF existe_mais_linhas = 1 THEN
			LEAVE meuLoop;
		END IF;
		
		-- INSERE O GASTO OU ENTRADA NA TABELA
        IF var_tipo = 'G' THEN
			INSERT INTO gastos(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
		ELSE
			INSERT INTO entradas(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
		END IF;
		
		-- VERIFICA SE O GASTO OU A ENTRADA É DO TIPO QUE REPETE INDEFINIDAMENTE, CASO FOR ELE É ATUALIZADO
		IF var_repetir_indefinidamente = TRUE THEN
			IF var_tipo_repeat = 'DAY' THEN
				SET var_data = (SELECT DATE_ADD(var_data, INTERVAL var_repetir DAY));
				WHILE var_data <= data_param DO
					IF var_tipo = 'G' THEN
						INSERT INTO gastos(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
					ELSE
						INSERT INTO entradas(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
					END IF;
					SET var_data = (SELECT DATE_ADD(var_data, INTERVAL var_repetir DAY));
				END WHILE;
				UPDATE AUTOMATICOS
				SET DATA = var_data
				WHERE ID = var_id;
			ELSEIF var_tipo_repeat = 'WEEK' THEN
				SET var_data = (SELECT DATE_ADD(var_data, INTERVAL var_repetir WEEK));
				WHILE var_data <= data_param DO
					IF var_tipo = 'G' THEN
						INSERT INTO gastos(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
					ELSE
						INSERT INTO entradas(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
					END IF;
					SET var_data = (SELECT DATE_ADD(var_data, INTERVAL var_repetir WEEK));
				END WHILE;
				UPDATE AUTOMATICOS
				SET DATA = var_data
				WHERE ID = var_id;
			ELSEIF var_tipo_repeat = 'MONTH' THEN
				SET var_data = (SELECT DATE_ADD(var_data, INTERVAL var_repetir MONTH));
				WHILE var_data <= data_param DO
					IF var_tipo = 'G' THEN
						INSERT INTO gastos(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
					ELSE
						INSERT INTO entradas(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
					END IF;
					SET var_data = (SELECT DATE_ADD(var_data, INTERVAL var_repetir MONTH));
				END WHILE;
				UPDATE AUTOMATICOS
				SET DATA = var_data
				WHERE ID = var_id;
			ELSE
				SET var_data = (SELECT DATE_ADD(var_data, INTERVAL var_repetir YEAR));
				WHILE var_data <= data_param DO
					IF var_tipo = 'G' THEN
						INSERT INTO gastos(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
					ELSE
						INSERT INTO entradas(conta_id,valor,obs,data,parcela,categoria,vezes_repeat, tipo_repeat, repetir_indefinidamente, numero_parcela) VALUES (var_conta_id,var_valor,var_obs,var_data,var_parcela,var_categoria,var_repetir, var_tipo_repeat, var_repetir_indefinidamente, var_numero_parcela);
					END IF;
					SET var_data = (SELECT DATE_ADD(var_data, INTERVAL var_repetir YEAR));
				END WHILE;
				UPDATE AUTOMATICOS
				SET DATA = var_data
				WHERE ID = var_id;
			END IF;
		ELSE
			DELETE FROM AUTOMATICOS
			WHERE ID = var_id;
		END IF;
           
	END LOOP meuLoop;	
         
END$$

DROP PROCEDURE IF EXISTS `parcelas_personalizado`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `parcelas_personalizado` (INOUT `data` DATE, INOUT `obs` VARCHAR(100), INOUT `valor` NUMERIC(50,2), INOUT `conta_id` INT, INOUT `qtd_parcelas` INT, INOUT `tipo` CHAR(1), INOUT `number_repeat` INT(2), INOUT `tipo_repeat` VARCHAR(10), INOUT `var_categoria` VARCHAR(20), INOUT `repeat_indefinidamente` BOOLEAN)  BEGIN
DECLARE cont INT;
DECLARE num_parcela INT;
DECLARE num_parcela_gastos INT;
DECLARE num_parcela_entradas INT;
	
	SET cont = 0;
	SET num_parcela = (SELECT COALESCE(MAX(PARCELA)+1,1) FROM automaticos);
	SET num_parcela_entradas = (SELECT COALESCE(MAX(PARCELA)+1,1) FROM entradas);
	SET num_parcela_gastos = (SELECT COALESCE(MAX(PARCELA)+1,1) FROM gastos);
	
	IF num_parcela < num_parcela_entradas THEN
		SET num_parcela = num_parcela_entradas;
	END IF;
	
	IF num_parcela < num_parcela_gastos THEN
		SET num_parcela = num_parcela_gastos;
	END IF;
	
	
	IF repeat_indefinidamente = TRUE THEN
		INSERT INTO automaticos( `CONTA_ID`, `VALOR`, `DATA`, `GENERO`, `OBS`, `VEZES_REPEAT`,`TIPO_REPEAT`, `PARCELA`, `CATEGORIA` , `REPEAT_INDEFINIDAMENTE`) VALUES (conta_id, valor, data, tipo, obs, number_repeat, tipo_repeat, num_parcela, var_categoria, TRUE);
	ELSE
		
		WHILE cont < qtd_parcelas DO
			IF cont <> 0 THEN
				IF tipo_repeat = 'DAY' THEN
					SET data = (SELECT DATE_ADD(data, INTERVAL number_repeat DAY));
				ELSEIF tipo_repeat = 'WEEK' THEN
					SET data = (SELECT DATE_ADD(data, INTERVAL number_repeat WEEK));
				ELSEIF tipo_repeat = 'MONTH' THEN
					SET data = (SELECT DATE_ADD(data, INTERVAL number_repeat MONTH));
				ELSE
					SET data = (SELECT DATE_ADD(data, INTERVAL number_repeat YEAR));
				END IF;
			END IF;
			INSERT INTO automaticos( `CONTA_ID`, `VALOR`, `DATA`, `GENERO`, `NUMERO_PARCELA`, `OBS`, `VEZES_REPEAT`,`TIPO_REPEAT`,`PARCELA`,`CATEGORIA`, `REPEAT_INDEFINIDAMENTE`) VALUES (conta_id,valor,data,tipo,CONCAT("(",cont+1,"/",qtd_parcelas,")"),obs,number_repeat,tipo_repeat,num_parcela, var_categoria, FALSE);
			SET cont = cont + 1;
		END WHILE;
	END IF;
END$$

DROP PROCEDURE IF EXISTS `update_automaticos`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_automaticos` (IN `parcela_param` INT, IN `valor_param` DOUBLE(50,2), IN `var_categoria` VARCHAR(20), IN `var_conta` INT)  BEGIN
	DECLARE PARCELA_FUTURA INT(255);
	DECLARE var_id INT(255);
	DECLARE EXISTE_MAIS_LINHAS INT DEFAULT 0;
	
	DECLARE CUR_PARCELAS_FUTURAS CURSOR FOR SELECT `ID`  FROM automaticos where PARCELA = parcela_param AND PARCELA IS NOT NULL;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET EXISTE_MAIS_LINHAS=1;
	
	SET PARCELA_FUTURA = (SELECT COUNT(1) FROM AUTOMATICOS WHERE PARCELA = parcela_param AND PARCELA IS NOT NULL);
	
	IF(PARCELA_FUTURA > 0) THEN
		OPEN CUR_PARCELAS_FUTURAS;
		
		meuLoop: LOOP
			FETCH CUR_PARCELAS_FUTURAS INTO var_id;
			IF EXISTE_MAIS_LINHAS = 1 THEN
				LEAVE meuLoop;
			END IF;
			
			UPDATE AUTOMATICOS
			SET VALOR = valor_param, CATEGORIA= var_categoria, CONTA_ID = var_conta
			WHERE PARCELA = parcela_param AND ID = var_id;
			
		END LOOP meuLoop;
	END IF;
         
END$$

DROP PROCEDURE IF EXISTS `update_to_null`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_to_null` (IN `parcela_param` INT)  BEGIN
	DECLARE repetir INT(10);
	DECLARE num_parcela INT;
	DECLARE num_parcela_gastos INT;
	DECLARE num_parcela_entradas INT;
	
	SET num_parcela = (SELECT COALESCE(MAX(PARCELA)+1,1) FROM automaticos);
	SET num_parcela_entradas = (SELECT COALESCE(MAX(PARCELA)+1,1) FROM entradas);
	SET num_parcela_gastos = (SELECT COALESCE(MAX(PARCELA)+1,1) FROM gastos);
	
	IF num_parcela < num_parcela_entradas THEN
		SET num_parcela = num_parcela_entradas;
	END IF;
	
	IF num_parcela < num_parcela_gastos THEN
		SET num_parcela = num_parcela_gastos;
	END IF;	
	
	SET repetir = (SELECT REPEAT_INDEFINIDAMENTE FROM automaticos WHERE parcela=parcela_param LIMIT 1);
	
	UPDATE gastos
	SET parcela = num_parcela
	where parcela = parcela_param;
	
	IF (repetir <> 1) THEN
		UPDATE automaticos
		SET parcela = parcela_param+1
		where parcela = parcela_param;
    END IF;
	
END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `teste`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `teste` () RETURNS DATE NO SQL
    DETERMINISTIC
begin
DECLARE primeirodia int(100);
DECLARE anoMes varchar(100);
DECLARE dia_da_semana  int(100);
DECLARE diferenca  int(100);

SET dia_da_semana = (select DATE_FORMAT('2018/11/11','%w'))+1;

IF(dia_da_semana <> 7)THEN
	SET diferenca = 7-dia_da_semana;
END IF;

return (select '2018/11/11' || '2018/11/11' +interval diferenca day);
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `automaticos`
--

DROP TABLE IF EXISTS `automaticos`;
CREATE TABLE `automaticos` (
  `ID` int(11) NOT NULL,
  `CONTA_ID` int(11) NOT NULL,
  `VALOR` double(50,2) NOT NULL,
  `DATA` date DEFAULT NULL,
  `GENERO` char(1) DEFAULT NULL,
  `NUMERO_PARCELA` varchar(50) NOT NULL,
  `OBS` varchar(100) NOT NULL,
  `CATEGORIA` varchar(20) NOT NULL,
  `VEZES_REPEAT` int(2) NOT NULL DEFAULT '0',
  `TIPO_REPEAT` varchar(10) DEFAULT NULL,
  `PARCELA` int(255) NOT NULL,
  `REPEAT_INDEFINIDAMENTE` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `automaticos`
--

INSERT INTO `automaticos` (`ID`, `CONTA_ID`, `VALOR`, `DATA`, `GENERO`, `NUMERO_PARCELA`, `OBS`, `CATEGORIA`, `VEZES_REPEAT`, `TIPO_REPEAT`, `PARCELA`, `REPEAT_INDEFINIDAMENTE`) VALUES
(363, 53, 100.00, '2018-12-01', 'G', '(10/10)', 'Tenis de patão', 'Sem Categoria', 1, 'MONTH', 1, 0),
(365, 53, 3600.00, '2018-12-01', 'E', '', 'Salário', 'Sem Categoria', 1, 'MONTH', 3, 1),
(366, 53, 1670.00, '2018-12-01', 'E', '', 'Salário', 'Mercado', 1, 'MONTH', 4, 1),
(367, 53, 100.00, '2018-11-25', 'E', '', 'dsadasd', 'Sem Categoria', 1, 'MONTH', 5, 0),
(368, 53, 21123.00, '2018-11-21', 'E', '', 'dasdasd', 'Sem Categoria', 1, 'MONTH', 6, 0),
(369, 53, 50.00, '2018-11-25', 'G', '', 'dsadasd', 'Sem Categoria', 1, 'MONTH', 7, 0),
(370, 53, 132213.00, '2018-11-25', 'E', '', 'adsadsa', 'Sem Categoria', 1, 'MONTH', 8, 0),
(371, 53, 1200.00, '2018-11-25', 'E', '', 'dasdasd', 'Sem Categoria', 1, 'MONTH', 9, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `ID` int(11) NOT NULL,
  `CATEGORIA` varchar(20) NOT NULL,
  `QUEM_CRIOU` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`ID`, `CATEGORIA`, `QUEM_CRIOU`) VALUES
(3, 'Almoço', 13),
(4, 'Janta', 13),
(7, 'Minha categoria', 14),
(8, 'Posto de Gasolina', 0),
(9, 'Mercado', 0),
(10, 'Mercado', 13),
(12, 'Teste', 21);

-- --------------------------------------------------------

--
-- Estrutura da tabela `conta`
--

DROP TABLE IF EXISTS `conta`;
CREATE TABLE `conta` (
  `ID` int(11) NOT NULL,
  `NOME_TITULAR` varchar(80) NOT NULL,
  `SALDO` double(50,2) DEFAULT '0.00',
  `ID_TITULAR` int(11) NOT NULL,
  `NOME_CONTA` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `conta`
--

INSERT INTO `conta` (`ID`, `NOME_TITULAR`, `SALDO`, `ID_TITULAR`, `NOME_CONTA`) VALUES
(53, 'André', 2673.00, 21, 'Banrisul'),
(54, 'Teste', 0.00, 22, 'dasdsadasdsa');

-- --------------------------------------------------------

--
-- Estrutura da tabela `entradas`
--

DROP TABLE IF EXISTS `entradas`;
CREATE TABLE `entradas` (
  `ID` int(11) NOT NULL,
  `CONTA_ID` int(11) NOT NULL,
  `VALOR` double(50,2) NOT NULL,
  `NUMERO_PARCELA` varchar(50) NOT NULL,
  `OBS` varchar(100) NOT NULL,
  `CATEGORIA` varchar(20) NOT NULL,
  `DATA` date NOT NULL,
  `PARCELA` int(255) DEFAULT NULL,
  `VEZES_REPEAT` int(2) NOT NULL,
  `TIPO_REPEAT` varchar(10) NOT NULL,
  `REPETIR_INDEFINIDAMENTE` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `entradas`
--

INSERT INTO `entradas` (`ID`, `CONTA_ID`, `VALOR`, `NUMERO_PARCELA`, `OBS`, `CATEGORIA`, `DATA`, `PARCELA`, `VEZES_REPEAT`, `TIPO_REPEAT`, `REPETIR_INDEFINIDAMENTE`) VALUES
(17, 53, 1600.00, '', 'Salário', 'Sem Categoria', '2018-09-01', 2, 1, 'MONTH', 1),
(20, 53, 1670.00, '', 'Salário', 'Mercado', '2018-10-01', 4, 1, 'MONTH', 1),
(21, 53, 1650.00, '', 'Salário', 'Mercado', '2018-11-01', 4, 1, 'MONTH', 1),
(23, 53, 150.00, '', 'dasda', 'Sem Categoria', '2018-10-19', NULL, 0, '', 0),
(24, 53, 213.00, '', 'dasdas', 'Sem Categoria', '2018-10-14', NULL, 0, '', 0),
(25, 53, 1200.00, '', 'dasda', 'Sem Categoria', '2018-10-25', NULL, 0, '', 0),
(26, 53, 900.00, '', 'dasdas', 'Sem Categoria', '2018-10-07', NULL, 0, '', 0);

--
-- Acionadores `entradas`
--
DROP TRIGGER IF EXISTS `delete_entrada`;
DELIMITER $$
CREATE TRIGGER `delete_entrada` BEFORE DELETE ON `entradas` FOR EACH ROW BEGIN       
    DECLARE SALDO_ATUAL NUMERIC(50,2);
	
    SET SALDO_ATUAL = (SELECT SALDO FROM `CONTA` WHERE id = OLD.conta_id);
    SET SALDO_ATUAL = SALDO_ATUAL-OLD.VALOR;
    
    UPDATE CONTA
    SET SALDO = SALDO_ATUAL
    WHERE ID=OLD.CONTA_ID;
    
    DELETE FROM movimentos
    WHERE ENTRADAS_ID= OLD.ID AND CONTA_ID = OLD.CONTA_ID;
    
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `insert_entrada`;
DELIMITER $$
CREATE TRIGGER `insert_entrada` AFTER INSERT ON `entradas` FOR EACH ROW BEGIN       
    DECLARE SALDO_ATUAL NUMERIC(50,2);
        
    SET SALDO_ATUAL = (SELECT SALDO FROM `CONTA` WHERE id = NEW.conta_id);
    
    SET SALDO_ATUAL = SALDO_ATUAL+NEW.VALOR;
    
    UPDATE CONTA
    SET SALDO = SALDO_ATUAL
    WHERE ID=NEW.CONTA_ID;
    
	INSERT INTO MOVIMENTOS (CONTA_ID, ENTRADAS_ID, DATA_MOVIMENTO, VALOR, OBS,CATEGORIA, NUMERO_PARCELA) VALUES (NEW.conta_id, NEW.id, new.data, NEW.VALOR, NEW.OBS, NEW.CATEGORIA, NEW.NUMERO_PARCELA);
    
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `update_entrada`;
DELIMITER $$
CREATE TRIGGER `update_entrada` AFTER UPDATE ON `entradas` FOR EACH ROW BEGIN       
    DECLARE SALDO_ATUAL NUMERIC(50,2);
	DECLARE SALDO_ATUAL_CONTA_ANTIGA NUMERIC(50,2);
	DECLARE DIFERENCA NUMERIC(50,2);
	
	IF (NEW.VALOR <> OLD.VALOR) OR (NEW.DATA <> OLD.DATA) OR (NEW.OBS <> OLD.OBS) OR (NEW.CATEGORIA <> OLD.CATEGORIA) THEN
		IF (new.conta_id = old.conta_id) THEN
			SET SALDO_ATUAL = (SELECT SALDO FROM `CONTA` WHERE id = NEW.conta_id);
			SET DIFERENCA = (SELECT IF(old.valor < new.valor, new.valor-old.valor, old.valor-new.valor));
			SET SALDO_ATUAL = (SELECT IF(old.valor > new.valor, SALDO_ATUAL-DIFERENCA, SALDO_ATUAL+DIFERENCA));
			
			UPDATE CONTA
			SET SALDO = SALDO_ATUAL
			WHERE ID=NEW.CONTA_ID;
			
			UPDATE MOVIMENTOS
			SET VALOR=NEW.VALOR,
				DATA_MOVIMENTO= NEW.DATA,
				OBS = NEW.OBS, 
				CATEGORIA = NEW.CATEGORIA 
			WHERE ENTRADAS_ID = NEW.ID;
		ELSE
			SET SALDO_ATUAL = (SELECT SALDO FROM `CONTA` WHERE id = NEW.conta_id);
			SET SALDO_ATUAL_CONTA_ANTIGA = (SELECT SALDO FROM `CONTA` WHERE id = OLD.conta_id);
			
			UPDATE CONTA
			SET SALDO = SALDO - OLD.VALOR
			WHERE ID = OLD.CONTA_ID;
			
			UPDATE CONTA
			SET SALDO = SALDO + NEW.VALOR
			WHERE ID = NEW.CONTA_ID;
			
			UPDATE MOVIMENTOS
			SET VALOR= NEW.VALOR,
				DATA_MOVIMENTO= NEW.DATA,
				OBS = NEW.OBS, 
				CATEGORIA = NEW.CATEGORIA,
				CONTA_ID = NEW.CONTA_ID
			WHERE ENTRADAS_ID = NEW.ID;
			
		END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `gastos`
--

DROP TABLE IF EXISTS `gastos`;
CREATE TABLE `gastos` (
  `ID` int(11) NOT NULL,
  `CONTA_ID` int(11) NOT NULL,
  `VALOR` double(50,2) NOT NULL,
  `NUMERO_PARCELA` varchar(50) NOT NULL,
  `OBS` varchar(100) NOT NULL,
  `CATEGORIA` varchar(20) NOT NULL,
  `DATA` date NOT NULL,
  `PARCELA` int(255) DEFAULT NULL,
  `VEZES_REPEAT` int(2) NOT NULL,
  `TIPO_REPEAT` varchar(10) NOT NULL,
  `REPETIR_INDEFINIDAMENTE` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `gastos`
--

INSERT INTO `gastos` (`ID`, `CONTA_ID`, `VALOR`, `NUMERO_PARCELA`, `OBS`, `CATEGORIA`, `DATA`, `PARCELA`, `VEZES_REPEAT`, `TIPO_REPEAT`, `REPETIR_INDEFINIDAMENTE`) VALUES
(103, 53, 100.00, '(1/10)', 'Tenis de patão', 'Sem Categoria', '2018-03-01', 1, 1, 'MONTH', 0),
(104, 53, 100.00, '(2/10)', 'Tenis de patão', 'Sem Categoria', '2018-04-01', 1, 1, 'MONTH', 0),
(105, 53, 100.00, '(3/10)', 'Tenis de patão', 'Sem Categoria', '2018-05-01', 1, 1, 'MONTH', 0),
(106, 53, 100.00, '(4/10)', 'Tenis de patão', 'Sem Categoria', '2018-06-01', 1, 1, 'MONTH', 0),
(107, 53, 100.00, '(5/10)', 'Tenis de patão', 'Sem Categoria', '2018-07-01', 1, 1, 'MONTH', 0),
(108, 53, 100.00, '(6/10)', 'Tenis de patão', 'Sem Categoria', '2018-08-01', 1, 1, 'MONTH', 0),
(109, 53, 100.00, '(7/10)', 'Tenis de patão', 'Sem Categoria', '2018-09-01', 1, 1, 'MONTH', 0),
(110, 53, 100.00, '(8/10)', 'Tenis de patão', 'Sem Categoria', '2018-10-01', 1, 1, 'MONTH', 0),
(111, 53, 110.00, '(9/10)', 'Tenis de patão', 'Sem Categoria', '2018-11-01', 1, 1, 'MONTH', 0),
(112, 53, 1000.00, '', 'dasdad', 'Sem Categoria', '2018-10-01', NULL, 0, '', 0),
(113, 53, 1900.00, '', 'dasdas', 'Sem Categoria', '2018-10-08', NULL, 0, '', 0),
(114, 53, 900.00, '', 'dasdas', 'Sem Categoria', '2018-10-26', NULL, 0, '', 0);

--
-- Acionadores `gastos`
--
DROP TRIGGER IF EXISTS `delete_gasto`;
DELIMITER $$
CREATE TRIGGER `delete_gasto` BEFORE DELETE ON `gastos` FOR EACH ROW BEGIN       
    DECLARE SALDO_ATUAL NUMERIC(50,2);
	
    SET SALDO_ATUAL = (SELECT SALDO FROM `CONTA` WHERE id = OLD.conta_id);
    SET SALDO_ATUAL = SALDO_ATUAL+OLD.VALOR;
    
    UPDATE CONTA
    SET SALDO = SALDO_ATUAL
    WHERE ID=OLD.CONTA_ID;
    
    DELETE FROM movimentos
    WHERE GASTOS_ID= OLD.ID AND CONTA_ID = OLD.CONTA_ID;
    
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `insert_gasto`;
DELIMITER $$
CREATE TRIGGER `insert_gasto` AFTER INSERT ON `gastos` FOR EACH ROW BEGIN       
    DECLARE SALDO_ATUAL NUMERIC(50,2);
    
    SET SALDO_ATUAL = (SELECT SALDO FROM `CONTA` WHERE id = NEW.conta_id);
    
    SET SALDO_ATUAL = SALDO_ATUAL-NEW.VALOR;
    
    UPDATE CONTA
    SET SALDO = SALDO_ATUAL
    WHERE ID=NEW.CONTA_ID;
    
	INSERT INTO MOVIMENTOS (CONTA_ID, GASTOS_ID, DATA_MOVIMENTO, VALOR, OBS, CATEGORIA, NUMERO_PARCELA) VALUES (NEW.conta_id, NEW.id, NEW.data, concat('-',NEW.VALOR), NEW.OBS, NEW.CATEGORIA, NEW.NUMERO_PARCELA);
    
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `update_gasto`;
DELIMITER $$
CREATE TRIGGER `update_gasto` AFTER UPDATE ON `gastos` FOR EACH ROW BEGIN       
    DECLARE SALDO_ATUAL NUMERIC(50,2);
	DECLARE SALDO_ATUAL_CONTA_ANTIGA NUMERIC(50,2);
	DECLARE DIFERENCA NUMERIC(50,2);
	
	IF (NEW.VALOR <> OLD.VALOR) OR (NEW.DATA <> OLD.DATA) OR (NEW.OBS <> OLD.OBS) OR (NEW.CATEGORIA <> OLD.CATEGORIA) THEN
		IF (new.conta_id = old.conta_id) THEN
			SET SALDO_ATUAL = (SELECT SALDO FROM `CONTA` WHERE id = NEW.conta_id);
			SET DIFERENCA = (SELECT IF(old.valor < new.valor, new.valor-old.valor, old.valor-new.valor));
			SET SALDO_ATUAL = (SELECT IF(old.valor > new.valor, SALDO_ATUAL+DIFERENCA, SALDO_ATUAL-DIFERENCA));
			
			UPDATE CONTA
			SET SALDO = SALDO_ATUAL
			WHERE ID=NEW.CONTA_ID;
			
			UPDATE MOVIMENTOS
			SET VALOR=CONCAT("-",NEW.VALOR),
				DATA_MOVIMENTO= NEW.DATA,
				OBS = NEW.OBS, 
				CATEGORIA = NEW.CATEGORIA 
			WHERE GASTOS_ID = NEW.ID;
		ELSE
			SET SALDO_ATUAL = (SELECT SALDO FROM `CONTA` WHERE id = NEW.conta_id);
			SET SALDO_ATUAL_CONTA_ANTIGA = (SELECT SALDO FROM `CONTA` WHERE id = OLD.conta_id);
			
			UPDATE CONTA
			SET SALDO = SALDO + OLD.VALOR
			WHERE ID = OLD.CONTA_ID;
			
			UPDATE CONTA
			SET SALDO = SALDO - NEW.VALOR
			WHERE ID = NEW.CONTA_ID;
			
			UPDATE MOVIMENTOS
			SET VALOR=CONCAT("-",NEW.VALOR),
				DATA_MOVIMENTO= NEW.DATA,
				OBS = NEW.OBS, 
				CATEGORIA = NEW.CATEGORIA,
				CONTA_ID = NEW.CONTA_ID
			WHERE GASTOS_ID = NEW.ID;
		END IF;
		
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `movimentos`
--

DROP TABLE IF EXISTS `movimentos`;
CREATE TABLE `movimentos` (
  `ID` int(11) NOT NULL,
  `CONTA_ID` int(11) DEFAULT NULL,
  `GASTOS_ID` int(11) DEFAULT NULL,
  `ENTRADAS_ID` int(11) DEFAULT NULL,
  `DATA_MOVIMENTO` date DEFAULT NULL,
  `NUMERO_PARCELA` varchar(50) NOT NULL,
  `OBS` varchar(100) NOT NULL,
  `CATEGORIA` varchar(20) NOT NULL,
  `VALOR` double(50,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `movimentos`
--

INSERT INTO `movimentos` (`ID`, `CONTA_ID`, `GASTOS_ID`, `ENTRADAS_ID`, `DATA_MOVIMENTO`, `NUMERO_PARCELA`, `OBS`, `CATEGORIA`, `VALOR`) VALUES
(119, 53, 103, NULL, '2018-03-01', '(1/10)', 'Tenis de patão', 'Sem Categoria', -100.00),
(120, 53, 104, NULL, '2018-04-01', '(2/10)', 'Tenis de patão', 'Sem Categoria', -100.00),
(121, 53, 105, NULL, '2018-05-01', '(3/10)', 'Tenis de patão', 'Sem Categoria', -100.00),
(122, 53, 106, NULL, '2018-06-01', '(4/10)', 'Tenis de patão', 'Sem Categoria', -100.00),
(123, 53, 107, NULL, '2018-07-01', '(5/10)', 'Tenis de patão', 'Sem Categoria', -100.00),
(124, 53, 108, NULL, '2018-08-01', '(6/10)', 'Tenis de patão', 'Sem Categoria', -100.00),
(125, 53, 109, NULL, '2018-09-01', '(7/10)', 'Tenis de patão', 'Sem Categoria', -100.00),
(126, 53, 110, NULL, '2018-10-01', '(8/10)', 'Tenis de patão', 'Sem Categoria', -100.00),
(127, 53, 111, NULL, '2018-11-01', '(9/10)', 'Tenis de patão', 'Sem Categoria', -110.00),
(128, 53, NULL, 17, '2018-09-01', '', 'Salário', 'Sem Categoria', 1600.00),
(131, 53, NULL, 20, '2018-10-01', '', 'Salário', 'Mercado', 1670.70),
(132, 53, NULL, 21, '2018-11-01', '', 'Salário', 'Mercado', 1650.00),
(134, 53, NULL, 23, '2018-10-19', '', 'dasda', 'Sem Categoria', 150.00),
(135, 53, NULL, 24, '2018-10-14', '', 'dasdas', 'Sem Categoria', 213.00),
(136, 53, NULL, 25, '2018-10-25', '', 'dasda', 'Sem Categoria', 1200.00),
(137, 53, NULL, 26, '2018-10-07', '', 'dasdas', 'Sem Categoria', 900.00),
(138, 53, 112, NULL, '2018-10-01', '', 'dasdad', 'Sem Categoria', -1000.00),
(139, 53, 113, NULL, '2018-10-08', '', 'dasdas', 'Sem Categoria', -1900.00),
(140, 53, 114, NULL, '2018-10-26', '', 'dasdas', 'Sem Categoria', -900.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pessoa`
--

DROP TABLE IF EXISTS `pessoa`;
CREATE TABLE `pessoa` (
  `ID` int(11) NOT NULL,
  `NOME` varchar(40) NOT NULL,
  `SOBRENOME` varchar(40) DEFAULT NULL,
  `TELEFONE` varchar(15) NOT NULL,
  `IMG` varchar(100) DEFAULT NULL,
  `SEXO` char(1) DEFAULT NULL,
  `EMAIL` varchar(40) NOT NULL,
  `SENHA` varchar(255) DEFAULT NULL,
  `ANIVERSARIO` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `pessoa`
--

INSERT INTO `pessoa` (`ID`, `NOME`, `SOBRENOME`, `TELEFONE`, `IMG`, `SEXO`, `EMAIL`, `SENHA`, `ANIVERSARIO`) VALUES
(21, 'André', 'Reginatto', '(54) 99618-1154', 'img/usuarios/h1.png', 'M', 'andre.reginatto@hotmail.com', '$2y$10$khtdEQHUNeoqjCBFpY0BlesJBLHue7JZaAu6dJMxxQ85xV5C/1CVq', '1998-02-11'),
(22, 'Teste', '', '(54) 99999-9999', 'img/usuarios/h2.png', 'M', 'exemplo@email.com', '$2y$10$BtZ2OrLdVGcdnld6SzpOiemnuQoXPp6JDUDPG4JmE55JwSzv.ThO6', '1998-02-11');

--
-- Acionadores `pessoa`
--
DROP TRIGGER IF EXISTS `DELETE_CONTA`;
DELIMITER $$
CREATE TRIGGER `DELETE_CONTA` BEFORE DELETE ON `pessoa` FOR EACH ROW BEGIN
    
    DELETE FROM conta
    WHERE ID_TITULAR=OLD.ID;

/* apagando pendencias, entradas, gastos */

    DELETE FROM automaticos
    WHERE CONTA_ID NOT IN (SELECT ID FROM CONTA);

    DELETE FROM entradas
    WHERE CONTA_ID NOT IN (SELECT ID FROM CONTA);

    DELETE FROM gastos
    WHERE CONTA_ID NOT IN (SELECT ID FROM CONTA);


END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `automaticos`
--
ALTER TABLE `automaticos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `salario_ibfk_1` (`CONTA_ID`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `conta`
--
ALTER TABLE `conta`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_contaaa` (`ID_TITULAR`);

--
-- Indexes for table `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `entradas_ibfk_1` (`CONTA_ID`);

--
-- Indexes for table `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `gastos_ibfk_1` (`CONTA_ID`);

--
-- Indexes for table `movimentos`
--
ALTER TABLE `movimentos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `movimentos_ibfk_1` (`CONTA_ID`),
  ADD KEY `movimentos_ibfk_2` (`ENTRADAS_ID`),
  ADD KEY `movimentos_ibfk_3` (`GASTOS_ID`);

--
-- Indexes for table `pessoa`
--
ALTER TABLE `pessoa`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`EMAIL`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `automaticos`
--
ALTER TABLE `automaticos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=372;

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `conta`
--
ALTER TABLE `conta`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `entradas`
--
ALTER TABLE `entradas`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `gastos`
--
ALTER TABLE `gastos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `movimentos`
--
ALTER TABLE `movimentos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `pessoa`
--
ALTER TABLE `pessoa`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `automaticos`
--
ALTER TABLE `automaticos`
  ADD CONSTRAINT `automaticos_ibfk_1` FOREIGN KEY (`CONTA_ID`) REFERENCES `conta` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `conta`
--
ALTER TABLE `conta`
  ADD CONSTRAINT `fk_contaaa` FOREIGN KEY (`ID_TITULAR`) REFERENCES `pessoa` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_ibfk_1` FOREIGN KEY (`CONTA_ID`) REFERENCES `conta` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`CONTA_ID`) REFERENCES `conta` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `movimentos`
--
ALTER TABLE `movimentos`
  ADD CONSTRAINT `movimentos_ibfk_1` FOREIGN KEY (`CONTA_ID`) REFERENCES `conta` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movimentos_ibfk_2` FOREIGN KEY (`ENTRADAS_ID`) REFERENCES `entradas` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `movimentos_ibfk_3` FOREIGN KEY (`GASTOS_ID`) REFERENCES `gastos` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
