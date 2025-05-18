import time
from faker import Faker
import random
import csv
import os
import uuid
import uuid
import datetime

# --- Configuración inicial ---
fake = Faker('es_CL')

# --- Constantes ---
NUM_ARTICULOS = 400
NUM_AUTORES = 50  # Asegúrate de tener suficientes autores para la variedad
NUM_REVISORES = 50 # Podría necesitarse aumentar si hay problemas para encontrar revisores relevantes
MAX_AUTORES_PER_ARTICULO = 3
MAX_TOPICOS_PER_ARTICULO = 3
MAX_ESPECIALIDADES_PER_REVISOR = 5 # Aumentar un poco puede ayudar a la compatibilidad
REVISORES_POR_ARTICULO = 3 # ¡Requisito estricto!

# Tópicos posibles (Base para especialidades)
TOPICOS_POSIBLES = [
    "Inteligencia Artificial", "Bases de Datos", "Redes de Computadores", "Ciberseguridad",
    "Computación Gráfica", "Algoritmos y Estructuras de Datos", "Ingeniería de Software", "Big Data",
    "Desarrollo Web Full-Stack", "Computación en la Nube", "Sistemas Operativos", "Teoría de la Computación",
    "Aprendizaje Automático", "Visión por Computador", "Procesamiento de Lenguaje Natural", "Blockchain",
    "Internet de las Cosas", "Realidad Virtual y Aumentada" # Añadir más para variedad
]

# Plantillas (sin cambios)
plantillas_titulos = [
    "Análisis profundo de {topico}", "Nuevas tendencias en {topico}",
    "Implementación de {topico} en la industria", "Estudio comparativo sobre {topico1} y {topico2}",
    "Desafíos actuales en {topico}", "Optimización de procesos mediante {topico}",
    "El futuro de {topico}: Una perspectiva", "Introducción a {topico} para principiantes",
    "Marco de trabajo para {topico} escalable", "Seguridad en sistemas de {topico}",
    "Aplicaciones prácticas de {topico1} con {topico2}", "Evaluación de rendimiento en {topico}"
]
plantillas_resumenes = [
    "Este artículo explora los fundamentos de {topico} y sus aplicaciones prácticas en diversos sectores.",
    "Se presenta una revisión exhaustiva de las últimas investigaciones y avances en el campo de {topico}.",
    "El presente trabajo detalla una nueva metodología para abordar problemas complejos en {topico}, validada experimentalmente.",
    "Discutimos las ventajas y desventajas de diferentes enfoques en {topico1} y {topico2}, proveyendo una guía comparativa.",
    "Se propone un marco de trabajo innovador basado en {topico} para mejorar la eficiencia y reducir costos operativos.",
    "Este estudio investiga el impacto de {topico} en el desarrollo tecnológico actual y sus implicaciones futuras.",
    "Ofrecemos una guía práctica y detallada para la implementacin exitosa de soluciones de {topico} en entornos reales.",
    "Se analizan los retos de seguridad, privacidad y éticos asociados con la adopción masiva de {topico}.",
    "Resultados de un estudio de caso sobre la aplicación de {topico} para resolver un problema específico de la industria.",
    "Exploramos la sinergia entre {topico1} y {topico2} para crear soluciones más robustas y potentes."
]

# --- Generadores de IDs ---
next_id_general = 1

def generar_id_unico():
    global next_id_general
    id_actual = next_id_general
    next_id_general += 1
    return id_actual

# --- Conjuntos globales para unicidad ---
ruts_usados_globalmente = set()
correos_usados_globalmente = set()

# --- Funciones de Generación ---

