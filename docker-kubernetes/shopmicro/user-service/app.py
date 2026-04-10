# Projecte Lluc - Dani | 24/03/2026 - 2n ASIX | Versió 1

# Aquest arxiu serveix per gestionar l'autenticació i registre d'usuaris del servei d'usuaris

# Proporciona una API REST per registrar nous usuaris i autenticar-los.
# Utilitza hash segur de contrasenya amb werkzeug.security per protegir les dades sensibles.
# Emmagatzema els usuaris a la BBDD.

from flask import Flask, request, jsonify
import pymysql, os
from werkzeug.security import generate_password_hash, check_password_hash

app = Flask(__name__)

# Permetre caràcters especials en la resposta JSON
app.config['JSON_AS_ASCII'] = False

# Variables d'entorn (arxiu .env)
DB_HOST = os.environ.get('DB_HOST')
DB_USER = os.environ.get('DB_USER')
DB_NAME = os.environ.get('DB_NAME')
REDIS_HOST = os.environ.get('REDIS_HOST')

# si existeix l'arxiu xifrat de Docker Secrets
if os.path.exists('/run/secrets/db_password'):
    with open('/run/secrets/db_password', 'r') as secret_file:
        DB_PASSWORD = secret_file.read().strip()
else:
    # Si no estem a Swarm, usem la variable d'entorn .env
    DB_PASSWORD = os.environ.get('DB_PASSWORD')

# Funció per obtenir connexió amb la BBDD.
def get_db():
    return pymysql.connect(
        host=DB_HOST, 
        user=DB_USER, 
        password=DB_PASSWORD, 
        database=DB_NAME, 
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )

# Endpoint per autenticar un usuari existent
# Verifica el nom d'usuari i la contrasenya contra la BD
@app.route('/users/login', methods=['POST'])
def login():
    # Obtenir les dades de la petició JSON
    data = request.json
    username = data.get('username')
    password = data.get('password')
    try:
        # Connexió a la BBDD per buscar l'usuari
        conn = get_db()
        with conn.cursor() as cur:
            # Busquem l'usuari pel nom
            cur.execute("SELECT id, username, password FROM users WHERE username=%s", (username,))
            user = cur.fetchone()
        conn.close()
        
        # Comprovem el HASH de la contrasenya amb la llibreria de seguretat
        if user and check_password_hash(user['password'], password):
            # Si l'autenticació és correcta, retornar les dades de l'usuari
            return jsonify({"status": "OK", "user": {"id": user['id'], "username": user['username']}})
        else:
            # Si no es troben les credencials, retornar error
            return jsonify({"status": "ERROR", "message": "Usuari o contrasenya incorrectes"})
    except Exception as e:
        # Gestionar errors durant la consulta
        return jsonify({"status": "ERROR", "message": str(e)})

# Endpoint per registrar un nou usuari
# Crea un compte amb nom d'usuari i contrasenya encriptada
@app.route('/users/register', methods=['POST'])
def register():
    # Obtenir les dades de la petició JSON
    data = request.json
    username = data.get('username')
    password = data.get('password')
    
    # Generar hash segur de la contrasenya
    hashed_password = generate_password_hash(password)
    
    try:
        # Connexió a la BBDD per inserir el nou usuari.
        conn = get_db()
        with conn.cursor() as cur:
            cur.execute("INSERT INTO users (username, password) VALUES (%s, %s)", (username, hashed_password))
        conn.commit()
        conn.close()
        # Retornar confirmació del registre correcte
        return jsonify({"status": "OK", "message": "Compte creat correctament! Ara pots iniciar sessió."})
    except Exception as e:
        # Si el nom d'usuari ja existeix, retornar error
        return jsonify({"status": "ERROR", "message": "Aquest nom d'usuari ja està agafat!"})

# Iniciar el servidor Flask en el port 5003
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5003)
