--**********Creacion de la base de datos*****************

-- Nos conectamos a "master" que almacena todas las bases de datos del sistema.
USE master
GO

-- Verificamos que no exista la nueva base de datos
IF NOT EXISTS (
    SELECT [name]
        FROM sys.databases
        WHERE [name] = N'GesconDatabase'
)
-- Crearemos nuestra base de datos "GesconDatabase".
CREATE DATABASE GesconDatabase
USE GesconDatabase
GO

--****************CREACION DE TABLAS********************
--Limpieamos la base de datos para poder correr reiteradas veces el codigo
DROP TABLE IF EXISTS Envio_Articulo;
DROP TABLE IF EXISTS Topicos_Articulos;
DROP TABLE IF EXISTS Especialidades_Revisores;
DROP TABLE IF EXISTS Revision;
DROP TABLE IF EXISTS Especialidad_Topicos;
DROP TABLE IF EXISTS Articulos;
DROP TABLE IF EXISTS Revisores;
DROP TABLE IF EXISTS Autores;

--******************CLASES FUERTES**********************
--TABLA Autores: Son los autores que escribieron o fueron participes en algun articulo.
CREATE TABLE Autores (
    rut_autor VARCHAR(9) NOT NULL PRIMARY KEY,
    nombre_autores VARCHAR(50) NOT NULL,
    correo_autores VARCHAR(100),
    CONSTRAINT check_correo_a CHECK (correo_autores LIKE '%_@_%._%')
);

--TABLA Revisores: Son los encargados de revisar cada articulo, y son asignados mediante las especialidades.
CREATE TABLE Revisores (
    rut_revisor VARCHAR(9) NOT NULL PRIMARY KEY,
    nombre_revisores VARCHAR(50) NOT NULL,
    correo_revisores VARCHAR(100),
    CONSTRAINT check_correo_r CHECK (correo_revisores LIKE '%_@_%._%')
);

--TABLA Articulos: Son articulos los articulos que los revisores miraran, los autores escriben los articulos segun ciertos topicos
CREATE TABLE Articulos (
    id_articulo INT NOT NULL PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    fecha_envio DATE NOT NULL,
    resumen VARCHAR(150) NOT NULL
);

--TABLA Especialidad_Topicos: Seran los topicos que se tendra disponible para las especialidades.
CREATE TABLE Especialidad_Topicos (
    id_especialidad_topico INT NOT NULL PRIMARY KEY,
    tipo VARCHAR(35) NOT NULL,
    descripcion VARCHAR(100) NOT NULL
);

--*******************CLASES DEBILES**********************
--TABLA Revision: clase asociativa para la relacion M;N entre revisores y autores
CREATE TABLE Revision (
    id_articulo INT NOT NULL,
    rut_revisor VARCHAR(9) NOT NULL,
    PRIMARY KEY (id_articulo, rut_revisor),
    FOREIGN KEY (id_articulo) REFERENCES Articulos(id_articulo),
    FOREIGN KEY (rut_revisor) REFERENCES Revisores(rut_revisor)
);


--TABLA Especialidades_Revisores: clase asociativa para manejar la relacion M;N entre especialidades_topicos y revisores
CREATE TABLE Especialidades_Revisores (
    rut_revisor VARCHAR(9) NOT NULL,
    id_especialidad_topico INT NOT NULL,
    PRIMARY KEY (rut_revisor, id_especialidad_topico),
    FOREIGN KEY (rut_revisor) REFERENCES Revisores(rut_revisor),
    FOREIGN KEY (id_especialidad_topico) REFERENCES Especialidad_Topicos(id_especialidad_topico)
);

--TABLA Topicos_articulos: clase asociativa para manejar la relacion M;N entre especialidades_topicos y articulos
CREATE TABLE Topicos_Articulos (
    id_articulo INT NOT NULL,
    id_especialidad_topico INT NOT NULL,
    PRIMARY KEY (id_articulo, id_especialidad_topico),
    FOREIGN KEY (id_articulo) REFERENCES Articulos(id_articulo),
    FOREIGN KEY (id_especialidad_topico) REFERENCES Especialidad_Topicos(id_especialidad_topico)
);

--TABLA Envio_Articulo: Clase asociativa para manejar la relacion M;N de autores y articulos. ademas de guardar los datos del autor de contacto
CREATE TABLE Envio_Articulo (
    rut_autor VARCHAR(9) NOT NULL,
    id_articulo INT NOT NULL,
    correo_contacto VARCHAR(100),
    userid_contacto VARCHAR(50),
    password_contacto VARBINARY(128),
    PRIMARY KEY (rut_autor, id_articulo),
    FOREIGN KEY (rut_autor) REFERENCES Autores(rut_autor),
    FOREIGN KEY (id_articulo) REFERENCES Articulos (id_articulo)
);
