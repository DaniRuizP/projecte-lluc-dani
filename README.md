# M0379 - Projecte Intermodular ASIX

**Autors:** Lluc Sánchez i Dani Ruiz  
**Cicle:** 2n ASIX (Administració de Sistemes Informàtics en Xarxa)  
**Centre:** I.E.S Sa Palomera (Blanes, Girona)  
**Data:** Març - Abril 2026

---

## Introducció

Aquest repositori conté tota la documentació, el codi (playbooks, fitxers yaml, scripts, dockerfiles) i els esquemes del nostre **Projecte Intermodular de final de cicle**. 

L'objectiu principal ha estat dissenyar, desplegar, automatitzar i assegurar una **infraestructura IT completa** des de zero, simulant les necessitats d'un entorn empresarial real (On-Site Datacenter).

### Filosofia del Projecte

Hem partit d'un entorn físic limitat (portàtils amb 16 GB de RAM) i hem fet ús de la **virtualització niuada (Nested Virtualization)** per aixecar una xarxa complexa de servidors. L'arquitectura inclou:

-  **Emmagatzematge centralitzat** amb redundància (TrueNAS + ZFS)
-  **Hipervisors** per gestionar VMs (Proxmox)
-  **Contenidors** per a escalabilitat (Docker → Kubernetes)
-  **Automatització** per eliminar errades manuals (Ansible)
-  **Seguretat** per detectar i prevenir amenaces (pfSense + Suricata)

---

##  Miniprojectes

### 1. [Virtualització - Proxmox i TrueNAS](./virtualització)

**Objectiu:** Crear entorn virtualitzat amb hipervisor i storage centralitzat

**Què hi trobaràs:**
- Instal·lació de Proxmox VE (hipervisor de virtualització)
- Instal·lació de TrueNAS (servidor de storage)
- Configuració de xarxes segmentades (DHCP, NFS, iSCSI)
- Integració de storage amb NFS i iSCSI

**Tecnologies:**
- Proxmox VE (KVM-based hypervisor)
- TrueNAS Core (ZFS storage)
- VirtualBox (virtualització física)
- NFS i iSCSI (protocoles de storage)

---

### 2. [Docker & Kubernetes - ShopMicro E-commerce](./docker-kubernetes)

**Objectiu:** Desplegar plataforma e-commerce escalable amb microserveis

**Fase 1:** Docker Compose (desenvolupament local)
- Definir serveis en format declaratiu
- Frontend (PHP), Backend (Python), BD (MySQL), Cache (Redis), MQ (RabbitMQ)

**Fase 2:** Docker Swarm (alta disponibilitat)
- Clúster amb 1 Manager + 2 Workers
- Integració amb TrueNAS per a storage compartit

**Fase 3:** DevSecOps (seguretat)
- Docker Secrets per a credencials
- Xarxes overlay xifrades
- Escaneig de vulnerabilitats

**Fase 4:** Kubernetes (producció)
- Migració a k3s
- Deployments, Services, ConfigMaps, Secrets

**Tecnologies:**
- Docker & Docker Compose
- Docker Swarm
- Kubernetes (k3s)
- Python (Flask) per a microserveis
- MySQL, Redis, RabbitMQ
- Nginx (API Gateway)

---

### 3. [Automatització - Ansible](./automatitzacio-configuracio)

**Objectiu:** Automatitzar configuració de servidors de forma centralitzada

**Funcionalitats:**
- **Aprovisionament:** Crear infraestructura automàticament
- **Gestió de configuració:** Mantenir consistència entre màquines
- **Desplegament:** Instal·lar aplicacions de forma centralitzada
- **Seguretat:** Aplicar polítiques uniforme

**Implementat:**
- Creació de usuaris i grups
- Configuració de firewall (UFW)
- Instal·lació automàtica de Docker, Zabbix, Suricata
- Templates Jinja2 per a configuracions dinàmiques
- Crons per a manteniment automàtic

**Tecnologies:**
- Ansible (orchestration)
- Python
- YAML (configuration as code)
- Jinja2 (templating)
- SSH (remote execution)

---

### 4. [IDS/IPS - pfSense + Suricata](./IDS-IPS)