def generar_persona_unica(cantidad, tipo_persona):
     print(f"Generando {cantidad} {tipo_persona} únicos...")
     personas = []
     intentos = 0
     max_intentos = cantidad * 10  # Aumentar margen por si hay muchas colisiones
 
     while len(personas) < cantidad and intentos < max_intentos:
         rut = fake.unique.rut()
         rut_sin_formato = rut.replace('.', '').replace('-', '')  # Eliminar puntos y guiones
         correo = fake.unique.email()
         intentos += 1
 
         # Validar formato RUT chileno básico (simplificado)
         if not (7 <= len(rut_sin_formato) <= 9):
             continue  # Intentar de nuevo si el formato no parece correcto
 
         if rut_sin_formato not in ruts_usados_globalmente and correo not in correos_usados_globalmente:
             nombre = fake.name()
             usuario = f"{nombre.split()[0].lower()}{rut_sin_formato[:4]}"  # Generate initial username
             contrasena = uuid.uuid4().hex
             personas.append({'rut': rut_sin_formato, 'nombre': nombre, 'correo': correo,
                              'usuario': usuario, 'contrasena': contrasena})
             ruts_usados_globalmente.add(rut_sin_formato)
             correos_usados_globalmente.add(correo)
             fake.unique.clear()
             if len(personas) % 10 == 0:
                 print(f"... {len(personas)}/{cantidad} {tipo_persona} generados. (Ej: usuario={usuario})")
 
     if len(personas) < cantidad:
         print(
             f"ADVERTENCIA: No se pudieron generar los {cantidad} {tipo_persona} únicos solicitados. Se generaron {len(personas)}. Puede que necesite más intentos o que Faker se haya quedado sin opciones únicas.")
 
     print(f"Generación de {tipo_persona} completada. {len(personas)} generados.")
     return personas


def generar_especialidades_topico():
    print("Generando especialidades/tópicos...")
    especialidades = []
    topico_a_id_map = {}
    id_contador = 1
    for topico in TOPICOS_POSIBLES:
        descripcion = f"Especialidad o tópico relacionado con {topico}."
        especialidades.append({
            'id_especialidad_topico': id_contador,
            'tipo': topico,
            'descripcion': descripcion
        })
        topico_a_id_map[topico] = id_contador
        id_contador += 1
    print(f"Generadas {len(especialidades)} especialidades/tópicos.")
    return especialidades, topico_a_id_map

