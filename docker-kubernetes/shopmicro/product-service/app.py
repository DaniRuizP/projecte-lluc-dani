# Projecte Lluc - Dani | 24/03/2026 - 2n ASIX | Versió 1

# Aquest arxiu serveix per gestionar el catàleg de productes del servei de productes

# Proporciona una API REST amb cache de Redis per obtenir productes de manera ràpida.
# Fa consultes a la BBDD i emmagatzema els resultats en Redis per minimitzar la càrrega sobre la BBDD.

from flask import Flask, jsonify
import redis, pymysql, json, os

app = Flask(__name__)
# Permetre caracteres especials en la resposta JSON (per acceptar accents i caràcters especials)
app.config['JSON_AS_ASCII'] = False

# Variables d'entorn (arxiu .env)
DB_HOST = os.environ.get('DB_HOST')
DB_USER = os.environ.get('DB_USER')
DB_PASSWORD = os.environ.get('DB_PASSWORD')
DB_NAME = os.environ.get('DB_NAME')
REDIS_HOST = os.environ.get('REDIS_HOST')

# Connexió amb Redis per la cache
cache = redis.Redis(host=REDIS_HOST, port=6379, decode_responses=True)

# Funció per obtenir connexió a la BBDD.
def get_db(): 
    return pymysql.connect(
        host=DB_HOST, 
        user=DB_USER, 
        password=DB_PASSWORD, 
        database=DB_NAME, 
        charset='utf8mb4',
        cursorclass=pymysql.cursors.DictCursor
    )

# Endpoint per obtenir la llista de tots els productes
# Primer comprova el cache de Redis, si no hi ha dades, les obté de la BBDD i les guarda en cache
@app.route('/products')
def products():
    # Intentar obtenir els productes de la cache de Redis.
    cached = cache.get('products')
    # Si existeix en cache, retornar-los directament amb la font "REDIS"
    if cached: return jsonify({"status": "OK", "source": "REDIS", "data": json.loads(cached)})
    try:
        # Connexió a la base de dades i obtenir tots els productes
        conn = get_db()
        with conn.cursor() as cur:
            cur.execute("SELECT * FROM products")
            prods = cur.fetchall()
        conn.close()
        # Convertir el preu a float
        for p in prods: p['price'] = float(p['price'])
        # Guardar els productes en cau de Redis amb expiració de 60 segons
        cache.set('products', json.dumps(prods, ensure_ascii=False), ex=60)
        # Retornar els productes amb la font "MYSQL"
        return jsonify({"status": "OK", "source": "MYSQL", "data": prods})
    except Exception as e: 
        # Gestionar errors durant la consulta
        return jsonify({"status": "ERROR", "message": str(e)})

# Iniciar el servidor Flask en el port 5001
if __name__ == '__main__': app.run(host='0.0.0.0', port=5001)
