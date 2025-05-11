/* *****************Creacion de la base de datos***************** */
-- Verificamos que no exista la nueva base de datos (en MySQL)
CREATE DATABASE IF NOT EXISTS GesconDatabase;

-- Nos conectamos a la base de datos recién creada
USE GesconDatabase;

/* ****************CREACION DE TABLAS******************** */
-- Limpieamos la base de datos para poder correr reiteradas veces el codigo
DROP TABLE IF EXISTS Envio_Articulo;
DROP TABLE IF EXISTS Topicos_Articulos;
DROP TABLE IF EXISTS Especialidad_Revisores;
DROP TABLE IF EXISTS Revision;
DROP TABLE IF EXISTS Especialidad_Topico;
DROP TABLE IF EXISTS Articulos;
DROP TABLE IF EXISTS Revisores;
DROP TABLE IF EXISTS Autores;

/* ******************CLASES FUERTES********************** */
-- TABLA Autores: Son los autores que escribieron o fueron participes en algun articulo.
CREATE TABLE Autores (
    rut_autor VARCHAR(9) NOT NULL PRIMARY KEY,
    nombre_autor VARCHAR(50) NOT NULL,
    correo_autor VARCHAR(100) NOT NULL,
    rol_autor VARCHAR(25),
    usuario_autor VARCHAR(35),
    contraseña_autor VARBINARY(255),
    CONSTRAINT check_correo_a CHECK (correo_autor LIKE '%@%.%') 
);

-- TABLA Revisores: Son los encargados de revisar cada articulo, y son asignados mediante las especialidades.
CREATE TABLE Revisores (
    rut_revisor VARCHAR(9) NOT NULL PRIMARY KEY,
    nombre_revisor VARCHAR(50) NOT NULL,
    correo_revisor VARCHAR(100) NOT NULL,
    rol_revisor VARCHAR(25),
    usuario_revisor VARCHAR(35),
    contraseña_revisor VARBINARY(255),
    CONSTRAINT check_correo_r CHECK (correo_revisor LIKE '%@%.%') 
);

-- TABLA Articulos: Son articulos los articulos que los revisores miraran, los autores escriben los articulos segun ciertos topicos
CREATE TABLE Articulos (
    id_articulo INT NOT NULL PRIMARY KEY, 
    titulo VARCHAR(100) NOT NULL,
    fecha_envio DATE NOT NULL,
    resumen VARCHAR(150) NOT NULL,
    estado VARCHAR(25),
);

-- TABLA Especialidad_Topicos: Seran los topicos que se tendra disponible para las especialidades.
CREATE TABLE Especialidad_Topico (
    -- en caso de querer agregar mas topicos, con el auto increment se creara una PK automaticamente
    id_especialidad_topico INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    tipo VARCHAR(35) NOT NULL,
    descripcion VARCHAR(100) NOT NULL
);

/* *******************CLASES DEBILES********************** */
-- TABLA Revision: clase asociativa para la relacion M;N entre revisores y autores
CREATE TABLE Revision (
    id_articulo INT NOT NULL,
    rut_revisor VARCHAR(9) NOT NULL,
    fecha_revision DATE,
    comentarios VARCHAR(150),
    calificacion TINYINT,
    PRIMARY KEY (id_articulo, rut_revisor),
    FOREIGN KEY (id_articulo) REFERENCES Articulos(id_articulo) ON DELETE CASCADE, 
    FOREIGN KEY (rut_revisor) REFERENCES Revisores(rut_revisor) ON DELETE CASCADE 
);

-- TABLA Especialidades_Revisores: clase asociativa para manejar la relacion M;N entre especialidades_topicos y revisores
CREATE TABLE Especialidad_Revisores (
    rut_revisor VARCHAR(9) NOT NULL,
    id_especialidad_topico INT NOT NULL,
    PRIMARY KEY (rut_revisor, id_especialidad_topico),
    FOREIGN KEY (rut_revisor) REFERENCES Revisores(rut_revisor) ON DELETE CASCADE, 
    FOREIGN KEY (id_especialidad_topico) REFERENCES Especialidad_Topico(id_especialidad_topico) ON DELETE CASCADE 
);

-- TABLA Topicos_articulos: clase asociativa para manejar la relacion M;N entre especialidades_topicos y articulos
CREATE TABLE Topicos_Articulos (
    id_articulo INT NOT NULL,
    id_especialidad_topico INT NOT NULL,
    PRIMARY KEY (id_articulo, id_especialidad_topico),
    FOREIGN KEY (id_articulo) REFERENCES Articulos(id_articulo) ON DELETE CASCADE, 
    FOREIGN KEY (id_especialidad_topico) REFERENCES Especialidad_Topico(id_especialidad_topico) ON DELETE CASCADE 
);

-- TABLA Envio_Articulo: Clase asociativa para manejar la relacion M;N de autores y articulos. ademas de guardar los datos del autor de contacto
CREATE TABLE Envio_Articulo (
    rut_autor VARCHAR(9) NOT NULL,
    id_articulo INT NOT NULL,
    autor_contacto VARCHAR(100),
    PRIMARY KEY (rut_autor, id_articulo),
    FOREIGN KEY (rut_autor) REFERENCES Autores(rut_autor) ON DELETE CASCADE, 
    FOREIGN KEY (id_articulo) REFERENCES Articulos(id_articulo) ON DELETE CASCADE 
);