def generar_articulos(cantidad, autores_disponibles, topico_a_id_map):
    """Genera la lista de artículos y la tabla de envío para el autor de contacto."""
    print(f"\nGenerando {cantidad} artículos y datos de envío...")
    articulos_generados = []
    topicos_articulos_relacion = []
    envio_articulo_data = [] # Ahora solo 1 entrada por artículo
    id_articulo_contador = 1

    if not autores_disponibles:
        print("ERROR: No hay autores disponibles para asignar a los artículos.")
        return [], [], []

    autores_ruts = [a['rut'] for a in autores_disponibles]
    autores_map = {a['rut']: a for a in autores_disponibles}

    articulos_con_autores = [] # Lista temporal para guardar qué autores tiene cada artículo

    for i in range(cantidad):
        id_art = id_articulo_contador

        # Seleccionar Tópicos (sin cambios)
        num_topicos = random.randint(1, min(len(TOPICOS_POSIBLES), MAX_TOPICOS_PER_ARTICULO))
        topicos_nombres_articulo = random.sample(TOPICOS_POSIBLES, k=num_topicos)
        topicos_ids_articulo = [topico_a_id_map[nombre] for nombre in topicos_nombres_articulo]

        # Generar Título (CORRECCIÓN AQUÍ)
        plantilla_titulo = random.choice(plantillas_titulos)
        if "{topico1}" in plantilla_titulo and len(topicos_nombres_articulo) >= 2:
            titulo = plantilla_titulo.format(topico1=topicos_nombres_articulo[0], topico2=topicos_nombres_articulo[1])
        elif "{topico}" in plantilla_titulo and topicos_nombres_articulo:
            titulo = plantilla_titulo.format(topico=topicos_nombres_articulo[0])
        elif topicos_nombres_articulo:
            # Si no hay marcadores específicos, usar el primer tópico
            titulo = f"Investigación sobre {topicos_nombres_articulo[0]}"
        else:
            titulo = "Artículo sin tópico asignado" # Caso improbable pero manejado
        titulo = titulo.capitalize()

        # Generar Resumen (CORRECCIÓN AQUÍ - similar al título)
        plantilla_resumen = random.choice(plantillas_resumenes)
        if "{topico1}" in plantilla_resumen and len(topicos_nombres_articulo) >= 2:
            resumen_base = plantilla_resumen.format(topico1=topicos_nombres_articulo[0], topico2=topicos_nombres_articulo[1])
        elif "{topico}" in plantilla_resumen and topicos_nombres_articulo:
            resumen_base = plantilla_resumen.format(topico=topicos_nombres_articulo[0])
        elif topicos_nombres_articulo:
            resumen_base = f"Análisis detallado sobre {topicos_nombres_articulo[0]}."
        else:
            resumen_base = "Resumen de artículo sin tópico."
        resumen = (resumen_base[:147] + '...') if len(resumen_base) > 150 else resumen_base

        fecha_envio = fake.date_between(start_date='-2y', end_date='today').strftime("%Y-%m-%d")

        # --- Cambios en Selección de Autores y Contacto ---
        num_autores_articulo = random.randint(1, min(len(autores_disponibles), MAX_AUTORES_PER_ARTICULO))
        autores_seleccionados_ruts = random.sample(autores_ruts, k=num_autores_articulo)
        # *** Designar el primer autor como contacto ***
        autor_contacto_rut = autores_seleccionados_ruts[0]
        autor_contacto_info = autores_map[autor_contacto_rut]
        # --- Fin Cambios ---

        # Guardar datos del artículo (solo la info base)
        articulos_generados.append({
            'id_articulo': id_art,
            'titulo': titulo,
            'fecha_envio': fecha_envio,
            'resumen': resumen,
            'estado': 'evaluado' # Añadimos como valor inicial evaluados
        })

        # Guardar relación Artículo-Tópico (sin cambios)
        for id_topico in topicos_ids_articulo:
            topicos_articulos_relacion.append({
                'id_articulo': id_art,
                'id_especialidad_topico': id_topico
            })

        # --- Cambios en Generación de Envío Artículo ---
        # *** Crear UNA SOLA entrada para el autor de contacto ***
        userid_cont = f"{autor_contacto_info['nombre'].split()[0].lower()}{autor_contacto_rut[:4]}"

        # Generar contraseña hexadecimal
        password_cont = uuid.uuid4().hex # Genera un UUID y lo convierte a hexadecimal

        envio_articulo_data.append({
             'id_articulo': id_art,
             'rut_autor': autor_contacto_rut,  # RUT del contacto
             'autor_contacto': autor_contacto_info['correo'],  # Correo del contacto (renombrado a autor_contacto)
             'usuario_contacto': autor_contacto_info['usuario'],  # Userid del contacto
             'contraseña_contacto': autor_contacto_info['contrasena']  # Password del contacto (hexadecimal)
         })

        # Guardar temporalmente qué autores tiene cada artículo (para referencia si es necesario, aunque envio_articulo ahora solo tiene 1)
        articulos_con_autores.append({'id_articulo': id_art, 'autores_ruts': autores_seleccionados_ruts})

        id_articulo_contador += 1

        if (i + 1) % 50 == 0:
            print(f"... {i+1}/{cantidad} artículos y envíos generados.")

    print(f"Generación de {len(articulos_generados)} artículos y {len(envio_articulo_data)} registros de envío completada.")
    # Devolvemos articulos_generados (info base), topicos_articulos_relacion, y envio_articulo_data (contacto único)
    return articulos_generados, topicos_articulos_relacion, envio_articulo_data

# ... (código anterior completo) ...

