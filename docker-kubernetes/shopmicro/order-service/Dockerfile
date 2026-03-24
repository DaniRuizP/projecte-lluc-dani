# Projecte Lluc - Dani | 24/03/2026 - 2n ASIX | Versió 1

# Última versió imatge de Python.
FROM python:3.14-alpine

# Docker Best Practices, definir el workdir:
# https://stackoverflow.com/questions/51066146/what-is-the-point-of-workdir-on-dockerfile
WORKDIR /app

# Copiar tots els arxius.
COPY . .

# Instal·lar els requeriments de Python.
RUN pip install -r requirements.txt

# Executar l'aplicació
CMD ["python", "-u", "app.py"]
