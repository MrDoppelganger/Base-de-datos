import time
from faker import Faker
import random
import csv # Importar el módulo CSV
import os

# --- Configuración inicial ---
fake = Faker('es_CL')

# Tópicos en español
topicos_posibles = [
    "Inteligencia Artificial", "Bases de Datos", "Redes", "Ciberseguridad",
    "Computación Gráfica", "Algoritmos", "Ingeniería de Software", "Big Data",
    "Desarrollo Web", "Computación en la Nube", "Sistemas Operativos", "Teoría de la Computación"
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
    "Ofrecemos una guía práctica y detallada para la implementación exitosa de soluciones de {topico} en entornos reales.",
    "Se analizan los retos de seguridad, privacidad y éticos asociados con la adopción masiva de {topico}.",
    "Resultados de un estudio de caso sobre la aplicación de {topico} para resolver un problema específico de la industria.",
    "Exploramos la sinergia entre {topico1} y {topico2} para crear soluciones más robustas y potentes."
]

# --- Constante para Máximo de Autores por Artículo ---
# Asegúrate que este valor coincida con el usado en generar_articulo
MAX_AUTORES_PER_ARTICULO = 5

# --- Funciones de Generación (Sin try/except, igual que antes) ---

def generar_autores_unicos(n):
    print(f"Generando {n} autores únicos...")
    start_time = time.time()
    autores = []
    emails_usados = set()
    ruts_usados = set()
    while len(autores) < n:
        email = fake.unique.email()
        rut = fake.unique.rut()
        if email not in emails_usados and rut not in ruts_usados:
            nombre = fake.name()
            autores.append({'nombre': nombre, 'email': email, 'rut': rut})
            emails_usados.add(email)
            ruts_usados.add(rut)
            if len(autores) % 10 == 0:
                 print(f"... {len(autores)} autores generados")
    fake.unique.clear()
    end_time = time.time()
    print(f"Generación de autores completada. {len(autores)} autores en {end_time - start_time:.2f} seg.")
    return autores

def generar_articulo(autores_disponibles):
    num_topicos = random.randint(1, min(len(topicos_posibles), 3))
    topicos_articulo = random.sample(topicos_posibles, k=num_topicos)

    # --- Título ---
    plantilla_titulo = random.choice(plantillas_titulos)
    if "{topico1}" in plantilla_titulo and "{topico2}" in plantilla_titulo:
        if num_topicos >= 2: titulo = plantilla_titulo.format(topico1=topicos_articulo[0], topico2=topicos_articulo[1])
        else: titulo = f"Estudio sobre {topicos_articulo[0]}"
    elif "{topico}" in plantilla_titulo: titulo = plantilla_titulo.format(topico=topicos_articulo[0])
    else: titulo = f"Artículo acerca de {topicos_articulo[0]}"
    titulo = titulo.capitalize()

    # --- Resumen (Límite 150) ---
    plantilla_resumen = random.choice(plantillas_resumenes)
    if "{topico1}" in plantilla_resumen and "{topico2}" in plantilla_resumen:
        if num_topicos >= 2: resumen_base = plantilla_resumen.format(topico1=topicos_articulo[0], topico2=topicos_articulo[1])
        else: resumen_base = f"Este documento trata sobre {topicos_articulo[0]} y temas relacionados."
    elif "{topico}" in plantilla_resumen: resumen_base = plantilla_resumen.format(topico=topicos_articulo[0])
    else: resumen_base = f"Análisis detallado sobre {topicos_articulo[0]}."
    resumen = (resumen_base[:147] + '...') if len(resumen_base) > 150 else resumen_base

    fecha_envio = fake.date_between(start_date='-3y', end_date='today').strftime("%d/%m/%Y")

    # Usar la constante global aquí también
    num_autores_articulo = random.randint(1, min(len(autores_disponibles), MAX_AUTORES_PER_ARTICULO))
    autores_articulo = random.sample(autores_disponibles, k=num_autores_articulo)

    return {
        'id_articulo': fake.uuid4(),
        'titulo': titulo,
        'fecha_envio': fecha_envio,
        'resumen': resumen,
        'topicos': topicos_articulo,
        'autores': autores_articulo, 
        'autor_contacto': autores_articulo[0]
    }

