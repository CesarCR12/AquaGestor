


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


CREATE TABLE Alertas (
    idAlerta INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    mensaje TEXT NOT NULL,
    fechaAlerta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);


CREATE TABLE Educacion (
    idEducacion INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    fechaPublicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE RegistroConsumoAgua (
    idConsumo INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    fecha DATE NOT NULL,
    Cantidad VARCHAR(255) NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

CREATE TABLE Reportes (
    idReporte INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    mensajeReporte TEXT NOT NULL,
    fechaReporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado BOOLEAN NOT NULL,
    FOREIGN KEY (idUsuario) REFERENCES Usuarios(idUsuario)
);

CREATE TABLE Recomendaciones (
    idRec INT AUTO_INCREMENT PRIMARY KEY,
    idUsuario INT,
    idReporte INT,
    mensajeRec TEXT NOT NULL,
    fechaRec TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
END; //

DELIMITER ;


/************/


DELIMITER //

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
END; //

DELIMITER ;


/*******************************************************************************/



/*************************************VIEWS*************************************/

/*Usuarios*/
CREATE VIEW VistaUsuariosLimitada AS
SELECT idUsuario, nombreUsuario, email, rol
FROM Usuarios
LIMIT 100;

CREATE VIEW VistaUsuariosAdmin AS
SELECT idUsuario, nombreUsuario, email
FROM Usuarios
WHERE rol = 'admin'
LIMIT 100;



/*Alertas*/
CREATE VIEW VistaAlertasRecientes AS
SELECT idAlerta, idUsuario, mensaje, fechaAlerta
FROM Alertas
WHERE fechaAlerta >= CURDATE() - INTERVAL 30 DAY
LIMIT 100;

CREATE VIEW VistaAlertasPorUsuario AS
SELECT idAlerta, idUsuario, mensaje, fechaAlerta
FROM Alertas
ORDER BY idUsuario, fechaAlerta DESC
LIMIT 100;


/*Educación*/
CREATE VIEW VistaEducacionReciente AS
SELECT idEducacion, titulo, contenido, fechaPublicacion
FROM Educacion
ORDER BY fechaPublicacion DESC
LIMIT 100;



/*Registro de Consumo de Agua*/
CREATE VIEW VistaConsumoAguaReciente AS
SELECT idConsumo, idUsuario, fecha, Cantidad, ubicacion
FROM RegistroConsumoAgua
WHERE fecha >= CURDATE() - INTERVAL 30 DAY
LIMIT 100;



/*Reportes*/
CREATE VIEW VistaReportesAbiertos AS
SELECT idReporte, idUsuario, mensajeReporte, fechaReporte, estado
FROM Reportes
WHERE estado = TRUE
LIMIT 100;

CREATE VIEW VistaReportesRecientes AS
SELECT idReporte, idUsuario, mensajeReporte, fechaReporte, estado
FROM Reportes
ORDER BY fechaReporte DESC
LIMIT 100;


/*Recomendaciones*/
CREATE VIEW VistaRecomendacionesRecientes AS
SELECT idRec, idUsuario, idReporte, mensajeRec, fechaRec
FROM Recomendaciones
ORDER BY fechaRec DESC
LIMIT 100;

CREATE VIEW VistaRecomendacionesPorReporte AS
SELECT idRec, idUsuario, idReporte, mensajeRec, fechaRec
FROM Recomendaciones
WHERE idReporte = 1 
LIMIT 100;
/*ID DE REPORTE PUEDE CAMBIAR*/



/*******************************************************************************/

/*****************************LLAMADAS A PROCEDURES*****************************/

/*Master12345?*/
CALL InsertarUsuario('masterAdmin', 'master@aquagestor.com', '$2a$14$gCv/wGAX5qiw4Zcnvo0g8eby3A4/lytkzqT.7.xXQVs9SUfFcj1fm', 'master', '../images/default-profile.png');
