import csv
import os
import pathlib

# Configuración
# Obtener el directorio del script
directorio_script = pathlib.Path(__file__).parent.resolve()
carpeta_csv = os.path.join(directorio_script, 'CSV')  # Ruta absoluta a la carpeta CSV
archivo_sql = 'poblar_todas_las_tablas.sql'  # Nombre del archivo SQL de salida
tablas_csv = {
    'Autores': 'autores.csv',
    'Articulos': 'articulos.csv',
    'Revisores': 'revisores.csv',
    'Especialidad_Topico': 'especialidad_topico.csv',
    'Envio_Articulo': 'envio_articulo.csv',
    'Revision': 'revision.csv',
    'Especialidad_Revisores': 'especialidad_revisores.csv',
    'Topicos_Articulos': 'topicos_articulos.csv'
}

def generar_insert_sql(nombre_tabla, nombre_csv):
    """Genera las sentencias INSERT para una tabla a partir de un archivo CSV."""

    ruta_csv = os.path.join(carpeta_csv, nombre_csv)
    sentencias_sql = []

    try:
        with open(ruta_csv, newline='', encoding='utf-8') as csvfile:
            lector = csv.reader(csvfile)
            encabezados = next(lector)  # Obtener los nombres de las columnas
            columnas_lista = list(encabezados)

            columnas = ', '.join(columnas_lista)

            # Índice del rol si existe
            indice_rol = None
            if nombre_tabla == 'Autores' and 'rol_autor' in columnas_lista:
                indice_rol = columnas_lista.index('rol_autor')
            elif nombre_tabla == 'Revisores' and 'rol_revisor' in columnas_lista:
                indice_rol = columnas_lista.index('rol_revisor')

            for fila in lector:
                valores = list(fila)

                # Si hay menos valores que columnas, los rellenamos con ''
                while len(valores) < len(columnas_lista):
                    valores.append('')

                # Si el valor de rol está vacío, lo rellenamos
                if nombre_tabla == 'Autores' and indice_rol is not None:
                    if valores[indice_rol].strip() == '':
                        valores[indice_rol] = 'autor'

                elif nombre_tabla == 'Revisores' and indice_rol is not None:
                    if valores[indice_rol].strip() == '':
                        valores[indice_rol] = 'revisor'

                # Escapar comillas y formatear cada valor
                valores_formateados = []
                for v in valores:
                    limpio = v.strip().replace("'", "''")  # Escapar comillas simples para SQL
                    valores_formateados.append(f"'{limpio}'")
                
                valores_str = ', '.join(valores_formateados)
                sentencia = f"INSERT INTO {nombre_tabla} ({columnas}) VALUES ({valores_str});"
                sentencias_sql.append(sentencia)

    except FileNotFoundError:
        print(f"Error: No se encontró el archivo CSV: {ruta_csv}")
        return []
    except Exception as e:
        print(f"Error al procesar el archivo CSV {ruta_csv}: {e}")
        return []

    return sentencias_sql


# Generar todas las sentencias INSERT para todas las tablas
todas_las_sentencias = []

# Agregar la sentencia USE GesconDatabase; al principio del archivo
todas_las_sentencias.append("USE GesconDatabase;\n")

for tabla, archivo_csv in tablas_csv.items():
    sentencias = generar_insert_sql(tabla, archivo_csv)
    if sentencias:
        todas_las_sentencias.extend(sentencias)
        todas_las_sentencias.append('\n')  # Separar las sentencias de cada tabla

# Escribir todas las sentencias en un solo archivo SQL
try:
    with open(archivo_sql, 'w', encoding='utf-8') as sqlfile:
        sqlfile.write('\n'.join(todas_las_sentencias))
    print(f"Archivo SQL '{archivo_sql}' generado exitosamente con los datos de todas las tablas.")

except Exception as e:
    print(f"Error al escribir en el archivo SQL: {e}")