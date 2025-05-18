-- Procedimiento almacenado
DELIMITER //
CREATE PROCEDURE InsertarRevisor(
    IN p_nombre VARCHAR(100),
    IN p_correo VARCHAR(100),
    IN p_usuario VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_rol VARCHAR(20),
    OUT resultado VARCHAR(100)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET resultado = 'Error en la operación';
        ROLLBACK;
    END;

    START TRANSACTION;
    
    IF EXISTS (SELECT 1 FROM Revisores WHERE usuario_revisor = p_usuario) THEN
        SET resultado = 'Usuario ya existe';
        ROLLBACK;
    ELSEIF EXISTS (SELECT 1 FROM Revisores WHERE correo_revisor = p_correo) THEN
        SET resultado = 'Correo ya registrado';
        ROLLBACK;
    ELSE
        INSERT INTO Revisores (
            nombre_revisor, 
            correo_revisor, 
            usuario_revisor, 
            contraseña_revisor, 
            rol_revisor
        ) VALUES (
            p_nombre,
            p_correo,
            p_usuario,
            p_password,
            p_rol
        );
        
        SET resultado = 'exito';
        COMMIT;
    END IF;
END //
DELIMITER ;
