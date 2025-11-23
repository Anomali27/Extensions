# 🎮 Sistem Pemesanan Billing PlayStation Secara Online

## 📖 Deskripsi
Aplikasi berbasis web untuk memudahkan pemilik rental PlayStation dalam mengatur jadwal serta memberikan kemudahan bagi pelanggan untuk melakukan pemesanan billing secara online.

---

### 🔎 Identifikasi Masalah Mengenai Pemilik Rental PlayStation dan Pelanggan
- **Kurangnya Efisiensi**: Proses pemesanan dan penagihan manual memakan waktu dan rentan kesalahan.  
- **Manajemen Data yang Buruk**: Data pemesanan dan riwayat pelanggan tidak tersimpan dengan rapi.  
- **Ketidakpastian Ketersediaan**: Pelanggan tidak bisa mengecek ketersediaan unit dan jam main dari jarak jauh.  
- **Proses Penagihan yang Tidak Transparan**: Perhitungan biaya seringkali dilakukan secara manual, yang bisa menimbulkan kesalahpahaman.  

---

## 🚀 Fitur
- Pemesanan billing PlayStation secara online.  
- Daftar billing & jadwal bermain.  
- Pemilihan room dengan status *Available* / *Booked*.  
- Konfirmasi sebelum pembayaran.  
- Desain responsif dan mudah digunakan.  
- Database MySQL untuk penyimpanan data user serta pemesanan.

---

## 🧑‍💻 Fitur Detail (User & Admin)

### 👤 **Fitur untuk User**
- **Register & Login** – Akun tersimpan di database.
- **Top Up Saldo** – Saldo bertambah dan tercatat di `topup_history`.
- **Pemesanan Billing PlayStation** – Sistem otomatis memotong saldo sesuai harga billing.
- **Cek Riwayat Booking** – User dapat melihat dan membatalkan pesanan.
- **Status Room** – Cek ketersediaan room secara real-time.
- **(Opsional)** Pilih durasi bermain, pilih room tipe VIP/Standard.

---

### 🛠️ **Fitur untuk Admin**
- **Dashboard Admin** menampilkan total user, booking, rooms, inventory, top up, payment.
- **CRUD User**
- **Edit / Cancel Booking**
- **Manajemen Rooms** *(masih dalam perbaikan)*
- **Manajemen Inventory** *(belum sepenuhnya stabil)*
- **Riwayat Top Up & Payment**, semuanya terhubung dengan saldo user.

---

## 🗄️ Entitas (Tabel Database)

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Menyimpan data user termasuk saldo |
| `rooms` | Menyimpan data unit/room PlayStation |
| `inventory` | Daftar perangkat PlayStation |
| `bookings` | Pemesanan unit PlayStation oleh user |
| `topup_history` | Catatan top up saldo |
| `payment_history` | Riwayat pemotongan saldo untuk booking |


---

## 🛢️ Setup Database

1. **Buat database baru dengan nama:*playstation-biling*
*(Perhatikan: hanya 1 huruf L pada "biling")*

2. **Import file SQL yang ada di repository**
- File: `playstation-biling.sql`
- Import melalui phpMyAdmin / Adminer
  1. Pilih database `playstation-biling`
  2. Klik **Import**
  3. Upload file SQL tersebut
  4. Jalankan

3. **Pastikan tabel berikut sudah ada setelah import:**
| `users` |
| `rooms` | 
| `inventory` |
| `bookings` |
| `topup_history` |
| `payment_history` |

4. **Database telah siap dan web dapat di jalankan menggunakan laragon dengan localhost**


## 💻 Teknologi yang Digunakan
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" height="40" alt="javascript logo"  />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" height="40" alt="html5 logo"  />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" height="40" alt="css logo"  />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" height="40" alt="php logo"  />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" height="40" alt="mysql logo"  />
  <img width="12" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/figma/figma-original.svg" height="40" alt="figma logo"  />

