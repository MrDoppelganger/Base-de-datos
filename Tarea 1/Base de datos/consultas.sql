--Nos aseguramos de que usemos la base de datos correcta.
USE GesconDatabase;
GO

--Consulta 1: Nombres y Resumenes de articulos cuyo título empiece por "O"
SELECT titulo, resumen, nom
FROM Articulos
WHERE titulo LIKE "O%";