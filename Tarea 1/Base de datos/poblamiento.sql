-- Nos conectamos a la base de datos "GesconDatabase".
USE GesconDatabase;
GO

-- Insertar datos desde autores.csv
BULK INSERT Autores
FROM 'C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea 1\autores.csv' -- Reemplaza con la ruta real
WITH (
    FIELDTERMINATOR = ',',
    ROWTERMINATOR = '\n',
    FIRSTROW = 2 -- Omitir la fila de encabezados
);

-- Insertar datos desde revisores.csv
BULK INSERT Revisores
FROM 'C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea 1\revisores.csv' -- Reemplaza con la ruta real
WITH (
    FIELDTERMINATOR = ',',
    ROWTERMINATOR = '\n',
    FIRSTROW = 2
);

-- Insertar datos desde articulos.csv
BULK INSERT Articulos
FROM 'C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea 1\articulos.csv' -- Reemplaza con la ruta real
WITH (
    FIELDTERMINATOR = ',',
    ROWTERMINATOR = '\n',
    FIRSTROW = 2
);

-- Insertar datos desde especialidad_topico.csv
BULK INSERT Especialidad_Topicos
FROM 'C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea 1\especialidad_topico.csv' -- Reemplaza con la ruta real
WITH (
    FIELDTERMINATOR = ',',
    ROWTERMINATOR = '\n',
    FIRSTROW = 2
);

-- Insertar datos desde revision.csv
BULK INSERT Revision
FROM 'C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea 1\revision.csv' -- Reemplaza con la ruta real
WITH (
    FIELDTERMINATOR = ',',
    ROWTERMINATOR = '\n',
    FIRSTROW = 2
);

-- Insertar datos desde especialidad_revisores.csv
BULK INSERT Especialidades_Revisores
FROM 'C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea 1\especialidad_revisores.csv' -- Reemplaza con la ruta real
WITH (
    FIELDTERMINATOR = ',',
    ROWTERMINATOR = '\n',
    FIRSTROW = 2
);

-- Insertar datos desde topicos_articulos.csv
BULK INSERT Topicos_Articulos
FROM 'C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea 1\topicos_articulos.csv' -- Reemplaza con la ruta real
WITH (
    FIELDTERMINATOR = ',',
    ROWTERMINATOR = '\n',
    FIRSTROW = 2
);

-- Insertar datos desde envio_articulo.csv (con conversión a VARBINARY)
INSERT INTO Envio_Articulo (rut_autor, id_articulo, correo_contacto, userid_contacto, password_contacto)
SELECT rut_autor, id_articulo, correo_contacto, userid_contacto, CONVERT(VARBINARY(128), SUBSTRING(password_contacto, 3, LEN(password_contacto) - 2), 2)
FROM OPENROWSET('CSV',
    'C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea 1\envio_articulo.csv', -- Reemplaza con la ruta real
    'FORMATFILE=C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea 1\envio_articulo_format.fmt', -- Reemplaza con la ruta real
    'FIRSTROW=2') AS csv_data;

-- Archivo de formato (envio_articulo_format.fmt)
-- Crea este archivo en la misma carpeta que envio_articulo.csv

/*
12.0
5
1 SQLCHAR 0 0 "," 0 rut_autor ""
2 SQLINT 0 0 "," 0 id_articulo ""
3 SQLCHAR 0 0 "," 0 correo_contacto ""
4 SQLCHAR 0 0 "," 0 userid_contacto ""
5 SQLCHAR 0 0 "\n" 0 password_contacto ""
*/