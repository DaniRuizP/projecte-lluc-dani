# Projecte Lluc - Dani | 24/03/2026 - 2n ASIX | Versió 1

# Última versió de nginx, utilitzo alpine per optimitzar al màxim l'espai i el rendiment del contenidor.
FROM nginx:alpine

# Actualitzar els paquets i descarregar openssl per generar el certificat.
RUN apk update && apk upgrade --no-cache zlib && apk add --no-cache openssl

# Crear directori on es guarda el certificat i generar-lo.
RUN mkdir -p /etc/nginx/ssl && \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/nginx/ssl/server.key \
    -out /etc/nginx/ssl/server.crt \
    -subj "/C=ES/ST=Girona/L=Blanes/O=ASIX/CN=shopmicro.local"

# Copiar arxiu de configuració dins del contenidor.
COPY nginx.conf /etc/nginx/nginx.conf
