# Minimalist Merch Admin Dashboard - Design Specification

Dokumen ini merangkum spesifikasi desain untuk **Minimalist Merch Admin Dashboard** (Achromatic Commerce). Desain ini mengusung estetika premium minimalis yang bersih, fungsional, dan berfokus pada visual produk serta penyajian data dengan kejelasan optimal.

---

## 🎨 Palet Warna (Color Palette)

Sistem warna ini berbasis monokromatik dengan aksen fungsional yang sangat spesifik.

### Warna Utama & Permukaan (Core Tones & Surfaces)

| Kegunaan | Token Warna | Nilai HEX |
| :--- | :--- | :--- |
| **Primary (Utama)** | `primary` | `#091426` |
| **Primary Container** | `primary-container` | `#1e293b` |
| **Secondary (Sekunder)** | `secondary` | `#505f76` |
| **Secondary Container** | `secondary-container` | `#d0e1fb` |
| **Background (Latar Belakang)** | `background` | `#fbf8fa` |
| **Surface (Permukaan Utama)** | `surface` | `#fbf8fa` |
| **Surface Dim** | `surface-dim` | `#dcd9db` |
| **Surface Container Lowest** | `surface-container-lowest` | `#ffffff` |
| **Surface Container Low** | `surface-container-low` | `#f5f3f4` |
| **Surface Container** | `surface-container` | `#f0edef` |
| **Surface Container High** | `surface-container-high` | `#eae7e9` |
| **Surface Container Highest**| `surface-container-highest`| `#e4e2e3` |
| **Outline** | `outline` | `#75777d` |
| **Outline Variant** | `outline-variant` | `#c5c6cd` |

### Teks & Kontras (Typography & Contrast)

- **On Primary**: `#ffffff`
- **On Secondary**: `#ffffff`
- **On Surface**: `#1b1b1d` (Arang Tua)
- **On Surface Variant**: `#45474c`
- **Inverse Surface**: `#303032`
- **Inverse On Surface**: `#f3f0f2`

### Aksen Semantik (Status & Errors)

- **Error**: `#ba1a1a`
- **On Error**: `#ffffff`
- **Error Container**: `#ffdad6`
- **On Error Container**: `#93000a`
- **Status Paid (Emerald Green)**: Teks hijau emerald di atas latar belakang hijau emerald dengan opasitas 10%.
- **Status Pending (Amber)**: Teks amber di atas latar belakang amber dengan opasitas 10%.

---

## 📐 Tata Letak & Spasi (Layout & Spacing)

Sistem layout menggunakan grid fleksibel (Fluid Grid) dengan batas lebar maksimal yang konsisten untuk menjaga keterbacaan data.

### Sistem Grid

- **Desktop**: 12-kolom grid dengan *gutter* 24px. Margin luar minimal 32px untuk memperkuat kesan premium dan lapang.
- **Tablet**: 8-kolom grid dengan *gutter* 20px.
- **Mobile**: 4-kolom grid dengan *gutter* 16px.

### Skala Spasi (Spacing Scale)

Menggunakan basis unit **4px** dengan panduan implementasi berikut:
- **`unit`**: 4px
- **`stack-sm`**: 8px (untuk pengelompokan data/elemen erat)
- **`stack-md`**: 16px (spasi standar antar komponen menengah)
- **`stack-lg`**: 24px (untuk memisahkan bagian/section besar)
- **`container-padding`**: 32px

---

## ✍️ Tipografi (Typography)

Sistem desain ini menggunakan font **Inter** untuk keterbacaan yang tinggi pada berbagai ukuran layar.

| Jenis | Ukuran Font | Weight | Line Height | Letter Spacing |
| :--- | :--- | :--- | :--- | :--- |
| **display-lg** | 32px | 600 (Semibold) | 40px | -0.02em |
| **headline-md** | 24px | 600 (Semibold) | 32px | -0.01em |
| **headline-sm** | 20px | 500 (Medium) | 28px | Normal |
| **body-lg** | 16px | 400 (Regular) | 24px | Normal |
| **body-md** | 14px | 400 (Regular) | 20px | Normal |
| **label-md** | 12px | 600 (Semibold) | 16px | 0.05em |
| **label-sm** | 11px | 500 (Medium) | 14px | Normal |
| **display-lg-mobile** | 24px | 600 (Semibold) | 32px | Normal |

---

## 📦 Elemen Bentuk & Kedalaman (Shapes & Elevation)

### Sudut Lengkung (Border Radius / Roundness)

- **Standard Components**: `0.25rem` (4px) — digunakan pada kolom input, tombol kecil, dan kontainer dasar.
- **Data Cards**: `0.5rem` (8px) — digunakan pada blok struktural utama seperti kartu ringkasan atau grafik.
- **Status Badges**: `9999px` (Full Pill) — digunakan pada penanda status agar berbentuk kapsul dan mudah diidentifikasi.

### Kedalaman & Elevasi (Elevation & Depth)

Desain ini menghindari bayangan tebal (heavy shadows) dan lebih mengutamakan lapisan warna tonal serta garis tepi kontras rendah:
- **Level 0 (Base)**: Pure White (`#ffffff`) atau Subtle Gray (`#f8fafc`).
- **Level 1 (Cards/Tables)**: Latar belakang putih dengan border tipis `1px solid #e2e8f0`.
- **Interactive Elevation**: Efek melayang (hover) menggunakan bayangan lembut yang difus: `0 4px 12px rgba(0, 0, 0, 0.03)`.
- **Sidebar & Header**: Diletakkan pada bidang utama putih, sedangkan area konten berada di atas bidang abu-abu redup (`#f8fafc`) untuk efek kedalaman berlapis yang minimalis.

---

## 🧩 Komponen Utama (Core Components)

### 1. Tombol (Buttons)
- **Primary**: Latar belakang Deep Charcoal (`#1e293b`), teks Putih (`#ffffff`), tanpa border.
- **Secondary**: Latar belakang transparan, border `1px solid #cbd5e1`, teks Slate Gray (`#64748b`).
- **Ghost**: Tanpa border dan latar belakang; hanya teks hingga di-hover (di mana latar belakang redup `#f8fafc` akan muncul).

### 2. Badge Status (Status Badges)
- **Paid**: Teks Hijau Emerald pada latar belakang Hijau Emerald dengan opasitas 10%.
- **Pending**: Teks Amber pada latar belakang Amber dengan opasitas 10%.
- **Gaya**: Padding rapat (4px atas/bawah, 12px kiri/kanan), ukuran 11px uppercase.

### 3. Tabel Data (Data Tables)
- **Header**: Teks Slate Gray, ukuran 12px uppercase, garis pembatas bawah `1px solid #f1f5f9`.
- **Isi (Cells)**: Padding vertikal yang lapang (16px), tanpa pembatas vertikal.
- **Efek Hover**: Baris tabel berubah warna menjadi `#f8fafc` saat disentuh kursor.

### 4. Input Fields
- **Border**: `1px solid #e2e8f0`.
- **Focus**: `1px solid #64748b` (Slate Gray) tanpa pendaran luar (outer glow/shadow).
- **Label**: Ukuran 12px uppercase Slate Gray, diletakkan tepat di atas kolom input.

### 5. Kartu Data (Data Cards)
- Blok ringkasan minimalis untuk metrik bisnis (misalnya pendapatan, pesanan).
- Menggunakan ikon monokromatik bergaris tipis (2px stroke) jika diperlukan.
- Nilai metrik ditampilkan dengan gaya `headline-md` (Deep Charcoal).
