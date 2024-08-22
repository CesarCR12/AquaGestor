-- Active: 1722392642883@@127.0.0.1@3306



/* Drop existing tables, triggers, database, etc. If needed.

    USE AquaGestor;
    DROP PROCEDURE IF EXISTS InsertarUsuario;
    DROP VIEW IF EXISTS VistaUsuariosLimitada;
    DROP VIEW IF EXISTS VistaUsuariosAdmin;
    DROP VIEW IF EXISTS VistaAlertasRecientes;
    DROP VIEW IF EXISTS VistaAlertasPorUsuario;
    DROP VIEW IF EXISTS VistaEducacionReciente;
    DROP VIEW IF EXISTS VistaConsumoAguaReciente;
    DROP VIEW IF EXISTS VistaReportesAbiertos;
    DROP VIEW IF EXISTS VistaReportesRecientes;
    DROP VIEW IF EXISTS VistaRecomendacionesRecientes;
    DROP VIEW IF EXISTS VistaRecomendacionesPorReporte;
    DROP TRIGGER IF EXISTS prevent_multiple_masters;
    DROP TRIGGER IF EXISTS prevent_edit_master;
    DROP TABLE IF EXISTS Educacion;
    DROP TABLE IF EXISTS Alertas;
    DROP TABLE IF EXISTS Recomendaciones;
    DROP TABLE IF EXISTS Reportes;
    DROP TABLE IF EXISTS RegistroConsumoAgua;
    DROP TABLE IF EXISTS Usuarios;
    DROP DATABASE IF EXISTS AquaGestor;
*/

CREATE DATABASE AquaGestor;
USE AquaGestor;

/*******************************TABLAS*******************************/

CREATE TABLE Usuarios (
    idUsuario INT AUTO_INCREMENT PRIMARY KEY,
    nombreUsuario VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('user', 'admin', 'master') NOT NULL DEFAULT 'user',
    fotoPerfil VARCHAR(255) NOT NULL,
    UNIQUE (nombreUsuario),
    UNIQUE (email)
);

