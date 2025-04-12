-- Nos conectamos a la base de datos "GesconDatabase".
USE GesconDatabase;
GO

-- Insertar datos desde autores.csv
BULK INSERT Autores
FROM 'C:\home\s4lv4\Documentos\GitHub\Base-de-datos\Tarea1\Programas\CSV\autores.csv' -- Reemplaza con la ruta real
WITH (
    FIELDTERMINATOR = ',',
    ROWTERMINATOR = '\n',
    FIRSTROW = 2 -- Omitir la fila de encabezados
);

