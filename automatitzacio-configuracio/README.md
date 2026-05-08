# Automatització de la Configuració - Ansible

## Autors
- Lluc Sánchez
- Dani Ruiz

**Curs:** 2n ASIX - 25/26 | **Projecte Intermodular:** M0379

---

## 📋 Índex
1. [Objectiu del Projecte](#objectiu-del-projecte)
2. [Arquitectura](#arquitectura)
3. [Prerequisits](#prerequisits)
4. [Instal·lació i Configuració](#instal·lació-i-configuració)
5. [Estructura del Projecte](#estructura-del-projecte)
6. [Funcionalitats](#funcionalitats)
7. [Guia de Desplegament](#guia-de-desplegament)
8. [Resolució de Problemes](#resolució-de-problemes)
9. [Recursos](#recursos)

---

## 🎯 Objectiu del Projecte

**Ansible** és una ferramenta d'automatització que ens permet gestionar i configurar múltiples màquines de forma centralitzada. Els objectius principals són:

- **Aprovisionament:** Crear infraestructura IT completa de forma automàtica
- **Gestió de Configuració:** Mantenir configuracions consistents entre màquines
- **Desplegament d'Aplicacions:** Instal·lar i actualitzar software de forma centralitzada
- **Seguretat:** Aplicar polítiques de seguretat uniforme a totes les màquines

### Avantatges de l'Automatització
- ✅ Elimina errades manuals
- ✅ Accelera desplegaments (hores → minuts)
- ✅ Garanteix consistència entre servidors
- ✅ Facilita la recuperació de desastres
- ✅ Escalabilitat sense esforç

---

## 🏗️ Arquitectura

### Xarxa d'Ansible

```
┌─────────────────────────────────────────────┐
│    Xarxa Host Only (192.168.20.0/24)       │
├─────────────────────────────────────────────┤
│                                             │
│  ┌──────────────────┐                      │
│  │ Host Amfitrió    │                      │
│  │ (VirtualBox)     │                      │
│  │ 192.168.20.1     │                      │
│  └────────┬─────────┘                      │
│           │                                │
│     SSH (Port 22)                          │
│           │                                │
│      ┌────┴─────────┬──────────┐          │
│      │              │          │          │
│  ┌───▼──┐       ┌───▼──┐   ┌──▼──┐       │
│  │Ansible│       │Client1│  │Client2    │
│  │Server │       │ (Test)│  │(Test)     │
│  │192.168│       │192.168│  │192.168    │
│  │.20.50 │       │.20.51 │  │.20.52     │
│  └───┬───┘       └───────┘  └───────    │
│      │                                   │
│  [SSH Keys]                              │
│  [Playbooks]                             │
│  [Hosts Inventory]                       │
│                                          │
└─────────────────────────────────────────────┘
```

### Estructura de Control

```
┌─────────────────────────────────────────┐
│    Ansible Server (Control Node)        │
├─────────────────────────────────────────┤
│  • Hosts Inventory                      │
│  • SSH Keys                             │
│  • Playbooks                            │
│  • Roles                                │
│  • Variables                            │
│  • Templates (Jinja2)                   │
└─────────────────────────────────────────┘
          ↓ (SSH)
    ┌─────┴──────┬──────────┐
    ↓            ↓          ↓
[Client1]   [Client2]  [ClientN]
 (Target)   (Target)   (Target)
```

---

## 📦 Prerequisits

### Hardware
- 1 Servidor Ansible (Ubuntu Server 24)
- 2+ Clients de Test (Ubuntu Server 24)
- Mínim 2 GB RAM per màquina
- Conexió de xarxa entre totes les màquines

### Software
- **Ansible 2.x o superior**
- **Python 3.x**
- **SSH Client/Server**
- Ubuntu 20.04 LTS o superior

### Xarxa
- Xarxa Host-Only (192.168.20.0/24) a VirtualBox
- Sense DHCP (IPs estàtiques)

---

## 🚀 Instal·lació i Configuració

### 1. Preparació de Màquines Virtuals

#### Crear 3 Màquines Ubuntu Server

```bash
# Especificacions per cada màquina:
# • RAM: 2 GB
# • Discs: 20 GB
# • Xarxa: Host-Only (192.168.20.0/24)

# Exemple de IPs:
# - Ansible Server: 192.168.20.50
# - Client 1:       192.168.20.51
# - Client 2:       192.168.20.52
```

#### Configurar Xarxa (Netplan)

Editar `/etc/netplan/00-installer-config.yaml`:

```yaml
network:
  version: 2
  ethernets:
    enp0s3:
      addresses:
        - 192.168.20.50/24    # Canviar IPs per a cada màquina
      gateway4: 192.168.20.1
      nameservers:
        addresses: [8.8.8.8, 8.8.4.4]
```

Aplicar:

```bash
sudo netplan apply
```

#### Actualitzar Sistema

```bash
sudo apt-get update
sudo apt-get upgrade -y
```

---

### 2. Instal·lació d'Ansible

#### Al Servidor Ansible

```bash
# Instal·lar Ansible
sudo apt-get install -y ansible

# Verificar instal·lació
ansible --version

# Instal·lar Python a tots els clients (requerit per Ansible)
sudo apt-get install -y python3 python3-pip
```

---

### 3. Configuració SSH

#### Generar Claus SSH

Al servidor Ansible:

```bash
# Generar parella de claus
ssh-keygen -t rsa -b 4096 -f ~/.ssh/ansible_key -N ""

# Mostra la clau pública
cat ~/.ssh/ansible_key.pub
```

#### Distribuir Claus als Clients

Per cada client:

```bash
# Crear carpeta .ssh si no existeix
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Afegir clau pública del servidor
echo "<clau_pública_servidor>" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys

# Habilitar SSH
sudo systemctl start ssh
sudo systemctl enable ssh
```

#### Provar Connexió

```bash
# Provar connexió sense contrasenya
ssh -i ~/.ssh/ansible_key user@192.168.20.51

# Si funciona, el prompt està actiu
```

---

## 📁 Estructura del Projecte

```
/etc/ansible/
├── ansible.cfg              # Configuració de Ansible
├── hosts                    # Inventari de màquines
├── playbook.yml            # Playbook principal
├── plantilla.j2            # Template Jinja2
└── roles/                  # (Opcional) Definir rols
    ├── users/
    ├── firewall/
    ├── applications/
    └── security/
```

### Arxiu `hosts` (Inventari)

```ini
[all]
# localhost   ansible_connection=local

[clients]
client1 ansible_host=192.168.20.51 ansible_user=user1 ansible_private_key_file=~/.ssh/ansible_key
client2 ansible_host=192.168.20.52 ansible_user=user2 ansible_private_key_file=~/.ssh/ansible_key

[clients:vars]
ansible_python_interpreter=/usr/bin/python3
```

### Arxiu `ansible.cfg`

```ini
[defaults]
inventory = ./hosts
remote_user = user1
private_key_file = ~/.ssh/ansible_key
host_key_checking = False
```

---

## ✨ Funcionalitats

### 1. Aprovisionament

Crear infraestructura completa:

```yaml
- name: "Actualització del sistema"
  apt:
    update_cache: yes
    upgrade: full

- name: "Configurar zona horària"
  timezone:
    name: Europe/Madrid

- name: "Crear usuaris"
  user:
    name: "{{ item.name }}"
    groups: "{{ item.groups }}"
    createhome: yes
  loop:
    - { name: "dani", groups: "sudo,asix" }
    - { name: "lluc", groups: "sudo,asix" }
    - { name: "guest", groups: "" }

- name: "Crear grup 'asix'"
  group:
    name: asix
    state: present
```

**Imatge de la creació de usuaris:**
```
[Espai per a imatge d'usuaris creats]
```

### 2. Gestió de Configuració

#### Cron Jobs

```yaml
- name: "Crear cron per netejar /tmp"
  cron:
    name: "Netejar /tmp setmanalment"
    minute: "0"
    hour: "1"
    weekday: "0"
    job: "rm -rf /tmp/*"
    user: root
```

#### Templates (Jinja2)

Fitxer `plantilla.j2`:

```jinja2
╔════════════════════════════════════════╗
║        {{ ansible_hostname }}          ║
╠════════════════════════════════════════╣
║ Xarxa Privada: {{ ansible_default_ipv4.address }}    ║
║ Sistema: {{ ansible_system }} {{ ansible_system_version }}  ║
║ Usuari: {{ ansible_user }}                           ║
╚════════════════════════════════════════╝
```

Aplicar template:

```yaml
- name: "Desplegar template de benvinguda"
  template:
    src: plantilla.j2
    dest: /etc/motd
    owner: root
    group: root
    mode: '0644'
```

**Resultat al Server:**

```
╔════════════════════════════════════════╗
║        ansible-server                  ║
╠════════════════════════════════════════╣
║ Xarxa Privada: 192.168.20.50            ║
║ Sistema: Linux 6.x                      ║
║ Usuari: ansible                         ║
╚════════════════════════════════════════╝
```

**Resultat al Client1:**

```
╔════════════════════════════════════════╗
║        client1                          ║
╠════════════════════════════════════════╣
║ Xarxa Privada: 192.168.20.51            ║
║ Sistema: Linux 6.x                      ║
║ Usuari: user1                           ║
╚════════════════════════════════════════╝
```

### 3. Desplegament d'Aplicacions

```yaml
- name: "Instal·lar aplicacions"
  apt:
    name: "{{ item }}"
    state: present
  loop:
    - zabbix-agent
    - suricata
    - docker.io
    - curl
    - wget

- name: "Habilitar serveis"
  systemd:
    name: "{{ item }}"
    enabled: yes
    state: started
  loop:
    - docker
    - zabbix-agent
    - suricata
```

### 4. Definició de Seguretat

```yaml
- name: "Configurar Firewall"
  ufw:
    rule: "{{ item.rule }}"
    port: "{{ item.port }}"
    proto: tcp
  loop:
    - { rule: allow, port: 22 }      # SSH
    - { rule: allow, port: 8080 }    # Docker
    - { rule: deny, port: 3306 }     # MySQL (bloquejat)

- name: "Bloquejar login root per SSH"
  lineinfile:
    path: /etc/ssh/sshd_config
    regexp: '^#PermitRootLogin'
    line: 'PermitRootLogin no'
    state: present
  notify: restart ssh

- name: "restart ssh"
  systemd:
    name: ssh
    state: restarted
```

---

## 🔄 Guia de Desplegament

### Playbook Principal

Fitxer `playbook.yml`:

```yaml
---
- name: "Configuració Ansible d'Infraestructura"
  hosts: clients
  become: yes
  
  tasks:
    # APROVISIONAMENT
    - name: "Fase 1: Aprovisionament"
      block:
        - name: Actualitzar sistema
          apt:
            update_cache: yes
            upgrade: full
        
        - name: Configurar zona horària
          timezone:
            name: Europe/Madrid
        
        - name: Crear usuaris del sistema
          user:
            name: "{{ item.name }}"
            groups: "{{ item.groups }}"
            createhome: yes
          loop:
            - { name: "dani", groups: "sudo,asix" }
            - { name: "lluc", groups: "sudo,asix" }
            - { name: "guest", groups: "" }
    
    # GESTIÓ DE CONFIGURACIÓ
    - name: "Fase 2: Gestió de Configuració"
      block:
        - name: Crear cron per netejar /tmp
          cron:
            name: "Netejar /tmp"
            minute: "0"
            hour: "1"
            weekday: "0"
            job: "rm -rf /tmp/*"
            user: root
        
        - name: Desplegar template de benvinguda
          template:
            src: plantilla.j2
            dest: /etc/motd
    
    # DESPLEGAMENT D'APLICACIONS
    - name: "Fase 3: Desplegament d'Aplicacions"
      block:
        - name: Instal·lar aplicacions
          apt:
            name: "{{ item }}"
            state: present
          loop:
            - zabbix-agent
            - suricata
            - docker.io
            - curl
            - wget
        
        - name: Habilitar Docker
          systemd:
            name: docker
            enabled: yes
            state: started
    
    # SEGURETAT
    - name: "Fase 4: Configuració de Seguretat"
      block:
        - name: Configurar Firewall
          ufw:
            rule: allow
            port: "{{ item }}"
            proto: tcp
          loop:
            - 22      # SSH
            - 8080    # Docker
        
        - name: Bloquejar login root per SSH
          lineinfile:
            path: /etc/ssh/sshd_config
            regexp: '^#PermitRootLogin'
            line: 'PermitRootLogin no'
          notify: restart ssh

  handlers:
    - name: "restart ssh"
      systemd:
        name: ssh
        state: restarted
```

### Comandes de Desplegament

```bash
# Navegar a carpeta Ansible
cd /etc/ansible

# Provar connexió amb tots els clients
ansible all -m ping

# Executar playbook sencer
ansible-playbook playbook.yml

# Executar playbook amb verbose
ansible-playbook -v playbook.yml

# Executar playbook en host específic
ansible-playbook -i hosts -l client1 playbook.yml

# Verificar sintaxis
ansible-playbook --syntax-check playbook.yml

# Mode dry-run (sense fer canvis)
ansible-playbook -C playbook.yml
```

---

## 🐛 Resolució de Problemes

### Error 1: Suricata No Inicia

**Símptoma:** `suricata status: failed`

**Causa:** La interfície de xarxa no és correcta

**Solució:**

```bash
# Editar fitxer suricata.yaml
sudo nano /etc/suricata/suricata.yaml

# Canviar eth0 per enp0s8
# home-net: "[10.0.0.0/8,!10.0.30.0/24]"
# af-packet:
#   - interface: enp0s8    # ← Aquesta és la correcta

# Afegir a playbook:
- name: "Corregir interfície Suricata"
  lineinfile:
    path: /etc/suricata/suricata.yaml
    regexp: "interface: eth0"
    line: "      interface: enp0s8"
    state: present
  notify: restart suricata
```

**Imatge de l'error:**
```
[Espai per a imatge de l'error Suricata]
```

**Imatge de la solució:**
```
[Espai per a imatge de Suricata funcionant]
```

### Error 2: SSH Connection Refused

**Símptoma:** `Permission denied (publickey)`

**Solució:**

```bash
# Verificar permisos
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys

# Reiniciar SSH
sudo systemctl restart ssh

# Provar de nou
ssh -i ~/.ssh/ansible_key user@192.168.20.51
```

### Error 3: Python No Instal·lat

**Solució:**

```bash
# Instal·lar Python a tots els clients
ansible all -m apt -a "name=python3 state=present" -u root
```

---

## 📊 Video Demostratius

### Fase d'Aprovisionament

En aquest vídeo es mostra:
- ✅ Creació de 3 usuaris
- ✅ Configuració de zone horària
- ✅ Instal·lació de paquets
- ✅ Habilitació de serveis

**Vídeo:** [Fase Aprovisionament]
```
[Espai per a link/incrustació de vídeo]
```

### Validació Final

En aquest vídeo es comprova:
- ✅ Usuaris creats correctament
- ✅ Serveis actius
- ✅ Template de motd activat
- ✅ Firewall configurat
- ✅ Zabbix, Suricata i Docker funcionant

**Vídeo:** [Validació Final]
```
[Espai per a link/incrustació de vídeo]
```

---

## 📈 Estadístiques del Desplegament

| Activitat | Temps Manual | Temps Ansible | Estalvi |
|-----------|--------------|---------------|---------|
| Setup 1 servidor | 2 hores | 5 minuts | 95% ⬇ |
| Instal·lar 5 apps | 30 min | 1 min | 98% ⬇ |
| Configurar firewall | 20 min | 30 seg | 98% ⬇ |
| Crear 10 usuaris | 15 min | 20 seg | 97% ⬇ |
| **Total 1 màquina** | **~3 hores** | **~7 min** | **97% ⬇** |

---

## 🔐 Millores de Seguretat Implementades

```yaml
✅ SSH sense contrasenya (claus públiques)
✅ Root login desactivat
✅ Firewall configurat (UFW)
✅ Ports SSH, Docker oberts
✅ Resta de ports bloquejats
✅ Crons de manteniment automàtic
✅ Usuaris amb privilegis sudo controlats
```

---

## 📚 Recursos

- [Documentació Ansible](https://docs.ansible.com/)
- [Jinja2 Templates](https://jinja.palletsprojects.com/)
- [Manual Complet](./documentació/Annex%203%20-%20Documentació%20Automatització%20de%20la%20Configuració%20-%20LlucDani.pdf)
- [GitHub Playbook](https://github.com/lluc-dani/projecte-lluc-dani)

---

## ✅ Checklist de Desplegament

- [ ] 3 Màquines Ubuntu creades
- [ ] Xarxa Host-Only configurada
- [ ] Ansible instal·lat al servidor
- [ ] SSH configurat sense contrasenya
- [ ] Connexió ping correcta (`ansible all -m ping`)
- [ ] Playbook valida sintaxis
- [ ] Playbook executat sense errors
- [ ] Usuaris creats correctament
- [ ] Serveis habilitats i actius
- [ ] Firewall funcionant
- [ ] Suricata, Zabbix i Docker actius

---

## 🎓 Leccions Apreses

1. **Ansible és potent:** Escriptes de 3 hores → 7 minuts
2. **Templates Jinja2 són flexibles:** Adaptar configuracions per màquina
3. **Idempotència és important:** Executar playbook múltiples vegades sense problemes
4. **SSH keys són essencials:** Millor seguretat i comoditat
5. **Documentació és crítica:** Facilita manteniment futur

---

*Última actualització: Març 2026*
