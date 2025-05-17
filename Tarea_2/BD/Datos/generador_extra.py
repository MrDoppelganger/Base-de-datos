#----------------librerias-------------------
import csv
import os
import random
import subprocess

#----------------Funciones------------------
'''
generar_insert_envio_articulo
***
ruta_datos : string
num_filas : int
num_copias : int
...
***
Tipo de Retorno : None
***
Funcion encargada de crear un archivo .sql con los insert copias de una cantidad designada de envio_articulos
, cambiando el autor de cada copia por uno disponible entre los autores del archivo autores.csv.
'''
def generar_inserts_envio_articulo(ruta_datos = str, num_filas = int, num_copias = int):
    #Establecemos las rutas de cada archivo a leer, y como se llamara nuestro nuevo documento
    ruta_autores_csv = os.path.join(ruta_datos, "CSV", "autores.csv")
    ruta_envio_csv = os.path.join(ruta_datos, "CSV", "envio_articulo.csv")
    ruta_salida_sql = os.path.join(ruta_datos, "Insert_extra.sql")

    # Almacenaremos los ruts disponibles.
    ruts_disponibles = []
    # Manejamos los errores de apertura del documento CSV
    try:
        #Leemos cada uno de los rut's que hay
        with open(ruta_autores_csv, 'r', encoding='utf-8') as archivo_autores:
            lector_csv = csv.reader(archivo_autores)
            next(lector_csv) 

            #leemos los ruts ubicados en la primera columna de cada fila
            for fila in lector_csv:
                ruts_disponibles.append(fila[0])

    except FileNotFoundError:
        print(f"Error: No se encontró el archivo {ruta_autores_csv}")
        return
    except Exception as e:
        print(f"Error al leer autores.csv: {e}")
        return

    # Ahora copiaremos las filas cambiandole el autor, para generar mas autores por articulos 
    try:
        with open(ruta_envio_csv, 'r', encoding='utf-8') as archivo_envio, \
             open(ruta_salida_sql, 'w', encoding='utf-8') as archivo_salida:

            lector_envio = csv.reader(archivo_envio)
            next(lector_envio) 

            #Establecemos la base de datos que se utilizara
            archivo_salida.write("USE GesconDatabase;\n")
            
            # Establecemos un contador para copiar la cantidad que deseemos de articulos
            filas_procesadas = 0
            for fila_envio in lector_envio:
                # Creamos nuestra condición de salida
                if filas_procesadas >= num_filas:
                    break 
                
                #Copiamos los datos de la fila
                id_articulo = fila_envio[0]
                rut_autor_original = fila_envio[1]
                autor_contacto = fila_envio[2]
                usuario_contacto = fila_envio[3]
                contrasena_contacto = fila_envio[4]

                # Guardamos en cada iteracion los ruts que han sido utilizados comenzando por el de la fila
                ruts_usados = [rut_autor_original]  
                
                # Enlazamos un nuevo autor a el articulo como num_copia establezaca
                for _ in range(num_copias):
                    # Seleccionar un rut aleatorio que no se haya usado aún para esta fila
                    rut_autor_nuevo = random.choice([rut for rut in ruts_disponibles if rut not in ruts_usados])
                    ruts_usados.append(rut_autor_nuevo)

                    # Crear la sentencia INSERT
                    insert_sql = f"INSERT INTO Envio_Articulo (id_articulo, rut_autor, autor_contacto, usuario_contacto, contraseña_contacto) VALUES " \
                                 f"('{id_articulo}', '{rut_autor_nuevo}', '{autor_contacto}', '{usuario_contacto}', '{contrasena_contacto}');\n"
                    archivo_salida.write(insert_sql)
                filas_procesadas += 1

        print(f"Se ha generado el archivo {ruta_salida_sql} con las sentencias INSERT.")

    except FileNotFoundError:
        print(f"Error: No se encontró el archivo {ruta_envio_csv}")
    except Exception as e:
        print(f"Error al procesar envio_articulo.csv: {e}")

'''
generar_insert_datos_extra
***
ruta_datos : string
...
***
Tipo de Retorno : None
***
Funcion encargada de agrear a un archivo .sql algunos insert destinado a la realizacion de pruebas.
'''
def generar_insert_datos_extra(ruta_salida_sql = str):
    # Establecemos nuestra contraseña plana para los usuarios de test
    contrasena_plana = "UserForTest"

    # Hasheamos la contraseña usando password_hash (atrapando los errores posibles con un try)
    try:
        resultado = subprocess.run(["php", "-r", f"echo password_hash('{contrasena_plana}', PASSWORD_DEFAULT);"], capture_output=True, text=True, check=True)
        contrasena_hasheada = resultado.stdout.strip()

    # Detectamos los errores comunes.
    except subprocess.CalledProcessError as e:
        print(f"Error al hashear la contraseña: {e.stderr}")

        return
    except FileNotFoundError:
        print("Error: PHP no está instalado o no se encuentra en el PATH.")

        return

    # Establecemos los insert de cada usuario de prueba
    insert_autor = f"INSERT INTO Autores (rut_autor, nombre_autor, correo_autor, rol_autor, usuario_autor, contraseña_autor) VALUES " \
                   f"('111111111', 'Test Autor', 'test.autor@example.com', 'Autor', 'testautor', '{contrasena_hasheada}');\n"

    insert_revisor = f"INSERT INTO Revisores (rut_revisor, nombre_revisor, correo_revisor, rol_revisor, usuario_revisor, contraseña_revisor) VALUES " \
                    f"('222222222', 'Test Revisor', 'test.revisor@example.com', 'Revisor', 'testrevisor', '{contrasena_hasheada}');\n"
    
    insert_admin_revisor = f"INSERT INTO Revisores (rut_revisor, nombre_revisor, correo_revisor, rol_revisor, usuario_revisor, contraseña_revisor) VALUES " \
                    f"('333333333', 'Test AdminRevisor', 'test.adminrevisor@example.com', 'AdminRevisor', 'testadminrevisor', '{contrasena_hasheada}');\n"

    # Escribimos los insert en nuestro archivo de datos extra (atrapamos los errores mediante un try)
    try:
        with open(ruta_salida_sql, 'a', encoding='utf-8') as archivo_salida:  # 'a' para append
            archivo_salida.write(insert_autor)
            archivo_salida.write(insert_revisor)
            archivo_salida.write(insert_admin_revisor)
        print(f"Se han agregado los inserts de usuarios de prueba al archivo {ruta_salida_sql}")

    except FileNotFoundError:
        print(f"Error: No se encontró el archivo {ruta_salida_sql}")
    except Exception as e:
        print(f"Error al escribir en el archivo SQL: {e}")

#-----------------Ejecución------------------------
#Establecemos los valores iniciales.
ruta = "BD/Datos"
ruta_insert = "BD/Datos/Insert_extra.sql"
cant_articulos_autores = 20
num_autores_agregador = 3

#generamos nuestro .sql
if __name__ == "__main__":
    generar_inserts_envio_articulo(ruta, cant_articulos_autores, num_autores_agregador)
    generar_insert_datos_extra(ruta_insert)