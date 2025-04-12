#Importamos las librerias necesarias para el manejo de archivos y CSV's
import csv
import os

#*********************POBLADOR AUTORES*******************************
#Establecemos los criterios de busqueda y creacion del archivo
nombre_csv = os.path.join('CSV', 'autores.csv')
nombre_tabla = 'Autores'
archivo_sql = 'poblar_autores.sql'

#Abrimos el archivo .csv que esta en nuestra carpeta
with open(nombre_csv, newline='', encoding='utf-8') as csvfile:
    lector = csv.reader(csvfile)
    #Tomamos la primera fila como encabezado
    encabezados = next(lector)  
    columnas = ', '.join(encabezados)

    #Creamos nuestro archivo encargado de poblar la base de datos
    with open(archivo_sql, 'w', encoding='utf-8') as sqlfile:
        # Agregar la sentencia USE GesconDatabase;
        sqlfile.write("USE GesconDatabase;\nGO\n")
                
        for fila in lector:
            valores = ', '.join([f"'{valor}'" for valor in fila])
            sentencia = f"INSERT INTO {nombre_tabla} ({columnas}) VALUES ({valores});\n"
            sqlfile.write(sentencia)

#Si no hubo errores, informamos de la correcta creacion del archivo .sql
print(f"Archivo {archivo_sql} generado correctamente.")

#*********************POBLADOR ARTICULOS*******************************
#Establecemos los criterios de busqueda y creacion del archivo
nombre_csv = os.path.join('CSV', 'articulos.csv') # Ruta relativa a la carpeta CSV
nombre_tabla = 'Articulos'
archivo_sql = 'poblar_articulos.sql'

#Abrimos el archivo .csv que esta en nuestra carpeta
with open(nombre_csv, newline='', encoding='utf-8') as csvfile:
    lector = csv.reader(csvfile)
    #Tomamos la primera fila como encabezado
    encabezados = next(lector)  
    columnas = ', '.join(encabezados)

    #Creamos nuestro archivo encargado de poblar la base de datos
    with open(archivo_sql, 'w', encoding='utf-8') as sqlfile:
        # Agregar la sentencia USE GesconDatabase;
        sqlfile.write("USE GesconDatabase;\nGO\n")

        for fila in lector:
            valores = ', '.join([f"'{valor}'" for valor in fila])
            sentencia = f"INSERT INTO {nombre_tabla} ({columnas}) VALUES ({valores});\n"
            sqlfile.write(sentencia)

#Si no hubo errores, informamos de la correcta creacion del archivo .sql
print(f"Archivo {archivo_sql} generado correctamente.")

#*********************POBLADOR REVISORES*******************************
#Establecemos los criterios de busqueda y creacion del archivo
nombre_csv = os.path.join('CSV', 'revisores.csv') # Ruta relativa a la carpeta CSV
nombre_tabla = 'Revisores'
archivo_sql = 'poblar_revisores.sql'

#Abrimos el archivo .csv que esta en nuestra carpeta
with open(nombre_csv, newline='', encoding='utf-8') as csvfile:
    lector = csv.reader(csvfile)
    #Tomamos la primera fila como encabezado
    encabezados = next(lector)  
    columnas = ', '.join(encabezados)

    #Creamos nuestro archivo encargado de poblar la base de datos
    with open(archivo_sql, 'w', encoding='utf-8') as sqlfile:
        # Agregar la sentencia USE GesconDatabase;
        sqlfile.write("USE GesconDatabase;\nGO\n")
        
        for fila in lector:
            valores = ', '.join([f"'{valor}'" for valor in fila])
            sentencia = f"INSERT INTO {nombre_tabla} ({columnas}) VALUES ({valores});\n"
            sqlfile.write(sentencia)

#Si no hubo errores, informamos de la correcta creacion del archivo .sql
print(f"Archivo {archivo_sql} generado correctamente.")

#*********************POBLADOR ESCPECIALIDAD_TOPICO*******************************
#Establecemos los criterios de busqueda y creacion del archivo
nombre_csv = os.path.join('CSV', 'especialidad_topico.csv') # Ruta relativa a la carpeta CSV
nombre_tabla = 'Especialidad_Topico'
archivo_sql = 'poblar_especialidad_topico.sql'

#Abrimos el archivo .csv que esta en nuestra carpeta
with open(nombre_csv, newline='', encoding='utf-8') as csvfile:
    lector = csv.reader(csvfile)
    #Tomamos la primera fila como encabezado
    encabezados = next(lector)  
    columnas = ', '.join(encabezados)

    #Creamos nuestro archivo encargado de poblar la base de datos
    with open(archivo_sql, 'w', encoding='utf-8') as sqlfile:
        # Agregar la sentencia USE GesconDatabase;
        sqlfile.write("USE GesconDatabase;\nGO\n")
        
        for fila in lector:
            valores = ', '.join([f"'{valor}'" for valor in fila])
            sentencia = f"INSERT INTO {nombre_tabla} ({columnas}) VALUES ({valores});\n"
            sqlfile.write(sentencia)

#Si no hubo errores, informamos de la correcta creacion del archivo .sql
print(f"Archivo {archivo_sql} generado correctamente.")