def generar_relaciones_revisor(revisores, articulos, topico_a_id_map, topicos_articulos_relacion):
    """Genera las tablas de relaciones para los revisores: especialidades y revisiones."""
    print("\nGenerando relaciones de revisores (especialidades y asignaciones)...")
    especialidad_revisores_data = []
    revision_data = []
    articulos_sin_suficientes_revisores = 0

    if not revisores or not articulos:
        print("ERROR: Faltan revisores o artículos para generar relaciones.")
        return [], [], 0

    # 1. Generar Especialidades de Revisores (y mapeo para consulta rápida)
    revisor_a_especialidades_map = {}
    for rev in revisores:
        num_especialidades = random.randint(1, min(len(TOPICOS_POSIBLES), MAX_ESPECIALIDADES_PER_REVISOR))
        # Asegurarse de que haya tópicos para elegir
        if not TOPICOS_POSIBLES:
            print(f"Advertencia: No hay tópicos definidos para asignar especialidades al revisor {rev['rut']}")
            continue
        especialidades_nombres = random.sample(TOPICOS_POSIBLES, k=min(num_especialidades, len(TOPICOS_POSIBLES)))
        especialidades_ids = {topico_a_id_map[nombre] for nombre in especialidades_nombres if nombre in topico_a_id_map}
        revisor_a_especialidades_map[rev['rut']] = especialidades_ids
        for id_esp in especialidades_ids:
            especialidad_revisores_data.append({
                'rut_revisor': rev['rut'],
                'id_especialidad_topico': id_esp
            })
    print(f"... Generadas {len(especialidad_revisores_data)} relaciones de especialidad de revisor.")

    # 2. Mapear artículos a sus tópicos (para consulta rápida)
    articulo_a_topicos_map = {}
    for rel in topicos_articulos_relacion:
        art_id = rel['id_articulo']
        top_id = rel['id_especialidad_topico']
        if art_id not in articulo_a_topicos_map:
            articulo_a_topicos_map[art_id] = set()
        articulo_a_topicos_map[art_id].add(top_id)

    # 3. Generar Asignaciones de Revisión (Lógica Modificada)
    print(f"... Asignando {REVISORES_POR_ARTICULO} revisores relevantes a cada artículo...")
    revisores_ruts_disponibles = list(revisor_a_especialidades_map.keys())

    for art in articulos:
        art_id = art['id_articulo']
        topicos_del_articulo = articulo_a_topicos_map.get(art_id, set())

        if not topicos_del_articulo:
             print(f"Advertencia: El artículo {art_id} no tiene tópicos asignados. No se pueden encontrar revisores relevantes.")
             articulos_sin_suficientes_revisores += 1
             continue

        # --- Lógica Nueva: Encontrar EXACTAMENTE REVISORES_POR_ARTICULO revisores RELEVANTES ---
        revisores_elegibles = []
        for rev_rut in revisores_ruts_disponibles:
            especialidades_revisor = revisor_a_especialidades_map.get(rev_rut, set())
            # Es elegible si hay intersección entre sus especialidades y los tópicos del artículo
            if topicos_del_articulo.intersection(especialidades_revisor):
                revisores_elegibles.append(rev_rut)

        # Verificar si hay suficientes revisores elegibles
        if len(revisores_elegibles) >= REVISORES_POR_ARTICULO:
            # Seleccionar exactamente REVISORES_POR_ARTICULO al azar de los elegibles
            revisores_asignados = random.sample(revisores_elegibles, k=REVISORES_POR_ARTICULO)
            for rev_rut in revisores_asignados:
                fecha_envio_date = datetime.datetime.strptime(art['fecha_envio'], "%Y-%m-%d").date()
                revision_data.append({
                    'id_articulo': art_id,
                    'rut_revisor': rev_rut,
                    'fecha_revision': fake.date_between(start_date=fecha_envio_date, end_date='today').strftime("%Y-%m-%d"),
                    'comentarios': fake.sentence(nb_words=10),
                    'calificacion': random.randint(1, 7)
                })
        else:
            # No se encontraron suficientes revisores relevantes
            print(f"ADVERTENCIA: No se encontraron {REVISORES_POR_ARTICULO} revisores con especialidades relevantes para el artículo {art_id}. "
                  f"(Encontrados: {len(revisores_elegibles)}). Este artículo no tendrá asignaciones de revisión en el CSV.")
            articulos_sin_suficientes_revisores += 1
        # --- Fin Lógica Nueva ---

    print(f"... Generadas {len(revision_data)} asignaciones de revisión.")
    if articulos_sin_suficientes_revisores > 0:
        print(f"*** {articulos_sin_suficientes_revisores}/{len(articulos)} artículos no pudieron recibir {REVISORES_POR_ARTICULO} revisores relevantes. ***")
        print("    -> Considera aumentar NUM_REVISORES, MAX_ESPECIALIDADES_PER_REVISOR, o revisar la diversidad de TOPICOS_POSIBLES.")

    print("Generación de relaciones de revisores completada.")
    return especialidad_revisores_data, revision_data, articulos_sin_suficientes_revisores
