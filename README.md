# 🌐 PTC Web Platform - Core System

PTC (Paid-To-Click) adalah sistem web yang memberikan pengguna imbalan (reward) untuk melakukan klik dan melihat iklan sponsor. Platform ini dirancang untuk menjadi penghubung antara pengiklan dan pengguna, dengan sistem perhitungan reward yang transparan, aman, dan efisien.

## 🚀 Fitur Utama

### 👥 Sistem Pengguna
- Registrasi dan login aman menggunakan sistem autentikasi modern.
- Dashboard pengguna untuk melihat statistik klik, saldo, dan histori transaksi.
- Referral system (1 atau lebih level) dengan pelacakan akurat.

### 💼 Sistem Admin
- Manajemen iklan (approve, tolak, atau hapus iklan).
- Kelola pengguna (ban, suspend, periksa saldo, atau ubah data).
- Statistik real-time: jumlah klik harian, penghasilan platform, performa iklan.

### 📢 Iklan & Kampanye
- Pengiklan dapat membeli slot iklan menggunakan saldo atau metode pembayaran lain.
- Jenis iklan yang didukung: iklan banner, iklan view timer (dengan durasi tertentu), shortlink, dan external redirect.
- Sistem anti-bot dan pelacakan waktu untuk memastikan pengguna benar-benar melihat iklan.

### 💰 Sistem Reward
- Reward diberikan ke pengguna setelah menyelesaikan view timer.
- Dukungan sistem bonus harian, klik beruntun (streak), dan hadiah dari referral.
- Withdrawal balance dengan metode pembayaran manual atau otomatis (seperti PayPal, crypto wallet, dll).

### 🧠 Keamanan & Anti-Fraud
- Anti cheat untuk view timer (tidak bisa dilewati tanpa menunggu).
- Validasi klik dengan token khusus untuk mencegah manipulasi melalui script.

---

# 📢 DISCLAIMER

Proyek ini dikembangkan sebagai **kernel sistem PTC (Paid-To-Click)** dan ditujukan **khusus untuk tujuan pembelajaran, studi teknis, atau pengembangan internal**.

---

## ❗ PERINGATAN

- Sistem ini **tidak boleh digunakan secara langsung dalam produksi** tanpa penyesuaian, pengujian, dan audit keamanan menyeluruh.
- **Dilarang keras** menggunakan sistem ini untuk kegiatan ilegal, penipuan (fraud), atau skema yang menyesatkan (scam).
- Penggunaan nama, kode, atau struktur sistem ini **sepenuhnya menjadi tanggung jawab pengguna**.

---

## 📚 TUJUAN UTAMA

- Studi sistem reward berbasis klik.
- Simulasi backend/admin & user dashboard.
- Eksplorasi fitur seperti referral, manajemen iklan, dan keamanan anti-cheat.

---

Terima kasih telah menggunakan proyek ini secara etis dan bertanggung jawab.

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP (CodeIgniter 4)
- **Frontend**: Bootstrap 5
- **Database**: MySQL
- **Security**: CSRF Protection, Session Hardening
---

## ⚙️ Core System Workflow

1. **User Registration**  
   ⤷ Input data → Validasi → Simpan user → Kirim email verifikasi (opsional)

2. **Login & Dashboard Access**  
   ⤷ Login → Cek status akun → Redirect ke dashboard

3. **Melihat Iklan**  
   ⤷ Pilih iklan → Redirect ke halaman view timer → Hitung mundur → Submit → Tambah reward ke saldo
---

## 📈 Statistik & Monitoring

- Jumlah total klik, pengguna aktif, dan iklan berjalan
- Grafik harian / mingguan untuk admin
- Ringkasan penghasilan pengguna dan biaya iklan

---

## 🔐 Catatan Tambahan

- Sistem ini **bukan HYIP**, hanya perantara antara pengiklan dan pengguna.
- Core ini dapat dikembangkan ke fitur lebih lanjut seperti **shortlink monetization**, **PTSU (Paid To Sign Up)**, atau **CPA offerwall**.
- Dukungan captcha di login dan view-ads sangat disarankan untuk mencegah bot.

---

## 📝 Lisensi

Proyek ini bersifat private/internal dan dilarang keras digunakan untuk tujuan penipuan atau scam. Gunakan hanya untuk platform yang sah dan transparan.

---

## ✉️ Kontak

Untuk pertanyaan atau kerjasama:
- Email: [miuprix@gmail.com]
