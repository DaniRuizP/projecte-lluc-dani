# Projecte Lluc - Dani | 24/03/2026 - 2n ASIX | Versió 1

# Aquest arxiu serveix per controlar les tasques de RabbitMQ

# Constantment llegeix la cua "order_notifications" generada per RabbitMQ
# i deixa el missatge per consola quan hi ha una nova comanda.

# Normalment es sol utilitzar per implementar un servei de mail.

import pika, time, sys, os

# Llegir la variable d'entorn
RMQ_HOST = os.environ.get('RMQ_HOST')


def callback(ch, method, properties, body):
    # Funció que serveix per deixar els missatges per consola utilitzant print()
    print(f"\n[LlucDani - RABBITMQ] -> {body.decode()}\n", flush=True)

while True:
    # Constantment escolta la cua "order_notifications" de RabbitMQ i processa les comandes
    # mostrant-les directament a la terminal utilitzant la funció callback().
    try:
        connection = pika.BlockingConnection(pika.ConnectionParameters(host=RMQ_HOST))
        channel = connection.channel()
        channel.queue_declare(queue='order_notifications')
        print('[*] Notification Service: Connectat a RabbitMQ. Esperant comandes...', flush=True)
        channel.basic_consume(queue='order_notifications', on_message_callback=callback, auto_ack=True)
        channel.start_consuming()
    except Exception as e:
        # Reintentar la connexió si RabbitMQ no es troba disponible.
        print("[!] Esperant RabbitMQ...", flush=True)
        time.sleep(3)
