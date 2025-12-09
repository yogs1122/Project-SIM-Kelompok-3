# Forum Penjualan & Lowongan UMKM - Dokumentasi Fitur

## Deskripsi Umum

Forum Penjualan adalah fitur yang memungkinkan pengguna regular (bukan UMKM) untuk membuat, membaca, mengubah, dan menghapus post penjualan, lowongan, atau pengumuman untuk komunitas UMKM lokal dan pengguna lainnya.

## Fitur Utama (User Side - Versi 1)

### 1. **Melihat Daftar Post (Browse)**
- Pengguna dapat melihat semua post yang aktif (status: active)
- Filter berdasarkan kategori (Umum, Produk, Layanan, Lowongan)
- Search dengan kata kunci di judul dan deskripsi
- Pagination: 12 post per halaman
- Tampil counter views untuk setiap post

### 2. **Membuat Post Baru**
- Form dengan field:
  - **Judul** (wajib): Ringkas dan jelas
  - **Kategori** (wajib): Umum, Produk, Layanan, Lowongan Kerja
  - **Deskripsi** (wajib): Detail lengkap max 5000 karakter
  - **Harga** (opsional): Untuk produk/layanan berbayar
  - **Foto** (opsional): Upload gambar produk/layanan (max 2MB)
- Drag-and-drop image upload dengan preview
- Post otomatis bernilai status=active saat dibuat
- File image disimpan di `storage/app/public/sales_forum/`

### 3. **Melihat Detail Post (Show)**
- Menampilkan:
  - Foto produk/layanan
  - Judul, deskripsi lengkap, harga (jika ada)
  - Kategori & status (AKTIF/TERJUAL)
  - Informasi penjual: nama, nomor WhatsApp
  - Tanggal post dan total views
  - Tombol "Chat WhatsApp" ke penjual (jika user penjual punya nomor phone)
- Increment counter views setiap kali post dibuka
- **Untuk pemilik post**: sidebar menampilkan statistik (views, status, tanggal) + tombol Edit/Hapus/Mark Sold
- **Untuk pengguna lain**: sidebar menampilkan card Contact dengan WhatsApp button + tips keamanan

### 4. **Edit Post**
- Hanya pemilik post yang bisa edit
- Bisa mengubah: title, category, description, price, image
- Form mirip create tapi pre-filled dengan data existing
- Tombol "Ganti Foto" dengan preview foto saat ini

### 5. **Hapus Post**
- Hanya pemilik post yang bisa hapus
- Konfirmasi sebelum delete
- Delete image file dari storage otomatis

### 6. **Tandai Terjual**
- Hanya pemilik post yang bisa mark as sold
- Mengubah status dari 'active' → 'sold'
- Post tetap visible tapi dengan badge "TERJUAL" (merah)

## Struktur Database

### Tabel: `sales_forum_posts`

```sql
CREATE TABLE sales_forum_posts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL (FK → users.id),
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    price DECIMAL(12,2) NULLABLE,
    category VARCHAR(50) DEFAULT 'umum',
    image VARCHAR(255) NULLABLE,
    status ENUM('active', 'sold', 'archived') DEFAULT 'active',
    views INT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Kategori yang tersedia:**
- `umum` - Pengumuman umum
- `produk` - Jual beli produk
- `layanan` - Jual beli layanan
- `lowongan` - Lowongan kerja

**Status:**
- `active` - Post aktif dan terlihat
- `sold` - Sudah terjual
- `archived` - Diarsipkan (future use)

## Model & Relationship

### SalesForumPost Model
```php
- belongsTo(User::class) // Pemilik post
- scopeActive() // Query hanya active posts
- scopeByCategory($category) // Query berdasarkan kategori
- isOwnedBy($userId) // Check kepemilikan
```

### User Model (Updated)
```php
- hasMany(SalesForumPost::class) // Post yang dibuat user
```

## Routes (Protected)

```
GET    /sales-forum                    → index (browse all posts)
GET    /sales-forum/create              → create (form baru)
POST   /sales-forum                    → store (simpan baru)
GET    /sales-forum/{id}               → show (detail)
GET    /sales-forum/{id}/edit          → edit (form edit)
PATCH  /sales-forum/{id}               → update (simpan perubahan)
DELETE /sales-forum/{id}               → destroy (hapus)
POST   /sales-forum/{id}/mark-sold     → markAsSold (tandai terjual)
```

**Auth Middleware**: Semua route memerlukan login

## UI/UX Design

### Index (List)
- Hero header dengan "Forum Penjualan & Lowongan UMKM" + tombol "Buat Post Baru"
- Search bar + category filter dropdown
- Grid 3 kolom di desktop (responsive)
- Card design:
  - Image area dengan gradient fallback
  - Title, description preview (truncated)
  - User info (nama penjual)
  - Footer: Harga + Views counter
  - Status badge (AKTIF/TERJUAL) + Category badge
  - Hover effect: scale image, change text color

### Create/Edit Form
- Max width container (max-w-2xl)
- Input fields dengan border-2 focus:ring
- File drag-drop dengan preview
- Tombol Submit + Cancel

### Show (Detail)
- 2 kolom layout:
  - **Left (2/3)**: Large image, title, category badges, price prominent
  - **Right (1/3)**: 
    - Untuk non-owner: Contact card (green gradient) dengan WhatsApp button + tips
    - Untuk owner: Stats card (blue) dengan views, status, created date
- Seller info card (indigo border) dengan nama & phone
- Action buttons di atas atau sidebar

### Sidebar Navigation
- Link "Forum Jual Beli" dengan gradient background (green)
- Icon: 🛍️
- Styling: `from-green-50 to-emerald-50` background, `border-l-4 border-green-500`
- Bold text + green color untuk menonjol

## Validasi & Keamanan

1. **Authorization**: Hanya owner yang bisa edit/delete post mereka
2. **File Upload**: JPG, PNG, GIF only, max 2MB
3. **Form Validation**:
   - Title: required, max 255 char
   - Description: required, max 5000 char
   - Price: nullable, numeric, min 0
   - Category: required, in enum list
   - Image: nullable, image type, max 2MB

## Implementasi Steps (Developer)

1. ✅ Migration `2025_12_08_000000_create_sales_forum_posts_table`
2. ✅ Model `SalesForumPost.php`
3. ✅ Controller `SalesForumController.php`
4. ✅ Routes di `web.php`
5. ✅ Views: `index, create, show, edit`
6. ✅ Add link di sidebar (`layouts/app.blade.php`)
7. ✅ Update User model (relationship)
8. ⏳ Run `php artisan migrate` (manual)
9. ⏳ Test di browser

## Catatan untuk Future Enhancement (UMKM Version)

- Tambah role-based features untuk UMKM seller
- Notification ketika ada pembeli interest
- Rating & review system
- Wishlist untuk pembeli
- Admin dashboard untuk moderate posts
- Payment integration via wallet

---

**Created**: December 8, 2025
**Status**: User-side MVP Complete (ready for testing)