# ... (resto del código: función escribir_csv y la sección de ejecución principal) ...

def escribir_csv(nombre_archivo, datos, encabezados):
    # (Sin cambios respecto a la versión anterior)
    print(f"Escribiendo archivo: {nombre_archivo}...")
    if not datos:
        print(f"Advertencia: No hay datos para escribir en {nombre_archivo}.")
        with open(nombre_archivo, 'w', newline='', encoding='utf-8') as csvfile:
             writer = csv.DictWriter(csvfile, fieldnames=encabezados)
             writer.writeheader()
        return

    try:
        with open(nombre_archivo, 'w', newline='', encoding='utf-8') as csvfile:
            if not encabezados:
                 encabezados = datos[0].keys()
            writer = csv.DictWriter(csvfile, fieldnames=encabezados)
            writer.writeheader()
            writer.writerows(datos)
        print(f"Archivo '{nombre_archivo}' guardado ({len(datos)} filas).")
    except Exception as e:
        print(f"Error al escribir el archivo {nombre_archivo}: {e}")

# =========================
# --- Ejecución Principal ---
# =========================
start_total_time = time.time()
print("--- INICIO DE GENERACIÓN DE DATOS (v2) ---")

# 1. Generar Autores
autores_generados = generar_persona_unica(NUM_AUTORES, "autores")

# 2. Generar Revisores
revisores_generados = generar_persona_unica(NUM_REVISORES, "revisores")

# 3. Generar Especialidades/Tópicos
especialidades_data, topico_a_id_map = generar_especialidades_topico()

# 4. Generar Artículos (info base), Relación Artículo-Tópico y Envío-Artículo (contacto único)
articulos_data, topicos_articulos_data, envio_articulo_data = generar_articulos(NUM_ARTICULOS, autores_generados, topico_a_id_map)

# 5. Generar Relaciones de Revisores (Especialidades y Revisiones - Lógica Nueva)
especialidad_revisores_data, revision_data, num_art_fallidos = generar_relaciones_revisor(revisores_generados, articulos_data, topico_a_id_map, topicos_articulos_data)


# =========================
# --- Escribir Archivos CSV ---
# =========================
print("\n--- ESCRIBIENDO ARCHIVOS CSV ---")

# (Renombrado de claves y escritura sin cambios respecto a la versión anterior)
def renombrar_claves(lista_diccionarios, mapeo_claves):
    nueva_lista = []
    for d in lista_diccionarios:
        nuevo_d = {}
        for clave_vieja, valor in d.items():
            clave_nueva = mapeo_claves.get(clave_vieja, clave_vieja)
            nuevo_d[clave_nueva] = valor
        nueva_lista.append(nuevo_d)
    return nueva_lista

mapeo_autores = {'rut': 'rut_autor', 'nombre': 'nombre_autor', 'correo': 'correo_autor', 'usuario': 'usuario_autor', 'contrasena': 'contraseña_autor'}
mapeo_revisores = {'rut': 'rut_revisor', 'nombre': 'nombre_revisor', 'correo': 'correo_revisor', 'usuario': 'usuario_revisor', 'contrasena': 'contraseña_revisor'}
 
