USE GesconDatabase;

CREATE VIEW ver_ev AS
SELECT 
    GROUP_CONCAT(et.tipo SEPARATOR ', ') AS topicos,
    a.titulo,
    a.resumen,
    a.estado,
    c.nombre_autor AS autor
FROM 
    Articulos a
JOIN 
    Envio_Articulo b ON a.id_articulo = b.id_articulo
JOIN 
    Autores c ON b.rut_autor = c.rut_autor
LEFT JOIN 
    Topicos_Articulos ta ON a.id_articulo = ta.id_articulo
LEFT JOIN 
    Especialidad_Topico et ON ta.id_especialidad_topico = et.id_especialidad_topico
WHERE 
    a.estado = 'evaluado'
GROUP BY 
    a.id_articulo, a.titulo, a.resumen, a.estado, c.nombre_autor;
