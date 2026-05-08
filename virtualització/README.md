# Virtualització - Proxmox i TrueNAS

## Autors
- Lluc Sánchez
- Dani Ruiz

**Curs:** 2n ASIX - 25/26 | **Projecte Intermodular:** M0379

---

## 📋 Índex
1. [Objectiu del Projecte](#objectiu-del-projecte)
2. [Estructura de Xarxa](#estructura-de-xarxa)
3. [Prerequisits](#prerequisits)
4. [Instal·lació](#instal·lació)
5. [Configuració](#configuració)
6. [Storage amb TrueNAS](#storage-amb-truenas)
7. [Connexió Proxmox amb TrueNAS](#connexió-proxmox-amb-truenas)
8. [Recursos](#recursos)

---

## 🎯 Objectiu del Projecte

Aquest miniprojecte consisteix en la instal·lació i configuració d'un entorn de virtualització basat en **Proxmox** (hipervisor) i **TrueNAS** (servidor de storage). L'objectiu és crear una infraestructura virtualitzada amb:

- **Proxmox**: Servidor de virtualització per crear màquines virtuals
- **TrueNAS**: Servidor de storage per emmagatzemar dades de forma segura i amb redundància
- Xarxes segmentades per a NFS i iSCSI

### Casos d'Ús
- Crear un entorn de prova per a desenvolupament i testing
- Gestionar múltiples màquines virtuals de forma centralitzada
- Implementar storage compartit amb NFS i iSCSI
- Garantir alta disponibilitat i recuperació de dades

---

## 🌐 Estructura de Xarxa

La xarxa està composta per dues màquines virtuals en VirtualBox amb les següents interfícies:

### Taula de Direccionament

| Servidor | NIC | Xarxa | IP |
|----------|-----|-------|-----|
| **TrueNAS** | enp0s3 | DHCP (Internet) | 10.0.2.15/24 |
| | enp0s8 | Xarxa Només Amfitrió (VB) | 192.168.10.10/24 |
| | enp0s9 | Xarxa Interna (NFS) | 10.0.30.10/24 |
| | enp0s10 | Xarxa Interna (iSCSI) | 10.0.40.10/24 |
| **Proxmox** | nic0 | DHCP (Internet) | 10.0.2.15/24 |
| | vmbr0 | Xarxa Només Amfitrió (VB) | 192.168.10.20/24 |
| | enp0s9 | Xarxa Interna (NFS) | 10.0.30.20/24 |
| | enp0s10 | Xarxa Interna (iSCSI) | 10.0.40.20/24 |

### Diagrama de Xarxa

```
┌─────────────────────────────────────────────────────────┐
│                     VirtualBox                          │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────────────┐      ┌──────────────────┐        │
│  │     TrueNAS      │      │     Proxmox      │        │
│  │  192.168.10.10   │      │  192.168.10.20   │        │
│  │   (Storage)      │      │ (Hipervisor)     │        │
│  │                  │      │                  │        │
│  │  NFS: 10.0.30.10 │◄───►│ NFS: 10.0.30.20  │        │
│  │ iSCSI: 10.0.40.10│      │iSCSI: 10.0.40.20 │        │
│  └──────────────────┘      └──────────────────┘        │
│                                                         │
└─────────────────────────────────────────────────────────┘
           │                           │
           └───────────┬───────────────┘
                       │
              (Host Amfitrió)
```

---

## 📦 Prerequisits

### Hardware Necessari
- Ordinador host amb suport de virtualització (Intel VT-x o AMD-V)
- Mínim 16 GB de RAM (recomanat 32 GB)
- Mínim 500 GB d'espai en disc

### Software Necessari
- **VirtualBox** (darrers versions)
- **ISO de TrueNAS** (descàrrega des de truenas.com)
- **ISO de Proxmox** (descàrrega des de proxmox.com)

### Instal·lacions Prèvies
- VirtualBox instal·lat i funcionant

---

## 🚀 Instal·lació

### 1. Descarregar ISO

```bash
# TrueNAS
https://www.truenas.com/download-truenas-core/

# Proxmox
https://www.proxmox.com/en/proxmox-ve/
```

### 2. Crear Xarxa Només Amfitrió a VirtualBox

1. Obrir VirtualBox → **Tools** → **Network**
2. Crear nova xarxa amb:
   - IP: **192.168.10.0/24**
   - Desactivar servidor DHCP (usar IPs estàtiques)

**Imatge de la configuració:**
```
[Espai per a imatge de configuració de xarxa VirtualBox]
```

### 3. Crear Màquina Virtual TrueNAS

#### Especificacions
- **RAM:** 2048 MB
- **CPU Cores:** 2
- **Disc Sistema:** 10 GB
- **Disc Dades:** 100 GB (dinàmic)
- **Xarxa:** NAT + Host-Only

#### Passos
1. Clic a **New** en VirtualBox
2. Nom: `TrueNAS-LlucDani`
3. ISO: Seleccionar ISO descargada de TrueNAS
4. Assignar RAM i CPU
5. Crear discs virtuals (10 GB + 100 GB)
6. Configurar interfícies de xarxa:
   - Adaptador 1: NAT
   - Adaptador 2: Host-Only (192.168.10.X)
   - Adaptador 3: Xarxa Interna (NFS)
   - Adaptador 4: Xarxa Interna (iSCSI)

**Imatge del resultat de creació:**
```
[Espai per a imatge de màquina virtual TrueNAS creada]
```

### 4. Crear Màquina Virtual Proxmox

#### Especificacions
- **RAM:** 6144 MB
- **CPU Cores:** 4
- **Disc Sistema:** 50 GB (dinàmic)
- **Xarxa:** NAT + Host-Only

#### Passos
1. Clic a **New** en VirtualBox
2. Nom: `Proxmox-LlucDani`
3. ISO: Seleccionar ISO descargada de Proxmox
4. Assignar 6 GB RAM i 4 cores
5. Crear disc de 50 GB
6. Configurar interfícies de xarxa igual que TrueNAS
7. **IMPORTANT:** Activar virtualització niuada (Hyper-V)

#### Activar Virtualització Niuada

A Windows, obrir PowerShell com administrador:

```powershell
cd "C:\Program Files\Oracle\VirtualBox"
.\VBoxManage.exe modifyvm "Proxmox-LlucDani" --nested-hw-virt on
```

**Imatge del resultat de creació:**
```
[Espai per a imatge de màquina virtual Proxmox creada]
```

---

## ⚙️ Configuració

### Configuració TrueNAS

#### 1. Instal·lació
1. Arrancar màquina TrueNAS
2. Seleccionar opció **Install**
3. Escollir disc de 10 GB per al SO
4. Seleccionar `Administrative User` (crearà usuari admin)
5. Assignar contrasenya: `P@ssw0rd`
6. Confirmar instal·lació
7. Treure ISO i reiniciar

**Imatge de la instal·lació:**
```
[Espai per a imatge del procés d'instal·lació]
```

#### 2. Configuració de Xarxa
1. A la consola de TrueNAS, seleccionar opció **1** per configurar interfícies
2. Escollir interfície `enp0s8`
3. Desactivar IPv4 i IPv6
4. Assignar IP estàtica: **192.168.10.10/24**
5. Guardar configuració

#### 3. Accés Web
1. Des del host amfitrió, obrir navegador
2. Accedir a: `https://192.168.10.10`
3. Usuari: `truenas_admin`
4. Contrasenya: `P@ssw0rd`

**Imatge de la interfície web:**
```
[Espai per a imatge de la interfície web de TrueNAS]
```

### Configuració Proxmox

#### 1. Instal·lació
1. Arrancar màquina Proxmox
2. Seleccionar opció **Install**
3. Escollir disc de 50 GB
4. Accedir a la consola i configurar interfícies de xarxa
5. Assignar IP: **192.168.10.20/24**

#### 2. Accés Web
1. Obrir navegador: `https://192.168.10.20:8006`
2. Usuari: `root`
3. Contrasenya: (la definida durant instal·lació)

**Imatge de la interfície web:**
```
[Espai per a imatge de la interfície web de Proxmox]
```

---

## 💾 Storage amb TrueNAS

### 1. Configuració de Discs

#### Pool de Dades
1. A la interfície web de TrueNAS, anar a **Storage** → **Pools**
2. Crear pool utilitzant el disc de 100 GB
3. Assignar tipus de RAID: **Stripe** (sense redundància per a entorn de proves)

**Imatge de la configuració:**
```
[Espai per a imatge de configuració del pool]
```

### 2. Dataset (NFS)

1. Al pool creat, crear dataset
2. Nom: `nfs_dataset`
3. Configurar permisos de lectura/escriptura

### 3. ZVOL (iSCSI)

1. Al pool creat, crear ZVOL
2. Nom: `iscsi_zvol`
3. Tamanys: 50 GB (per a dades de Proxmox)

**Imatge de la configuració:**
```
[Espai per a imatge de ZVOL creat]
```

---

## 🔗 Connexió Proxmox amb TrueNAS

### 1. Configuració NFS

#### A TrueNAS
1. **Sharing** → **NFS**
2. Crear nova compartició
3. Dataset: `nfs_dataset`
4. Xarxes autoritzades: `10.0.30.0/24`

#### A Proxmox
1. **Datacenter** → **Storage**
2. Afegir almacenament NFS
3. ID: `truenas_nfs`
4. Servidor: `10.0.30.10`
5. Camí NFS: `/mnt/pool0/nfs_dataset`

**Imatge de la configuració:**
```
[Espai per a imatge de NFS connectat]
```

### 2. Configuració iSCSI

#### A TrueNAS
1. **Sharing** → **iSCSI**
2. Crear extent amb el ZVOL creat
3. Crear target
4. Autoritzacions: `10.0.40.0/24`

#### A Proxmox
1. **Datacenter** → **Storage**
2. Afegir almacenament iSCSI
3. Portal: `10.0.40.10`
4. Target: (el configurat a TrueNAS)

---

## 📚 Recursos

- [Documentació TrueNAS](https://www.truenas.com/docs/)
- [Documentació Proxmox](https://pve.proxmox.com/wiki/Main_Page)
- [Manual d'Instal·lació Complet](./documentació/Annex%201%20-%20%20Documentació%20Virtualització%20-%20LlucDani.pdf)

---

## 📝 Notes Importants

- Els discs de 100 GB a TrueNAS els hem creat com a dinàmics per estalviar espai inicial
- La virtualització niuada a Proxmox és essencial per crear màquines virtuals dins del Proxmox
- Recomana usar IPs estàtiques a totes les interfícies internes
- Els credencials predefinits (admin/P@ssw0rd) han de ser canviats en producció

---

## ✅ Estat del Projecte

- ✅ Instal·lació de màquines virtuals
- ✅ Configuració de xarxes
- ✅ Configuració de TrueNAS
- ✅ Configuració de Proxmox
- ✅ Storage NFS
- ✅ Storage iSCSI
- ✅ Connexió Proxmox ↔ TrueNAS

---

*Última actualització: Març 2026*