CREATE TABLE Soporte (
    idSoporte INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    mensaje VARCHAR(255) NOT NULL,
    asunto VARCHAR(255) NOT NULL,
    fechaTicketSoporte DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    accion ENUM('Pendiente', 'Resuelto') NOT NULL DEFAULT 'Pendiente',
    estadoUsuario ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

CREATE TABLE Alertas (
    idAlerta INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    mensaje TEXT NOT NULL,
    fechaAlerta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

CREATE TABLE RegistroConsumoAgua (
    idConsumo INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    fechaConsumo DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cantidad VARCHAR(255) NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

CREATE TABLE Reportes (
    idReporte INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    mensajeReporte TEXT NOT NULL,
    fechaReporte DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('Pendiente', 'Resuelto') NOT NULL DEFAULT 'Pendiente',
    estadoUsuario ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

CREATE TABLE Recomendaciones (
    idRec INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    idReporte INT,
    mensajeRec TEXT NOT NULL,
    fechaRec DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('Pendiente', 'Resuelto') NOT NULL DEFAULT 'Pendiente',
    estadoUsuario ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario),
    FOREIGN KEY (idReporte) REFERENCES Reportes(idReporte)
);

/*******************************STORED PROCEDURES*******************************/

DELIMITER //

CREATE PROCEDURE InsertarUsuario(
    IN p_nombreUsuario VARCHAR(255),
    IN p_email VARCHAR(255),
    IN p_contrasena VARCHAR(255),
    IN p_rol ENUM('user', 'admin', 'master'),
    IN p_fotoPerfil VARCHAR(255)
)
BEGIN
    INSERT INTO Usuarios (nombreUsuario, email, contrasena, rol, fotoPerfil) 
    VALUES (p_nombreUsuario, p_email, p_contrasena, p_rol, p_fotoPerfil);
END; //

DELIMITER ;


DELIMITER $$

CREATE PROCEDURE InsertarUsuariosMasivos()
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE username VARCHAR(255);
    DECLARE email VARCHAR(255);
    DECLARE `password` VARCHAR(255);
    DECLARE role ENUM('admin', 'user');
    
    WHILE i <= 200 DO
        SET username = CONCAT('user', i);
        SET email = CONCAT('user', i, '@example.com');
        SET `password` = '$2a$14$gCv/wGAX5qiw4Zcnvo0g8eby3A4/lytkzqT.7.xXQVs9SUfFcj1fm';
        SET role = CASE
            WHEN i % 2 = 0 THEN 'admin'
            ELSE 'user'
        END;
        
        CALL InsertarUsuario(username, email, `password`, role, '../images/default-profile.png');
        
        SET i = i + 1;
    END WHILE;
END$$

CREATE PROCEDURE InsertarAlertasMasivas()
BEGIN
    DECLARE i INT DEFAULT 2;
    DECLARE numUsuarios INT;
    
    SELECT COUNT(*) INTO numUsuarios FROM Usuarios;
    
    WHILE i <= numUsuarios DO
        INSERT INTO Alertas (idUsuario, mensaje, fechaAlerta)
        VALUES (i, CONCAT('Alerta para el usuario ', i), NOW());
        
        SET i = i + 1;
    END WHILE;
END$$



CREATE PROCEDURE InsertarConsumosMasivos()
BEGIN
    DECLARE i INT DEFAULT 2;
    DECLARE numUsuarios INT;
    
    SELECT COUNT(*) INTO numUsuarios FROM Usuarios;
    
    WHILE i <= numUsuarios DO
        INSERT INTO RegistroConsumoAgua (idUsuario, cantidad, ubicacion, fechaConsumo)
        VALUES (i, CONCAT('Cantidad ', i), CONCAT('Ubicación ', i), NOW());
        
        SET i = i + 1;
    END WHILE;
END$$



CREATE PROCEDURE InsertarReportesMasivos()
BEGIN
    DECLARE i INT DEFAULT 2;
    DECLARE numUsuarios INT;
    
    SELECT COUNT(*) INTO numUsuarios FROM Usuarios;
    
    WHILE i <= numUsuarios DO
        INSERT INTO Reportes (idUsuario, mensajeReporte, fechaReporte)
        VALUES (i, CONCAT('Reporte para el usuario ', i), NOW());
        
        SET i = i + 1;
    END WHILE;
END$$


CREATE PROCEDURE InsertarRegistrosMasivos()
BEGIN
    DECLARE i INT DEFAULT 2;
    DECLARE numUsuarios INT;
    
    SELECT COUNT(*) INTO numUsuarios FROM Usuarios;
    
    WHILE i <= numUsuarios DO
        INSERT INTO Soporte (idUsuario, mensaje, asunto, fechaTicketSoporte)
        VALUES (i, CONCAT('Mensaje de soporte para el usuario ', i), CONCAT('Asunto del soporte ', i), NOW());
        
        SET i = i + 1;
    END WHILE;
END$$


CREATE PROCEDURE InsertarRecomendacionesMasivas()
BEGIN
    DECLARE i INT DEFAULT 2;
    DECLARE numUsuarios INT;
    DECLARE numReportes INT;
    
    SELECT COUNT(*) INTO numUsuarios FROM Usuarios;
    SELECT COUNT(*) INTO numReportes FROM Reportes;
    
    WHILE i <= numUsuarios DO
        IF numReportes > 0 THEN
            INSERT INTO Recomendaciones (idUsuario, idReporte, mensajeRec, fechaRec)
            VALUES (i, (i % numReportes) + 1, CONCAT('Recomendación para el usuario ', i), NOW());
        END IF;
        
        SET i = i + 1;
    END WHILE;
END$$

CREATE PROCEDURE EliminarRegistrosAntiguos()
BEGIN
    DELETE FROM Soporte
    WHERE accion = 'Resuelto'
      AND fechaTicketSoporte < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 60 DAY);


    DELETE FROM Recomendaciones
    WHERE estado = 'Resuelto'
      AND fechaRec < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 60 DAY);

    DELETE FROM Reportes
    WHERE estado = 'Resuelto'
      AND fechaReporte < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 60 DAY);

    
    DELETE FROM Alertas
    WHERE fechaAlerta < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 90 DAY);

    DELETE FROM RegistroConsumoAgua
    WHERE fechaConsumo < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 90 DAY);

    DELETE FROM Soporte
    WHERE accion = 'Pendiente'
      AND fechaTicketSoporte < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 90 DAY);

    DELETE FROM Reportes
    WHERE estado = 'Pendiente'
      AND fechaRec < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 90 DAY);

    DELETE FROM Reportes
    WHERE estado = 'Pendiente'
      AND fechaReporte < DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 90 DAY);

END$$