autores_csv_list = renombrar_claves(autores_generados, mapeo_autores)
revisores_csv_list = renombrar_claves(revisores_generados, mapeo_revisores)
 
# Lista de archivos a generar (envio_articulo ya está con los datos correctos)
archivos_a_generar = [
     {'nombre': "autores.csv", 'datos': autores_csv_list, 'encabezados': ['rut_autor', 'nombre_autor', 'correo_autor', 'rol_autor', 'usuario_autor', 'contraseña_autor']},
     {'nombre': "revisores.csv", 'datos': revisores_csv_list, 'encabezados': ['rut_revisor', 'nombre_revisor', 'correo_revisor', 'rol_revisor', 'usuario_revisor', 'contraseña_revisor']},
     {'nombre': "articulos.csv", 'datos': articulos_data, 'encabezados': ['id_articulo', 'titulo', 'fecha_envio', 'resumen', 'estado']},
     {'nombre': "especialidad_topico.csv", 'datos': especialidades_data, 'encabezados': ['id_especialidad_topico', 'tipo', 'descripcion']},
     {'nombre': "especialidad_revisores.csv", 'datos': especialidad_revisores_data, 'encabezados': ['rut_revisor', 'id_especialidad_topico']},
     {'nombre': "topicos_articulos.csv", 'datos': topicos_articulos_data, 'encabezados': ['id_articulo', 'id_especialidad_topico']},
     {'nombre': "revision.csv", 'datos': revision_data, 'encabezados': ['id_articulo', 'rut_revisor', 'fecha_revision', 'comentarios', 'calificacion']},
     {'nombre': "envio_articulo.csv", 'datos': envio_articulo_data, 'encabezados': ['id_articulo', 'rut_autor', 'autor_contacto', 'usuario_contacto', 'contraseña_contacto']}
 ]

# Escribir todos los archivos
for archivo_info in archivos_a_generar:
    escribir_csv(archivo_info['nombre'], archivo_info['datos'], archivo_info['encabezados'])


end_total_time = time.time()
print(f"\n--- GENERACIÓN TOTAL COMPLETADA EN {end_total_time - start_total_time:.2f} SEGUNDOS ---")

# =========================
# --- Resumen Final ---
# =========================
print("\n--- Resumen Final ---")
print(f"* Autores Generados: {len(autores_generados)}")
print(f"* Revisores Generados: {len(revisores_generados)}")
print(f"* Artículos Generados: {len(articulos_data)}")
print(f"* Especialidades/Tópicos Definidos: {len(especialidades_data)}")
print(f"* Registros de Envío (1 por Artículo): {len(envio_articulo_data)}")
print(f"* Relaciones Especialidad-Revisor: {len(especialidad_revisores_data)}")
print(f"* Relaciones Tópico-Artículo: {len(topicos_articulos_data)}")
print(f"* Asignaciones de Revisión Generadas: {len(revision_data)} (Esperado <= {len(articulos_data) * REVISORES_POR_ARTICULO})")
if num_art_fallidos > 0:
     print(f"*** ADVERTENCIA: {num_art_fallidos} artículos no pudieron ser asignados a {REVISORES_POR_ARTICULO} revisores relevantes y no están en 'revision.csv'. ***")
print("\nArchivos CSV creados:")
for archivo_info in archivos_a_generar:
    print(f"- {archivo_info['nombre']}")
print("\nCondiciones aplicadas:")
print(f"- Cada artículo en 'revision.csv' tiene {REVISORES_POR_ARTICULO} revisores.")
print("- Revisores asignados tienen al menos 1 especialidad coincidente con el tópico del artículo.")
print("- 'envio_articulo.csv' contiene solo 1 fila por artículo (autor de contacto).")
print("- RUTs y Correos únicos entre Autores y Revisores.")
print("- IDs secuenciales.")