#*********************POBLADOR ENVIO_ARTICULO*******************************
#Establecemos los criterios de busqueda y creacion del archivo
nombre_csv = os.path.join('CSV', 'envio_articulo.csv') # Ruta relativa a la carpeta CSV
nombre_tabla = 'Envio_Articulo'
archivo_sql = 'poblar_envio_articulo.sql'

#Abrimos el archivo .csv que esta en nuestra carpeta
with open(nombre_csv, newline='', encoding='utf-8') as csvfile:
    lector = csv.reader(csvfile)
    #Tomamos la primera fila como encabezado
    encabezados = next(lector)  
    columnas = ', '.join(encabezados)

    #Creamos nuestro archivo encargado de poblar la base de datos
    with open(archivo_sql, 'w', encoding='utf-8') as sqlfile:
        sqlfile.write("USE GesconDatabase;\nGO\n") # Usar la base de datos correcta.
        for fila in lector:
            #Convertimos la contraseña a VARBINARY
            password_hex = fila[4]  # Obtiene la contraseña hexadecimal
            valores = ', '.join([f"'{valor.strip()}'" if i != 4 else f"CONVERT(VARBINARY(128), '{valor.strip()}', 2)" for i, valor in enumerate(fila)])
            sentencia = f"INSERT INTO {nombre_tabla} ({columnas}) VALUES ({valores});\n"
            sqlfile.write(sentencia)

#Si no hubo errores, informamos de la correcta creacion del archivo .sql
print(f"Archivo {archivo_sql} generado correctamente.")

#*********************POBLADOR REVISION*******************************
#Establecemos los criterios de busqueda y creacion del archivo
nombre_csv = os.path.join('CSV', 'revision.csv') # Ruta relativa a la carpeta CSV
nombre_tabla = 'Revision'
archivo_sql = 'poblar_revision.sql'

#Abrimos el archivo .csv que esta en nuestra carpeta
with open(nombre_csv, newline='', encoding='utf-8') as csvfile:
    lector = csv.reader(csvfile)
    #Tomamos la primera fila como encabezado
    encabezados = next(lector)  
    columnas = ', '.join(encabezados)

    #Creamos nuestro archivo encargado de poblar la base de datos
    with open(archivo_sql, 'w', encoding='utf-8') as sqlfile:
        # Agregar la sentencia USE GesconDatabase;
        sqlfile.write("USE GesconDatabase;\nGO\n")
        
        for fila in lector:
            valores = ', '.join([f"'{valor}'" for valor in fila])
            sentencia = f"INSERT INTO {nombre_tabla} ({columnas}) VALUES ({valores});\n"
            sqlfile.write(sentencia)

#Si no hubo errores, informamos de la correcta creacion del archivo .sql
print(f"Archivo {archivo_sql} generado correctamente.")

#*********************POBLADOR ESPECIALIDAD_REVISORES*******************************
#Establecemos los criterios de busqueda y creacion del archivo
nombre_csv = os.path.join('CSV', 'especialidad_revisores.csv') # Ruta relativa a la carpeta CSV
nombre_tabla = 'Especialidad_Revisores'
archivo_sql = 'poblar_especialidad_revisores.sql'

#Abrimos el archivo .csv que esta en nuestra carpeta
with open(nombre_csv, newline='', encoding='utf-8') as csvfile:
    lector = csv.reader(csvfile)
    #Tomamos la primera fila como encabezado
    encabezados = next(lector)  
    columnas = ', '.join(encabezados)

    #Creamos nuestro archivo encargado de poblar la base de datos
    with open(archivo_sql, 'w', encoding='utf-8') as sqlfile:
        # Agregar la sentencia USE GesconDatabase;
        sqlfile.write("USE GesconDatabase;\nGO\n")
        
        for fila in lector:
            valores = ', '.join([f"'{valor}'" for valor in fila])
            sentencia = f"INSERT INTO {nombre_tabla} ({columnas}) VALUES ({valores});\n"
            sqlfile.write(sentencia)

#Si no hubo errores, informamos de la correcta creacion del archivo .sql
print(f"Archivo {archivo_sql} generado correctamente.")

#*********************POBLADOR ARTICULOS*******************************
#Establecemos los criterios de busqueda y creacion del archivo
nombre_csv = os.path.join('CSV', 'topicos_articulos.csv') # Ruta relativa a la carpeta CSV
nombre_tabla = 'Topicos_Articulos'
archivo_sql = 'poblar__topicos_articulos.sql'

#Abrimos el archivo .csv que esta en nuestra carpeta
with open(nombre_csv, newline='', encoding='utf-8') as csvfile:
    lector = csv.reader(csvfile)
    #Tomamos la primera fila como encabezado
    encabezados = next(lector)  
    columnas = ', '.join(encabezados)

    #Creamos nuestro archivo encargado de poblar la base de datos
    with open(archivo_sql, 'w', encoding='utf-8') as sqlfile:
        # Agregar la sentencia USE GesconDatabase;
        sqlfile.write("USE GesconDatabase;\nGO\n")
        
        for fila in lector:
            valores = ', '.join([f"'{valor}'" for valor in fila])
            sentencia = f"INSERT INTO {nombre_tabla} ({columnas}) VALUES ({valores});\n"
            sqlfile.write(sentencia)

#Si no hubo errores, informamos de la correcta creacion del archivo .sql
print(f"Archivo {archivo_sql} generado correctamente.")