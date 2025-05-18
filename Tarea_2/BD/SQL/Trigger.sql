DELIMITER //
CREATE TRIGGER prevenir_eliminacion_admin
BEFORE DELETE ON Revisores
FOR EACH ROW
BEGIN
    DECLARE admin_count INT;
    
    -- Verificar si el revisor a eliminar es admin
    IF OLD.rol_revisor = 'admin' THEN
        -- Contar cuántos admins quedan
        SELECT COUNT(*) INTO admin_count FROM Revisores WHERE rol_revisor = 'admin';
        
        -- Si es el último admin, prohibir eliminación
        IF admin_count <= 1 THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'No se puede eliminar el único administrador del sistema';
        ELSE
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'No se pueden eliminar usuarios con rol de administrador';
        END IF;
    END IF;
END //
DELIMITER ;

-- Trigger de auditoría
DELIMITER //
CREATE TRIGGER after_revisor_insert
AFTER INSERT ON Revisores
FOR EACH ROW
BEGIN
    INSERT INTO logs_administracion (
        accion, 
        tabla_afectada, 
        id_afectado, 
        usuario_admin, 
        fecha
    ) VALUES (
        'INSERT', 
        'Revisores', 
        NEW.rut_revisor, 
        (SELECT usuario_autor FROM Autores WHERE rol_autor = 'admin' LIMIT 1), 
        NOW()
    );
END //
DELIMITER ;