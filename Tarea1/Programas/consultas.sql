--Nos aseguramos de que usemos la base de datos correcta.
USE GesconDatabase;
GO

--**********************CONSULTAS************************************

--Consulta 1: Nombres y Resumenes de articulos cuyo título empiece por "O".
SELECT 
    titulo, resumen  
FROM
    Articulos
    --Buscaremos todo los titulos que comiencen con "O".
WHERE titulo LIKE 'O%';
GO


--Consulta 2.0: Obtener los articulos enviados por cada autor.
SELECT
    --Obtenemos los atributos del autor con el alias "cons_autor".
    cons_autor.rut_autor, cons_autor.nombre_autor,
    --Obtenemos los atributos del articulo con el alias "cons_articulo".
    cons_articulo.id_articulo, cons_articulo.titulo
FROM
    --Establecemos como tabla principal a autor.
    Autores cons_autor
JOIN
    --Unimos las tablas usando rut_autor como clave.
    Envio_Articulo cons_ea ON cons_autor.rut_autor = cons_ea.rut_autor
JOIN
    --Unimos las tablas usando id_articulo como clave.
    Articulos cons_articulo ON cons_ea.id_articulo = cons_articulo.id_articulo
ORDER BY
    --Ordenamos por el nombre.
    cons_autor.nombre_autor;
GO


--Consulta 2.1: Obtenemos el numero de articulos por cada autor.
USE GesconDatabase;
GO

SELECT a.rut_autor, a.nombre_autor, COUNT(e.rut_autor) AS cantidad_articulos
FROM Autores a
JOIN Envio_Articulo e ON a.rut_autor = e.rut_autor
GROUP BY a.rut_autor, a.nombre_autor;
GO


--Consulta 3:Obtenemos los titulos de los articulos con mas de un topico asignado.
SELECT 
    --Seleccionamos el atributo que queremos mostrar.
    cons_articulo.titulo
From 
    --Obtenemos la tabla a consultar.
    Topicos_Articulos topicos_articulos
JOIN 
    --Unimos las tablas Articulos y Topicos_Articulos para acceder al titulo.
    Articulos cons_articulo ON topicos_articulos.id_articulo = cons_articulo.id_articulo
GROUP BY 
    cons_articulo.id_articulo, cons_articulo.titulo
HAVING 
    --Colocamos como condicion que sea mayor a uno la cantidad de topicos.
    COUNT(topicos_articulos.id_especialidad_topico) > 1;
GO


--Consulta 4: Mostrar el titulo del articulo con la palabra "Software" y toda la informacion del autor de contacto.
SELECT a.titulo, au.rut_autor, au.nombre_autor, au.correo_autor
FROM Articulos a
JOIN Envio_Articulo ea ON a.id_articulo = ea.id_articulo
JOIN Autores au ON ea.rut_autor = au.rut_autor
WHERE a.titulo LIKE '%Software%';
GO


--Consulata 5: Obtenemos el nombre y la cantidad de articulos que tiene cada revisor.
SELECT r.nombre_revisor, COUNT(*) AS cantidad_articulos
FROM Revision revision
JOIN Revisores r ON revision.rut_revisor = r.rut_revisor
GROUP BY r.rut_revisor, r.nombre_revisor;
GO


--Consulta 6: Obtenemos el nombre de los revisores con mas de 3 articulos a revisar.
SELECT r.nombre_revisor
FROM Revision revision
JOIN Revisores r ON revision.rut_revisor = r.rut_revisor
GROUP BY r.rut_revisor, r.nombre_revisor
HAVING COUNT(r.rut_revisor)>3;
GO


--Consulta 7: Obtener el nombre de los articulos con la palabra "web" y el nombre de los revisores asignados.
SELECT a.titulo, r.nombre_revisor
FROM Articulos a
JOIN Revision rev ON a.id_articulo = rev.id_articulo
JOIN Revisores r ON rev.rut_revisor = r.rut_revisor
WHERE a.titulo LIKE '%web%';
GO


--Consulta 8: Obtener el numero de especialista por cada topico.
SELECT
    --Seleccionamos los datos que queremos mostrar.
    cons_especialidad.tipo, COUNT(*) AS cantidad_especialistas
FROM 
    --Obtenemos la tabla a consultar 
    Especialidad_Revisores especialidades
JOIN 
    --Unimos las tablas para obtener el tipo de topico que posee cada especialista.
    Especialidad_Topico cons_especialidad ON especialidades.id_especialidad_topico = cons_especialidad.id_especialidad_topico
GROUP BY 
    cons_especialidad.id_especialidad_topico, cons_especialidad.tipo;
GO


--Consulta 9: Obtener el top 10 de articulos mas antiguos de la base de datos.
SELECT TOP 10 
    --Seleccionamos el top 10 de datos que queremos mostrar.
    titulo, fecha_envio
FROM
    --Establecemo en que tabla haremos la busqueda
    Articulos
ORDER BY
    --Establecemos que sera la fecha el parametro con el que se ordenara, y que sera de forma ascendente.
    fecha_envio ASC;
GO


--Consulta 10: Obtendremos los titulos de los articulos cuyos revisores (cada uno) participa en la revisión de 3 o mas articulos.
SELECT 
    --Seleccionamos el titulo del articulo para mostrar.
    cons_articulo.titulo
FROM 
    --Seleccionamos la tabla que utilizaremos y le asignamos un alias.
    Articulos cons_articulo
WHERE NOT EXISTS (
    --Buscamos si existe algun revisor del articulo actual que revise menos de 3 articulos.
    SELECT 1
    FROM 
        --Establecemos un alias a la tabla revisión
        Revision revision_1
    WHERE 
        --Relacionamos la revisión con el artículo actual.
        revision_1.id_articulo = cons_articulo.id_articulo
        AND (
            --Contamos cuántos artículos ha revisado ese revisor.
            SELECT 
                COUNT(revision_2.id_articulo)
            FROM 
                Revision revision_2
            WHERE 
                revision_2.rut_revisor = revision_1.rut_revisor
            --Condición: ese revisor ha revisado menos de 3 artículos.
            ) < 3  
);
GO
