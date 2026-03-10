# 🖥️ Miniprojecte: Creació d'Entorn Virtual Bàsic

Aquesta carpeta conté tota la documentació, evidències i configuracions de la infraestructura base del nostre **Projecte Intermodular (ASIX)**. L'objectiu ha estat dissenyar i desplegar un entorn de virtualització tipus *On-Site* simulant un Centre de Dades (CPD) real, utilitzant maquinari limitat (portàtils amb 16 GB de RAM i Windows).

## 📐 Arquitectura de la Infraestructura

Per tal d'aprofitar al màxim els recursos físics sense comprometre l'estabilitat del sistema amfitrió, s'ha optat per una arquitectura de **Virtualització Niuada (Nested Virtualization)** utilitzant Oracle VirtualBox com a hipervisor base (Tipus 2).

Sobre aquest hipervisor s'han desplegat dos servidors principals:
1. **Servidor de Computació (Proxmox VE):** Encarregat d'executar les màquines virtuals del projecte.
2. **Servidor d'Emmagatzematge (TrueNAS Core):** Actuant com a SAN/NAS per proveir discos i fitxers a través de la xarxa.

### ⚙️ Assignació de Recursos de Maquinari (Hardware)

L'assignació s'ha calculat de forma mil·limètrica per no saturar els 16 GB de RAM de l'equip físic:

*   **Proxmox VE (El Cervell):**
    *   **RAM:** 6 GB | **CPU:** 4 Cores.
    *   **Emmagatzematge:** 1 Disc de 50 GB Dinàmic (Format VDI). Només ocupa l'espai real utilitzat.
    *   **Acceleració:** S'ha forçat l'activació de **KVM Hardware Virtualization (VT-x/AMD-V)** en mode *Host Passthrough* (desactivant el Core Isolation i l'Hyper-V natiu de Windows). Això permet a les màquines virtuals internes (ex. Ubuntu Server) gaudir d'un rendiment gairebé natiu.
*   **TrueNAS Core (L'Emmagatzematge):**
    *   **RAM:** 4 GB | **CPU:** 2 Cores. (Mínim indispensable per al sistema de fitxers ZFS).
    *   **Emmagatzematge (Sistema):** 1 Disc de 10 GB per al SO base.
    *   **Emmagatzematge (Dades):** 2 Discos de 100 GB dinàmics configurats en **Mirror (RAID 1)**.

---

## 🌐 Disseny i Segmentació de Xarxes

Seguint les bones pràctiques d'administració de sistemes i disseny de xarxes per a *Datacenters*, s'ha evitat l'ús d'una xarxa plana. S'han configurat **4 targetes de xarxa (NICs) per servidor**, separant completament els diferents tipus de trànsit:

| Targeta / NIC | Mode a VirtualBox | Funcionalitat Empresarial | Subxarxa / IP |
| :--- | :--- | :--- | :--- |
| **NIC 1** | NAT | **WAN / Internet:** Exclusiva per rebre actualitzacions i descarregar paquets. | 10.0.2.x (DHCP) |
| **NIC 2** | Només-Amfitrió | **Management (OOB):** Xarxa de gestió aïllada. Només accessible per l'Administrador via Web GUI/SSH des del portàtil. | `192.168.10.x / 24` |
| **NIC 3** | Xarxa Interna (`NET-NFS`) | **Storage LAN:** Enllaç directe privat per compartició de fitxers (NFS). | `10.0.30.x / 24` |
| **NIC 4** | Xarxa Interna (`NET-ISCSI`) | **Storage SAN:** Autopista d'alta velocitat exclusiva per trànsit de blocs (iSCSI). Evita colls d'ampolla. | `10.0.40.x / 24` |

---

## 💾 Estratègia d'Emmagatzematge (Storage) Híbrid

Hem implementat una arquitectura d'emmagatzematge híbrida a TrueNAS per donar servei a Proxmox, aprofitant la redundància del ZFS Pool en RAID 1 (`pool-principal`):

1. **Protocol de Fitxers (NFS):**
   * S'ha creat un *Dataset* (`isos-backups`) compartit per NFS a través de la xarxa `10.0.30.x`.
   * **Funció:** Emmagatzemar imatges ISO, plantilles de contenidors i còpies de seguretat (Backups) de Proxmox.
   * *Troubleshooting resolt:* Es va aplicar la configuració `Maproot User: root` per evitar l'error de permisos (Root Squash) en intentar crear el directori `/dump` des de Proxmox.

2. **Protocol de Blocs (iSCSI + LVM):**
   * S'ha creat un *Zvol* de 60 GB (`disco-vms`), compartit mitjançant iSCSI Target a través de la xarxa `10.0.40.x`.
   * Un cop connectat l'LUN a Proxmox, se li ha donat format com a **LVM Compartit**.
   * **Funció:** Allotjar els discos durs virtuals de les màquines en execució, atorgant un rendiment d'Entrada/Sortida (I/O) molt superior al d'un sistema de fitxers tradicional.

---

## 🚀 Resultat Final

Gràcies a aquesta arquitectura, hem estat capaços de crear una **Màquina Virtual d'Ubuntu Server** totalment funcional dins de Proxmox, amb la particularitat que:
* Llegeix l'instal·lador (.iso) des de la xarxa NFS.
* Escriu el seu sistema operatiu en el volum de blocs iSCSI de TrueNAS.
* Opera amb acceleració KVM gràcies a l'ajust de virtualització niuada.

## 📂 Contingut d'aquest directori

* `Annex_Virtualitzacio_Lluc_Dani.pdf`: Document tècnic pas a pas amb totes les captures de pantalla, des de la instal·lació dels SO fins a la configuració d'iSCSI/NFS.
* **Vídeo Demostratiu:** Trobareu l'enllaç al vídeo de demostració del funcionament de l'entorn dins del PDF adjunt.