**Objectiu:** Detectar i prevenir amenaces en la xarxa

**Components:**
- **pfSense:** Firewall + Router per protegir la xarxa
- **Suricata:** Motor IDS/IPS para detectar atacs

**Funcionalitats:**
- Monitoratge de tràfic en temps real
- Detecció de patrons maliciosos
- Bloqueig automàtic d'amenaces
- Genació de logs i alertes
- Simulació d'atacs per validar seguretat

**Amenaces detectades:**
- Atacs DDoS
- Escanneig de ports
- Malware i exploits
- Tràfic sospitós
- Accés no autoritzat

**Tecnologies:**
- pfSense (firewall i routing)
- Suricata (IDS/IPS engine)
- Rules signatures (ET Pro / ET Open)
- EVE JSON logs
- GeoIP Blocking
- pfBlockerNG

---

## Stack Tecnològic

### Infraestructura Base
- **Oracle VirtualBox** - Virtualització del host
- **Proxmox VE** - Hipervisor per a VMs
- **TrueNAS Core** - Storage centralitzat amb ZFS

### Sistemes Operatius
- **Ubuntu Server 24.04 LTS** - Màquines virtuals
- **Debian** - Servidors especialitzats

### Contenidors i Orquestració
- **Docker** - Containerització
- **Docker Compose** - Definició d'aplicacions
- **Docker Swarm** - Orquestració bàsica
- **Kubernetes (k3s)** - Orquestració avançada

### Automatització
- **Ansible** - Provisionament i gestió de configuració
- **Python** - Scripting i microserveis
- **YAML** - Infrastructure as Code

### Aplicacions
- **Apache + PHP** - Frontend web
- **MySQL** - Base de dades relacional
- **Redis** - Cache in-memory
- **RabbitMQ** - Cua de missatges
- **Nginx** - API Gateway i reverse proxy

### Seguretat
- **pfSense** - Firewall i routing
- **Suricata** - IDS/IPS
- **SSH** - Accés segur remot
- **Docker Secrets** - Gestió de credencials

### Emmagatzematge
- **ZFS** - Sistema de fitxers amb redundància
- **NFS** - Network File System
- **iSCSI** - Storage over network

---

## Estructura de Carpetes

```
projecte-lluc-dani/
│
├── README.md                          ← Estàs aquí
│
├── 🖥️ virtualització/
│   ├── README.md                      (Documentació completa)
│   ├── documentació/
│   │   └── Annex 1 - Documentació Virtualització...pdf
│   ├── diagrama_pfsense.png
│   └── ...
│
├── 🐳 docker-kubernetes/
│   ├── README.md                      (Documentació completa)
│   ├── Annex 2 - Documentació Docker-Kubernetes...pdf
│   ├── Docker/
│   │   └── shopmicro/
│   │       ├── docker-compose.yml
│   │       ├── docker-stack.yml
│   │       ├── api-gateway/
│   │       ├── frontend/
│   │       ├── product-service/
│   │       ├── order-service/
│   │       ├── user-service/
│   │       ├── notification-service/
│   │       └── db-init/
│   └── Kubernetes/
│       └── shopmicro/
│           ├── *-deployment.yaml
│           ├── *-service.yaml
│           ├── *-configmap.yaml
│           └── *-secret.yaml
│
├── 🤖 automatitzacio-configuracio/
│   ├── README.md                      (Documentació completa)
│   ├── ansible/
│   │   ├── hosts
│   │   ├── playbook.yml
│   │   └── plantilla.j2
│   ├── documentació/
│   │   ├── Annex 3 - Documentació Automatització...pdf
│   │   └── diagrama_ansible.png
│   └── ...
│
├── 🛡️ IDS-IPS/
│   ├── README.md                      (Documentació completa)
│   ├── Annex 4 - Documentació IDS-IPS...pdf
│   ├── backup_pfsense.xml
│   ├── diagrama_pfsense.png
│   └── ...
│
└── .git/                              (Git repository)
    └── ...
```

---

## Com Fer Ús d'aquest Repositori

### 1. Descarregar PDFs de Referència

Cada carpeta conté la documentació oficial en PDF:

- [Virtualització PDF](./virtualització/documentació/Annex%201%20-%20%20Documentació%20Virtualització%20-%20LlucDani.pdf)
- [Docker-Kubernetes PDF](./docker-kubernetes/Annex%202%20-%20%20Documentació%20Docker-Kubernetes%20-%20LlucDani.pdf)
- [Automatització PDF](./automatitzacio-configuracio/documentació/Annex%203%20-%20Documentació%20Automatització%20de%20la%20Configuració%20-%20LlucDani.pdf)
- [IDS-IPS PDF](./IDS-IPS/Annex%204%20-%20Documentació%20Sistema%20de%20Detecció%20i_o%20Prevenció%20d'Intrusions%20-%20LlucDani.pdf)

### 2. Replicar la Infraestructura

Seguir els passos documentats en cada miniprojecte per recrear l'entorn complet.

---

## Documents de Referència

### Documentació de Projecte
- [Annex 1: Virtualització](./virtualització/documentació/)
- [Annex 2: Docker-Kubernetes](./docker-kubernetes/)
- [Annex 3: Automatització](./automatitzacio-configuracio/documentació/)
- [Annex 4: IDS-IPS](./IDS-IPS/)

### Recursos Externals
- [Proxmox Documentation](https://pve.proxmox.com/wiki/Main_Page)
- [TrueNAS Documentation](https://www.truenas.com/docs/)
- [Docker Documentation](https://docs.docker.com/)
- [Kubernetes Documentation](https://kubernetes.io/docs/)
- [Ansible Documentation](https://docs.ansible.com/)
- [pfSense Documentation](https://docs.netgate.com/pfsense/en/latest/)
- [Suricata Documentation](https://suricata.io/documentation/)

---

## Estadístiques del Projecte

| Métrica | Valor |
|---------|-------|
| **Miniprojectes** | 5 |
| **Documentació (pàgines)** | 200+ |
| **PDFs** | 5 annexos complets |
| **Màquines virtuals** | 10+ |
| **Contenidors Docker** | 9+ |
| **Pods Kubernetes** | 9+ |
| **Hores de treball** | 200+ |
| **Nivell de complexitat** | Avançat |

---

## Requisits Completats

### ✓ Infraestructura
- [x] Virtualització niuada (Proxmox + TrueNAS)
- [x] Storage centralitzat (NFS + iSCSI)
- [x] Xarxes segmentades
- [x] Alta disponibilitat

### ✓ Aplicacions
- [x] E-commerce amb microserveis (ShopMicro)
- [x] Frontend (PHP)
- [x] API Gateway (Nginx)
- [x] Microserveis (Python)

### ✓ Orquestració
- [x] Docker Compose
- [x] Docker Swarm
- [x] Kubernetes (k3s)
- [x] Escalabilitat automàtica

### ✓ Automatització
- [x] Aprovisionament amb Ansible
- [x] Gestió de configuració
- [x] Desplegament d'aplicacions
- [x] Configuració de seguretat

### ✓ Seguretat
- [x] Firewall (pfSense)
- [x] IDS/IPS (Suricata)
- [x] Segmentació de xarxes
- [x] Docker Secrets

---

## Tecnologies Aprenentgudes

```
DevOps:        Ansible, Docker, Kubernetes, Git
Seguretat:     pfSense, Suricata, SSH, Secrets
Xarxes:        Segmentació, NAT, NFS, iSCSI, VPN
Cloud:         Microserveis, Contenidors, Orquestració
Linux:         Ubuntu Server, Shell scripting
IaC:           YAML, Ansible, Dockerfiles
BD:            MySQL, Redis, RabbitMQ
Frontend:      PHP, Apache, HTML/CSS
Backend:       Python (Flask)
```

---

## Nota per als Avaluadors

Totes les decisions de maquinari, arquitectura i configuració estan **documentades i justificades** en els PDFs. La complexitat del projecte s'adapta a un entorn de laboratori amb recursos limitats, però segueix les millors pràctiques empresarials.

---

## Autors

- **Lluc Sánchez** - Virtualització i Orquestració
- **Dani Ruiz** - Automatització i Seguretat

---

## Llicència

Projecte educatiu - I.E.S Sa Palomera (2026)

---

**Última actualització:** Abril 2026
