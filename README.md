# 🚀 M0379 - Projecte Intermodular ASIX

**Autors:** Lluc Sánchez i Dani Ruiz  
**Cicle:** 2n ASIX (Administració de Sistemes Informàtics en Xarxa)  
**Centre:** I.E.S Sa Palomera (Blanes, Girona)  

---

## 📖 Introducció

Aquest repositori conté tota la documentació, el codi (playbooks, fitxers yaml, scripts) i els esquemes del nostre Projecte Intermodular de final de cicle. 

L'objectiu principal d'aquest projecte ha estat dissenyar, desplegar, automatitzar i assegurar una infraestructura IT completa des de zero, simulant les necessitats d'un entorn empresarial real (On-Site Datacenter).

Per fer-ho, hem partit d'un entorn físic limitat (portàtils amb 16 GB de RAM) i hem fet ús de la **virtualització niuada (Nested Virtualization)** per aixecar una xarxa complexa de servidors, segmentant el trànsit i garantint l'alta disponibilitat i l'eficiència dels serveis.

---

## 🗂️ Índex de Continguts / Miniprojectes

Fes clic en cadascun dels enllaços per accedir als fitxers de configuració i la documentació específica de cada apartat:

1. [🖥️ **Creació d'Entorn Virtual**](./virtualitzacio)
   * *Hipervisor Proxmox VE i Emmagatzematge SAN/NAS amb TrueNAS (ZFS Mirror, NFS, iSCSI LVM).*
2. [🐳 **Orquestradors: Docker Swarm i Kubernetes**](./docker-kubernetes)
   * *Contenirització d'una plataforma e-Commerce (ShopMicro) i alta disponibilitat.*
3.[🤖 **Automatització de la Configuració**](./ansible)
   * *Ús d'Ansible per a l'aprovisionament de servidors, gestió d'usuaris i desplegament de serveis.*
4.[📊 **Monitorització de Xarxa**](./monitoritzacio)
   * *Desplegament de Zabbix per al control de rendiment de maquinari i estat dels serveis.*
5.[🛡️ **Sistema de Detecció i Prevenció d'Intrusions**](./seguretat)
   * *Implementació de Suricata (IDS/IPS) per a la detecció d'amenaces en xarxa.*
6.[📄 **Memòria i Annexos (PDFs)**](./docs)
   * *Documentació oficial del projecte, justificacions tècniques i manuals d'instal·lació pas a pas.*

---

## 🛠️ Stack Tecnològic

Aquest projecte s'ha dut a terme utilitzant exclusivament programari de codi obert o solucions *Enterprise* en els seus formats lliures:

*   **Infraestructura Base:** Oracle VirtualBox (Host), Proxmox VE, TrueNAS Core.
*   **Sistemes Operatius:** Ubuntu Server 24.04 LTS, Debian.
*   **Contenidors:** Docker, Docker Compose, Kubernetes.
*   **Eines DevOps:** Ansible (Python, YAML).
*   **Xarxes i Emmagatzematge:** ZFS, LVM, NFS, iSCSI, Segmentació de Xarxes Internes i NAT.

---

> **Nota per als avaluadors:** 
> Totes les decisions de maquinari i arquitectura de xarxa (com la segmentació de les targetes per a management, iSCSI i NFS) estan documentades i justificades a la memòria principal del projecte per adaptar-se a la virtualització dins d'equips domèstics.

---
Arxiu en procés de finalització....
