--Nos aseguramos de que usemos la base de datos correcta.
USE GesconDatabase;
GO

--**********************CONSULTAS************************************
--Consulta 1: Nombres y Resumenes de articulos cuyo título empiece por "O"
SELECT 
    titulo, resumen  
FROM
    Articulos
    --Buscaremos todo los titulos que comiencen con "O"
WHERE titulo LIKE 'O%';
GO
--Consulata 2: Obtener los articulos enviados por cada autor
SELECT
    --Obtenemos los atributos del autor con el alias "cons_autor"
    cons_autor.rut_autor, cons_autor.nombre_autor,
    --Obtenemos los atributos del articulo con el alias "cons_articulo"
    cons_articulo.id_articulo, cons_articulo.titulo
FROM
    --Establecemos como tabla principal a autor
    Autores cons_autor
JOIN
    --Unimos las tablas usando rut_autor como clave
    Envio_Articulo cons_ea ON cons_autor.rut_autor = cons_ea.rut_autor
JOIN
    --Unimos las tablas usando id_articulo como clave
    Articulos cons_articulo ON cons_ea.id_articulo = cons_articulo.id_articulo
ORDER BY
    --Ordenamos por el nombre
    cons_autor.nombre_autor;
GO