# Docker & Kubernetes - ShopMicro E-commerce

## Autors
- Lluc Sánchez
- Dani Ruiz

**Curs:** 2n ASIX - 25/26 | **Projecte Intermodular:** M0379

---

## 📋 Índex
1. [Objectiu del Projecte](#objectiu-del-projecte)
2. [Arquitectura](#arquitectura)
3. [Fases del Projecte](#fases-del-projecte)
4. [Estructura de Xarxa](#estructura-de-xarxa)
5. [Prerequisits](#prerequisits)
6. [Guia d'Instal·lació](#guia-d'instal·lació)
7. [Fase 1: Docker Compose](#fase-1-docker-compose)
8. [Fase 2: Docker Swarm](#fase-2-docker-swarm)
9. [Fase 3: Seguretat (DevSecOps)](#fase-3-seguretat-devsecops)
10. [Fase 4: Kubernetes](#fase-4-kubernetes)
11. [Recursos](#recursos)

---

## 🎯 Objectiu del Projecte

**ShopMicro** és una plataforma d'e-commerce basada en **microserveis** que evoluciona a través de 4 fases:

1. **Fase 1 (Docker Compose):** Desenvolupament local amb definició declarativa
2. **Fase 2 (Docker Swarm):** Escalat a clúster d'alta disponibilitat
3. **Fase 3 (DevSecOps):** Implementació de mesures de seguretat
4. **Fase 4 (Kubernetes):** Orquestració professional amb k3s

### Objectius Principals
- Abandonar desplegaments monolítics tradicionals
- Implementar arquitectura de microserveis
- Escalar automàticament segons demanda
- Garantir alta disponibilitat i tolerància a fallades
- Implementar pràctiques de seguretat (DevSecOps)

---

## 🏗️ Arquitectura

### Contenidors Principals

```
┌─────────────────────────────────────────────────────┐
│              ShopMicro E-commerce                   │
├─────────────────────────────────────────────────────┤
│                                                     │
│  ┌──────────────┐                                   │
│  │ API Gateway  │  (Nginx Reverse Proxy)            │
│  │ (Port 80/443)│                                   │
│  └────────┬─────┘                                   │
│           │                                         │
│      ┌────┴────┬───────────┬──────────┐            │
│      │          │           │          │            │
│  ┌───▼──┐  ┌───▼──┐  ┌────▼──┐  ┌───▼──┐          │
│  │Front │  │User  │  │Product│  │Order │          │
│  │end   │  │Svc   │  │Svc    │  │Svc   │          │
│  │(PHP) │  │(Py)  │  │(Py)   │  │(Py)  │          │
│  └──┬───┘  └──┬───┘  └───┬───┘  └───┬──┘          │
│     │        │           │         │              │
│     └────────┴─────┬─────┴─────────┘              │
│                    │                              │
│        ┌───────────┼───────────┐                  │
│        │           │           │                  │
│    ┌───▼──┐   ┌───▼──┐   ┌──▼────┐              │
│    │MySQL │   │Redis │   │RabbitMQ│             │
│    │(DB)  │   │Cache │   │MQ      │             │
│    └──────┘   └──────┘   └────┬───┘              │
│                               │                  │
│                        ┌──────▼────┐             │
│                        │Notification│             │
│                        │Service     │             │
│                        │(Worker)    │             │
│                        └─────────────┘             │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### Tecnologies Utilitzades

| Component | Tecnologia | Funció |
|-----------|-----------|--------|
| **API Gateway** | Nginx | Reverse Proxy, SSL Termination |
| **Frontend** | Apache + PHP | Interfície gràfica, gestió sessions |
| **Autenticació** | User Service (Python) | Validació usuaris, hash passwords |
| **Catàleg** | Product Service (Python) | Gestió productes, caching |
| **Comandes** | Order Service (Python) | Transaccions, validació estoc |
| **Notificacions** | Notification Service (Python) | Worker asíncron |
| **Base de Dades** | MySQL | Almacenament persistent |
| **Memòria Cau** | Redis | Cache de productes |
| **Cua de Missatges** | RabbitMQ | Processament asíncron |

---

## 📅 Fases del Projecte

### ✅ Fase 1: Docker Compose
**Objectiu:** Definir l'infraestructura en format declaratiu (YAML)

**Característiques:**
- Únic fitxer `docker-compose.yml`
- Xarxes segregades (frontend, backend)
- Health checks per cada servei
- Volums per a persistència de dades

**Fitxers:**
- `docker-compose.yml` - Definició de tots els serveis
- `Dockerfile` (múltiples) - Per a cada microservei
- `.env` - Variables d'entorn (credencials)

---

### ✅ Fase 2: Docker Swarm
**Objectiu:** Escalar a clúster amb múltiples nodes

**Millores:**
- 3 nodes: 1 Manager + 2 Workers
- Alta disponibilitat
- Replicació de serveis
- Balanceig de càrrega automàtic
- Integració amb TrueNAS (NFS)

**Arxiu:** `docker-stack.yml`

---

### ✅ Fase 3: Seguretat (DevSecOps)
**Objectiu:** Implementar polítiques de seguretat

**Mesures:**
- Docker Secrets per a credencials
- Xarxes overlay xifrades
- Escaneig de vulnerabilitats en imatges
- Gestió de TLS

**Eines:**
- `docker secret` - Gestió de secrets
- Trivy - Escaneig de vulnerabilitats
- Análisis de logs de seguretat

---

### ✅ Fase 4: Kubernetes
**Objectiu:** Migrar a Kubernetes per a producció

**Recursos:**
- `*-deployment.yaml` - Definició de desplegaments
- `*-service.yaml` - Serveis Kubernetes
- `*-configmap.yaml` - Configuracions
- `shop-db-secret.yaml` - Secrets xifrats

**Plataforma:** k3s (Kubernetes lleuger)

---

## 🌐 Estructura de Xarxa

### Taula de Direccionament

| Servidor | NIC | Xarxa | IP |
|----------|-----|-------|-----|
| **Manager** | enp0s3 | DHCP (Internet) | 10.0.2.15/24 |
| | enp0s8 | Docker Network | 10.0.50.10/24 |
| | enp0s9 | Host-Only | 192.168.10.100/24 |
| | enp0s10 | NFS | 10.0.30.60/24 |
| **Worker1** | enp0s3 | DHCP (Internet) | 10.0.2.16/24 |
| | enp0s8 | Docker Network | 10.0.50.20/24 |
| | enp0s9 | Host-Only | 192.168.10.101/24 |
| | enp0s10 | NFS | 10.0.30.61/24 |
| **Worker2** | enp0s3 | DHCP (Internet) | 10.0.2.17/24 |
| | enp0s8 | Docker Network | 10.0.50.30/24 |
| | enp0s9 | Host-Only | 192.168.10.102/24 |
| | enp0s10 | NFS | 10.0.30.62/24 |
| **TrueNAS** | nic0 | DHCP (Internet) | 10.0.2.18/24 |
| | vmbr0 | Host-Only | 192.168.10.20/24 |

### Diagrama de Xarxa

```
┌────────────────────────────────────────────────┐
│            Infraestructura VirtualBox          │
├────────────────────────────────────────────────┤
│                                                │
│  ┌──────────────┐  ┌──────────┐  ┌──────────┐ │
│  │   Manager    │  │  Worker1 │  │  Worker2 │ │
│  │ 192.168.10.100  │ 192.168.10.101 │ 192.168.10.102 │ │
│  │  (Swarm Mgr) │  │  Node    │  │  Node    │ │
│  └──────┬───────┘  └────┬─────┘  └────┬─────┘ │
│         │               │              │      │
│         └───────────────┴──────────────┘      │
│                    │                          │
│         ┌──────────▼─────────┐              │
│         │   TrueNAS Storage  │              │
│         │   192.168.10.20    │              │
│         │   (NFS + iSCSI)    │              │
│         └────────────────────┘              │
│                                                │
└────────────────────────────────────────────────┘
```

---

## 📦 Prerequisits

### Hardware
- Mínim 3 máquinas virtuals Ubuntu Server 24
- 4 GB RAM per màquina (recomanat 8 GB)
- 50 GB disc per màquina

### Software
- Ubuntu Server 24.04 LTS
- Docker Engine (darrers versions)
- Docker Compose
- k3s (per Kubernetes)

### Altres
- TrueNAS amb NFS configurat
- Nginx per a reverse proxy

---

## 🚀 Guia d'Instal·lació

### 1. Preparació de Màquines Virtuals

#### Crear 3 Màquines (Manager + 2 Workers)

```bash
# Per cada màquina, configurar:
# 1. RAM: 6 GB (Manager) / 4 GB (Workers)
# 2. Discs: 50 GB
# 3. Xarxes: NAT + Host-Only + Interna (Docker) + NFS
```

#### Configurar Xarxa (Netplan)

Editar `/etc/netplan/00-installer-config.yaml`:

```yaml
network:
  version: 2
  ethernets:
    enp0s3:
      dhcp4: true
    enp0s8:
      addresses:
        - 192.168.10.100/24  # Canviar per 101, 102...
    enp0s9:
      addresses:
        - 10.0.50.10/24      # Canviar per 20, 30...
    enp0s10:
      addresses:
        - 10.0.30.60/24      # Canviar per 61, 62...
```

Aplicar canvis:

```bash
sudo netplan apply
sudo netplan generate
```

### 2. Instal·lació de Docker

```bash
# Afegir repositori oficial de Docker
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

# Instal·lar Docker
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io

# Afegir usuari al grup docker
sudo usermod -aG docker $USER

# Verificar instal·lació
docker --version
```

---

## 🐳 Fase 1: Docker Compose

### Estructura de Carpetes

```
/opt/shopmicro/
├── docker-compose.yml
├── .env                      (NO al GitHub per seguretat)
├── api-gateway/
│   ├── Dockerfile
│   └── nginx.conf
├── frontend/
│   ├── Dockerfile
│   ├── index.html
│   ├── index.php
│   └── styles/
├── product-service/
│   ├── Dockerfile
│   ├── app.py
│   └── requirements.txt
├── order-service/
│   ├── Dockerfile
│   ├── app.py
│   └── requirements.txt
├── user-service/
│   ├── Dockerfile
│   ├── app.py
│   └── requirements.txt
├── notification-service/
│   ├── Dockerfile
│   ├── app.py
│   └── requirements.txt
└── db-init/
    └── init.sql
```

### Arxiu docker-compose.yml

**Xarxes:**
- `frontend-net`: API Gateway i Frontend
- `backend-net`: Microserveis, BD, Cache, MQ

**Serveis Principals:**

```yaml
services:
  shop-db:
    image: mysql:9.6
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: shop_db
    volumes:
      - db-data:/var/lib/mysql
      - ./db-init/init.sql:/docker-entrypoint-initdb.d/init.sql
    networks:
      - backend-net
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

  redis:
    image: redis:7-alpine
    networks:
      - backend-net
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s

  rabbitmq:
    image: rabbitmq:3-management
    environment:
      RABBITMQ_DEFAULT_USER: ${MQ_USER}
      RABBITMQ_DEFAULT_PASS: ${MQ_PASSWORD}
    networks:
      - backend-net
    ports:
      - "15672:15672"
```

### Comandes Bàsiques

```bash
# Navegar a la carpeta
cd /opt/shopmicro

# Llançar contenidors
docker-compose up -d

# Veure logs
docker-compose logs -f

# Aturar
docker-compose down

# Reconstruir imatges
docker-compose build --no-cache
```

### Diagrames de Fluxos

#### Flux 1: Autentificació d'Usuaris

```
[Client] ──HTTP─→ [API Gateway] ──http─→ [User Service]
                                              │
                                              ├─→ [MySQL] ✓ Credencials
                                              │
                                          [Hash Password]
                                              │
                              [JWT Token] ←──┴──
              ← JWT ← [API Gateway] ← ────
```

#### Flux 2: Consulta de Productes

```
[Client] ──GET /api/products──→ [API Gateway]
                                     │
                              [Product Service]
                                     │
                        ┌────────────┼────────────┐
                        ▼            ▼            ▼
                   [Redis Cache]  [MySQL]   [Backoff Cache]
                        │
                    ✓ Si no existent
                        │
                   [BD Query]
                        │
                   [Redis Update]
```

---

## 🐝 Fase 2: Docker Swarm

### Inicialitzar Swarm

```bash
# Al Manager
docker swarm init --advertise-addr 192.168.10.100

# Obtenir token
docker swarm join-token worker

# Al Worker1 i Worker2
docker swarm join --token SWMTKN... 192.168.10.100:2377
```

### Desplegar Stack

```bash
# Crear secrets
echo "db_password" | docker secret create db_password -
echo "admin_user" | docker secret create admin_user -

# Desplegar
docker stack deploy -c docker-stack.yml shopmicro

# Veure estatus
docker stack services shopmicro
```

### Configuració TrueNAS NFS

A la configuració de Swarm, els volums utilitzaran NFS:

```yaml
volumes:
  db-data:
    driver: local
    driver_opts:
      type: nfs
      o: addr=10.0.30.10,vers=4,soft,timeo=180,bg,tcp,rw
      device: ":/mnt/pool0/nfs_dataset"
```

---

## 🔒 Fase 3: Seguretat (DevSecOps)

### Docker Secrets

Reemplaçar variables d'entorn per secrets:

```bash
# Crear secrets
echo "contrasenya_db" | docker secret create db_pass -
echo "contrasenya_admin" | docker secret create admin_pass -

# Usar al stack
secrets:
  db_pass:
    external: true
  admin_pass:
    external: true
```

### Xarxes Overlay Xifrades

```yaml
networks:
  backend-net:
    driver: overlay
    driver_opts:
      encrypted: "true"
```

### Escaneig de Vulnerabilitats

```bash
# Instal·lar Trivy
curl -sfL https://raw.githubusercontent.com/aquasecurity/trivy/main/contrib/install.sh | sh -s -- -b /usr/local/bin

# Escanejar imatge
trivy image product-service:latest
```

---

## ☸️ Fase 4: Kubernetes (k3s)

### Instal·lació de k3s

```bash
# Al Manager (Master)
curl -sfL https://get.k3s.io | sh -
export KUBECONFIG=/etc/rancher/k3s/k3s.yaml

# Obtenir token
cat /var/lib/rancher/k3s/server/node-token

# Al Workers
curl -sfL https://get.k3s.io | K3S_URL=https://192.168.10.100:6443 K3S_TOKEN=<token> sh -
```

### Recursos Kubernetes

#### ConfigMap

```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: shop-config
data:
  DB_HOST: shop-db-service
  DB_PORT: "3306"
```

#### Secret

```yaml
apiVersion: v1
kind: Secret
metadata:
  name: db-credentials
type: Opaque
data:
  username: YWRtaW4=           # base64 encoded
  password: cGFzc3dvcmQEKQ==   # base64 encoded
```

#### Deployment

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: product-service
spec:
  replicas: 3
  selector:
    matchLabels:
      app: product-service
  template:
    metadata:
      labels:
        app: product-service
    spec:
      containers:
      - name: product-service
        image: product-service:latest
        ports:
        - containerPort: 5000
        env:
        - name: REDIS_HOST
          value: redis-service
        - name: DB_HOST
          valueFrom:
            configMapKeyRef:
              name: shop-config
              key: DB_HOST
```

#### Service

```yaml
apiVersion: v1
kind: Service
metadata:
  name: product-service
spec:
  type: ClusterIP
  ports:
  - port: 5000
    targetPort: 5000
  selector:
    app: product-service
```

### Desplegar a Kubernetes

```bash
# Aplicar resources
kubectl apply -f shop-db-secret.yaml
kubectl apply -f service-configmap.yaml
kubectl apply -f product-service-deployment.yaml
kubectl apply -f product-service-service.yaml

# Veure deployments
kubectl get deployments
kubectl get pods
kubectl get services

# Logs
kubectl logs <pod-name>
```

### Solucionar Problemes

#### Problema 1: DNS no resolent serveis

**Solució:** Usar `<service>.<namespace>.svc.cluster.local`

```yaml
env:
- name: DB_HOST
  value: shop-db-service.default.svc.cluster.local
```

#### Problema 2: PHP no respón

**Solució:** Canviar a PHP-FPM o usar socket unix

#### Problema 3: Port Forwarding

```bash
# Accedir a serveis localment
kubectl port-forward service/api-gateway 8080:80
```

---

## 📚 Recursos

- [Documentació Docker](https://docs.docker.com/)
- [Documentació Kubernetes](https://kubernetes.io/docs/)
- [k3s Documentation](https://docs.k3s.io/)
- [Manual Complet](./Annex%202%20-%20%20Documentació%20Docker-Kubernetes%20-%20LlucDani.pdf)

---

## 🔄 Fluxos de Dades

```
┌─────────────────────────────────────────┐
│      Client (Navegador Web)            │
├─────────────────────────────────────────┤
│                │                        │
│         HTTPS (443)                     │
│                ▼                        │
│    ┌──────────────────────┐            │
│    │   API Gateway (Nginx)│ SSL Term   │
│    │  (Reverse Proxy)     │            │
│    └──────┬───────────────┘            │
│           │                            │
│    HTTP (80) Internal                  │
│    ┌──────┴────────────────┐          │
│    │                       │           │
│    ▼        ▼        ▼     ▼          │
│  [Front]  [User]  [Prod]  [Order]    │
│           Svc      Svc     Svc        │
│                    │ │ │              │
│        ┌───────────┼─┼─┴──────────┐  │
│        │           │              │  │
│        ▼           ▼              ▼  │
│      [MySQL]   [Redis]    [RabbitMQ]│
│       (BBDD)   (Cache)      (MQ)     │
│                               │      │
│                               ▼      │
│                      [Notification] │
│                      Service(Worker)│
│                                     │
└─────────────────────────────────────────┘
```

---

## ✅ Checklist de Desplegament

- [ ] Màquines virtuals configurades (3 nodes)
- [ ] Docker instal·lat en totes les màquines
- [ ] Xarxes configurades correctament
- [ ] Docker Compose funcionant (Fase 1)
- [ ] Swarm inicialitzat (Fase 2)
- [ ] NFS connectat correctament
- [ ] Secrets configurats (Fase 3)
- [ ] k3s instal·lat (Fase 4)
- [ ] Tots els Pods en estat Running
- [ ] API accessible per HTTP/HTTPS

---

*Última actualització: Abril 2026*
