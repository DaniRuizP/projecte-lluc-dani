# Projecte Lluc - Dani | 24/03/2026 - 2n ASIX | Versió 1

# Aquest arxiu serveix per gestionar les comandes de compra del servei de comandes.

# Proporciona una API REST per crear comandes, comprova l'estoc de productes,
# actualitza la base de dades i envia notificacions a través de RabbitMQ.
# Utilitza Redis com a cache per optimitzar les consultes de productes.

from flask import Flask, request, jsonify
import pymysql, pika, redis, os

app = Flask(__name__)

# Variables d'entorn (arxiu .env)
DB_HOST = os.environ.get('DB_HOST')
DB_USER = os.environ.get('DB_USER')
DB_NAME = os.environ.get('DB_NAME')
REDIS_HOST = os.environ.get('REDIS_HOST')
RMQ_HOST = os.environ.get('RMQ_HOST')

# si existeix l'arxiu xifrat de Docker Secrets
if os.path.exists('/run/secrets/db_password'):
    with open('/run/secrets/db_password', 'r') as secret_file:
        DB_PASSWORD = secret_file.read().strip()
else:
    # Si no estem a Swarm, usem la variable d'entorn .env
    DB_PASSWORD = os.environ.get('DB_PASSWORD')

# Connexió amb Redis per la cache
cache = redis.Redis(host=REDIS_HOST, port=6379, decode_responses=True)

# Endpoint per crear una nova comanda
# Rep les dades de l'usuari i els productes, comprova l'estoc i guarda la comanda a la BD
@app.route('/orders', methods=['POST'])
def create_order():
    # Obtenir les dades de la petició JSON
    data = request.json
    user_id = data.get('user_id')
    products = data.get('products')
    
    try:
        # Connexió a la BBDD
        conn = pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASSWORD, database=DB_NAME)
        with conn.cursor() as cur:
            # Processa cada producte de la comanda
            for pid in products:
                # Comprovar estoc del producte i restar una unitat si n'hi ha disponible
                cur.execute("UPDATE products SET stock = stock - 1 WHERE id = %s AND stock > 0", (pid,))
                if cur.rowcount == 0:
                    # Si no hi ha estoc, fer rollback i retornar un error
                    conn.rollback()
                    return jsonify({"status": "ERROR", "message": f"Sense estoc del producte {pid}"}), 400
                # Inserir la nova comanda a la taula orders amb estat 'PAGAT'
                cur.execute("INSERT INTO orders (user_id, product_id, status) VALUES (%s, %s, 'PAGAT')", (user_id, pid))

        # Confirmació dels canvis a la base de dades (COMMIT;)
        conn.commit()
        conn.close()
        
        # Netejar la cache de Redis per actualitzar la llista de productes
        cache.delete('products')        
        # Enviar notificació a RabbitMQ per al servei de notificacions
        connection = pika.BlockingConnection(pika.ConnectionParameters(host=RMQ_HOST))
        channel = connection.channel()
        channel.queue_declare(queue='order_notifications')
        channel.basic_publish(exchange='', routing_key='order_notifications', body=f'Usuari {user_id} ha comprat {len(products)} producte/s!')
        connection.close()

        # Retornar resposta d'èxit
        return jsonify({"status": "OK", "message": "Comandes desades i enviades a la cua de RabbitMQ!"})
    except Exception as e:
        # Gestionar errors durant la transacció
        return jsonify({"status": "ERROR", "message": str(e)})

# Iniciar el servidor Flask en el port 5002
if __name__ == '__main__': app.run(host='0.0.0.0', port=5002)