def generar_revisores(n, autores_existentes):
    print(f"Generando {n} revisores únicos...")
    start_time = time.time()
    revisores = []
    ruts_usados_global = {autor['rut'] for autor in autores_existentes}
    emails_usados_global = {autor['email'] for autor in autores_existentes}
    ruts_usados_revisores = set()
    emails_usados_revisores = set()

    num_reutilizar = min(n // 3, len(autores_existentes))
    if num_reutilizar > 0:
        autores_a_reutilizar = random.sample(autores_existentes, k=num_reutilizar)
        print(f"Reutilizando {len(autores_a_reutilizar)} autores como revisores.")
    else:
        autores_a_reutilizar = []

    for autor in autores_a_reutilizar:
        revisores.append({
            'rut': autor['rut'], 'nombre': autor['nombre'], 'email': autor['email'],
            'especialidades': random.sample(topicos_posibles, k=random.randint(1, min(len(topicos_posibles), 4)))
        })
        ruts_usados_revisores.add(autor['rut'])
        emails_usados_revisores.add(autor['email'])

    num_nuevos_needed = n - len(revisores)
    if num_nuevos_needed > 0: print(f"Generando {num_nuevos_needed} revisores nuevos...")

    while len(revisores) < n:
        email_candidato = fake.unique.email()
        rut_candidato = fake.unique.rut()
        if rut_candidato not in ruts_usados_global and \
           email_candidato not in emails_usados_global and \
           rut_candidato not in ruts_usados_revisores and \
           email_candidato not in emails_usados_revisores:
            nombre = fake.name()
            revisores.append({
                'nombre': nombre, 'email': email_candidato, 'rut': rut_candidato,
                'especialidades': random.sample(topicos_posibles, k=random.randint(1, min(len(topicos_posibles), 4)))
            })
            ruts_usados_revisores.add(rut_candidato)
            emails_usados_revisores.add(email_candidato)
            nuevos_generados = len(revisores) - len(autores_a_reutilizar)
            if nuevos_generados > 0 and nuevos_generados % 10 == 0:
                print(f"... {nuevos_generados} revisores nuevos generados")

    fake.unique.clear()
    end_time = time.time()
    print(f"Generación de revisores completada. {len(revisores)} revisores en {end_time - start_time:.2f} seg.")
    return revisores


# =========================
# Ejecutar generación
# =========================
start_total_time = time.time()

# --- Cantidades ---
NUM_ARTICULOS = 400
TARGET_AUTORES = 60
TARGET_REVISORES = 70

# --- Nombres de Archivos CSV de Salida ---
AUTORES_FILENAME_CSV = "autores.csv"
REVISORES_FILENAME_CSV = "revisores.csv"
ARTICULOS_FILENAME_CSV = "articulos.csv"

print("--- INICIO DE GENERACIÓN DE DATOS (Sin Try/Except) ---")
autores_globales = generar_autores_unicos(TARGET_AUTORES)
revisores = generar_revisores(TARGET_REVISORES, autores_globales)
print(f"\nGenerando {NUM_ARTICULOS} artículos...")
articulos = []
for i in range(NUM_ARTICULOS):
     art = generar_articulo(autores_globales)
     articulos.append(art)
     if (i + 1) % 50 == 0: print(f"... {i+1}/{NUM_ARTICULOS} artículos generados.")
print(f"Generación de {len(articulos)} artículos completada.")

end_total_time = time.time()
print(f"\n--- GENERACIÓN TOTAL COMPLETADA EN {end_total_time - start_total_time:.2f} SEGUNDOS ---")


# =========================
# Escribir resultados a archivos CSV (Sin Try/Except)
# =========================

# --- Escribir Autores CSV (Sin cambios) ---
print(f"\nEscribiendo autores en: {AUTORES_FILENAME_CSV}")
with open(AUTORES_FILENAME_CSV, 'w', newline='', encoding='utf-8') as csvfile_aut:
    fieldnames = ['RUT', 'Nombre', 'Email']
    writer = csv.writer(csvfile_aut)
    writer.writerow(fieldnames)
    for autor in autores_globales:
        writer.writerow([autor.get('rut', ''), autor.get('nombre', ''), autor.get('email', '')])
print(f"Archivo '{AUTORES_FILENAME_CSV}' guardado.")

# --- Escribir Revisores CSV (Sin cambios) ---
print(f"Escribiendo revisores en: {REVISORES_FILENAME_CSV}")
with open(REVISORES_FILENAME_CSV, 'w', newline='', encoding='utf-8') as csvfile_rev:
    fieldnames = ['RUT', 'Nombre', 'Email', 'Especialidades']
    writer = csv.writer(csvfile_rev)
    writer.writerow(fieldnames)
    for revisor in revisores:
        especialidades = revisor.get('especialidades', [])
        especialidades_str = "|".join(especialidades) if isinstance(especialidades, list) else ''
        writer.writerow([revisor.get('rut', ''), revisor.get('nombre', ''), revisor.get('email', ''), especialidades_str])
print(f"Archivo '{REVISORES_FILENAME_CSV}' guardado.")

# --- Escribir Artículos CSV (Formato de autor modificado) ---
print(f"Escribiendo artículos en: {ARTICULOS_FILENAME_CSV}")
with open(ARTICULOS_FILENAME_CSV, 'w', newline='', encoding='utf-8') as csvfile_art:
    # Definir encabezado fijo + encabezados dinámicos para autores
    fieldnames_base = ['ID_Articulo', 'Titulo', 'Fecha_Envio', 'Resumen', 'Topicos']
    author_fieldnames = []
    for i in range(1, MAX_AUTORES_PER_ARTICULO + 1):
        author_fieldnames.append(f'Nombre_Autor_{i}')
        author_fieldnames.append(f'Email_Autor_{i}')

    # Combinar encabezados
    writer = csv.writer(csvfile_art)
    writer.writerow(fieldnames_base + author_fieldnames) # Escribir encabezado completo

    # Escribir filas de datos
    for art in articulos:
        # Preparar datos base del artículo
        topicos = art.get('topicos', [])
        topicos_str = "|".join(topicos) if isinstance(topicos, list) else ''
        row_data = [
            art.get('id_articulo', ''),
            art.get('titulo', ''),
            art.get('fecha_envio', ''),
            art.get('resumen', ''),
            topicos_str
        ]

        # Preparar datos de autores y rellenar hasta el máximo
        autores_lista = art.get('autores', [])
        author_data_flat = []
        for i in range(MAX_AUTORES_PER_ARTICULO):
            if i < len(autores_lista):
                autor = autores_lista[i]
                author_data_flat.append(autor.get('nombre', '')) # Nombre Autor i
                author_data_flat.append(autor.get('email', ''))  # Email Autor i
            else:
                # Rellenar con vacío si no hay más autores para este artículo
                author_data_flat.append('') # Nombre vacío
                author_data_flat.append('') # Email vacío

        # Combinar datos base y de autores y escribir fila
        writer.writerow(row_data + author_data_flat)

print(f"Archivo '{ARTICULOS_FILENAME_CSV}' guardado.")


# =========================
# Mostrar resumen en consola
# =========================
print("\n--- Resumen Final (Consola) ---")
print(f"* Autores Generados: {len(autores_globales)}")
print(f"* Revisores Generados: {len(revisores)}")
print(f"* Artículos Generados: {len(articulos)}")
print(f"* Archivos CSV creados: '{AUTORES_FILENAME_CSV}', '{REVISORES_FILENAME_CSV}', '{ARTICULOS_FILENAME_CSV}'")
print(f"* Formato Artículos: Columnas fijas + {MAX_AUTORES_PER_ARTICULO*2} columnas para autores (Nombre/Email).")