CREATE PROCEDURE BuscarRegistros(
    IN p_tableName VARCHAR(255),
    IN id_Usuario_current INT,
    IN p_value INT
)
BEGIN
    DECLARE offset_value INT;
    DECLARE id_current_user INT;
    DECLARE primaryKeyColumn VARCHAR(255);
    DECLARE apply_offset BOOLEAN;
	DECLARE apply_val BOOLEAN;
    
    
    IF id_Usuario_current IS NULL OR id_Usuario_current = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Id de usuario no puede ser nulo';
    END IF;

    IF p_value IS NULL OR p_value < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La cantidad a buscar no puede ser nula o menor a 0';
    END IF;
    
    IF p_value = 0 THEN
        SET apply_offset = FALSE;
        SET offset_value = 0;
    ELSE
        SET apply_offset = TRUE;
        SET offset_value = p_value - 1;
    END IF;

    SET id_current_user = id_Usuario_current;
	SET apply_val = apply_offset;

    SET @apply_val = apply_val;
    SET @id_current_user = id_current_user;
    SET @offset_value = offset_value;

    
    IF p_tableName = 'Soporte' THEN
        SET primaryKeyColumn = 'idSoporte';
    ELSEIF p_tableName = 'Alertas' THEN
        SET primaryKeyColumn = 'idAlerta';
    ELSEIF p_tableName = 'RegistroConsumoAgua' THEN
        SET primaryKeyColumn = 'idConsumo';
    ELSEIF p_tableName = 'Reportes' THEN
        SET primaryKeyColumn = 'idReporte';
    ELSEIF p_tableName = 'Recomendaciones' THEN
        SET primaryKeyColumn = 'idRec';
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Tabla no reconocida';
    END IF;
    
    SET @sql_more = '';
    IF @apply_val THEN 
        SET @sql_more = CONCAT(' ORDER BY ', primaryKeyColumn, ' LIMIT 1 OFFSET ?');
    ELSE
        SET @sql_more = CONCAT(' ORDER BY ', primaryKeyColumn);
    END IF;
    
    
    SET @sql = CONCAT(
        'SELECT * FROM ', p_tableName, 
        ' WHERE idUsuario = ?', @sql_more 
    );
    
    
    PREPARE stmt FROM @sql;
    IF @apply_val THEN
        EXECUTE stmt USING @id_current_user, @offset_value;
    ELSE
        EXECUTE stmt USING @id_current_user;
    END IF;
    DEALLOCATE PREPARE stmt;
    
END$$
DELIMITER ;


/*******************************************************************************/


/***********************************EVENTOS************************************/



/* Se puede cambiar a  EVERY X MINUTE [numero de minutos siendo X el numero] 
    Para eso dropear el evento y crearlo nuevamente con los nuevos datos

    DROP EVENT IF EXISTS EliminarRegistrosAntiguos_Event;

*/
DELIMITER ;

CREATE EVENT IF NOT EXISTS EliminarRegistrosAntiguos_Event
ON SCHEDULE EVERY 1 DAY
DO
    CALL EliminarRegistrosAntiguos();

DELIMITER ;


/*******************************************************************************/


/***********************************TRIGGERS************************************/


DELIMITER //

CREATE TRIGGER prevent_multiple_masters
BEFORE INSERT ON Usuarios
FOR EACH ROW
BEGIN
    IF NEW.rol = 'master' THEN
        IF EXISTS (SELECT 1 FROM Usuarios WHERE rol = 'master') THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo puede haber un usuario con el rol master';
        END IF;
    END IF;
END; 


CREATE TRIGGER prevent_edit_master
BEFORE UPDATE ON Usuarios
FOR EACH ROW
BEGIN
    IF NEW.rol = 'master' THEN
        IF EXISTS (SELECT 1 FROM Usuarios WHERE rol = 'master' AND idUsuario <> OLD.idUsuario) THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede asignar el rol master a más de un usuario.';
        END IF;
    END IF;

    IF OLD.rol = 'master' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede editar un usuario con el rol master.';
    END IF;
END; 

CREATE TRIGGER before_insert_reportes
BEFORE INSERT ON Reportes
FOR EACH ROW
BEGIN
    IF NEW.estado NOT IN ('Pendiente', 'Resuelto') THEN
        SET NEW.estado = 'Pendiente';
    END IF;
END;


CREATE TRIGGER update_status_on_user_delete
AFTER DELETE ON Usuarios
FOR EACH ROW
BEGIN
    UPDATE Soporte SET estadoUsuario = 'Inactivo' WHERE idUsuario = OLD.idUsuario;

    UPDATE Reportes SET estadoUsuario = 'Inactivo' WHERE idUsuario = OLD.idUsuario;

    UPDATE Recomendaciones SET estadoUsuario = 'Inactivo' WHERE idUsuario = OLD.idUsuario;
END; //

DELIMITER ;


/*******************************************************************************/



/*************************************VIEWS*************************************/

/* HAY QUE RECREARLAS */


/*******************************************************************************/

/*****************************LLAMADAS A PROCEDURES*****************************/

/*Master12345?*/
CALL InsertarUsuario('masterAdmin', 'master@aquagestor.com', '$2a$14$gCv/wGAX5qiw4Zcnvo0g8eby3A4/lytkzqT.7.xXQVs9SUfFcj1fm', 'master', '../images/default-profile.png');

CALL InsertarUsuariosMasivos();

CALL InsertarAlertasMasivas();
CALL InsertarConsumosMasivos();
CALL InsertarReportesMasivos();
CALL InsertarRegistrosMasivos();
CALL InsertarRecomendacionesMasivas();
