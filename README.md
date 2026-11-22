# FlipPlay API

REST API FlipPlay untuk Demo Modul 4, melanjutkan simulasi OOP FlipPlay dari Modul 3 ke Laravel 12 dengan database, migration, Eloquent, Form Request, API Resource, dan custom action relasi many-to-many.

## Fitur

- Dua entitas Eloquent: `Media` dan `Pengguna`.
- Relasi many-to-many melalui tabel pivot `koleksi_media`.
- CRUD Media lengkap: `index`, `store`, `show`, `update`, `destroy`.
- Form Request Validation untuk input Media dan Pengguna.
- API Resource dengan `whenLoaded()` dan `whenCounted()` agar format JSON konsisten.
- Pencarian/filter media berdasarkan judul, jenis, dan genre.
- Custom action menambah/menghapus media dari koleksi pengguna.
- Seeder data demo dan feature test untuk happy path serta failure path.
- Postman collection siap pakai.

## Kebutuhan

- PHP 8.2+ (diuji dengan PHP 8.4.28)
- Composer
- Laravel 12
- SQLite (default) atau MySQL

## Setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Base URL API: `http://127.0.0.1:8000/api`

Jika `php artisan serve` gagal listen di Windows, gunakan:

```bash
php artisan serve --no-reload
```

### Opsi MySQL

Ubah `.env`, lalu jalankan ulang migration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flipplay_api
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan migrate:fresh --seed
```

## Endpoint

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| GET | `/api/media` | Daftar media, mendukung filter dan pagination |
| POST | `/api/media` | Tambah media baru |
| GET | `/api/media/{media}` | Detail media beserta pengguna yang mengoleksinya |
| PATCH/PUT | `/api/media/{media}` | Perbarui media |
| DELETE | `/api/media/{media}` | Hapus media dan relasi pivot-nya |
| GET | `/api/pengguna` | Daftar pengguna beserta jumlah koleksi |
| POST | `/api/pengguna` | Tambah pengguna baru |
| GET | `/api/pengguna/{pengguna}` | Detail pengguna beserta koleksi media |
| GET | `/api/pengguna/{pengguna}/koleksi` | Koleksi media milik pengguna |
| POST | `/api/pengguna/{pengguna}/koleksi/{media}` | Custom action: tambah media ke koleksi |
| DELETE | `/api/pengguna/{pengguna}/koleksi/{media}` | Custom action: hapus media dari koleksi |

### Query filter `GET /api/media`

| Parameter | Nilai | Keterangan |
| --- | --- | --- |
| `search` | teks | Filter judul (LIKE) |
| `jenis` | `film`, `musik`, `serial`, `buku` | Filter jenis |
| `genre` | teks | Filter genre (LIKE) |
| `sort` | `judul`, `tahun_rilis`, `rating`, `created_at` | Kolom sorting |
| `order` | `asc`, `desc` | Arah sorting |
| `per_page` | 1-100 | Jumlah data per halaman |

Contoh: `GET /api/media?search=incep&jenis=film&sort=tahun_rilis&order=desc&per_page=5`

## Contoh Request

### Tambah media

```json
POST /api/media
{
  "judul": "Dune: Part Two",
  "jenis": "film",
  "genre": "Sci-Fi",
  "tahun_rilis": 2024,
  "deskripsi": "Paul Atreides bersatu dengan Fremen untuk membalas dendam.",
  "rating": 8.6
}
```

Respons `201 Created`:

```json
{
  "data": {
    "id": 9,
    "judul": "Dune: Part Two",
    "jenis": "film",
    "genre": "Sci-Fi",
    "tahun_rilis": 2024,
    "rating": 8.6,
    "jumlah_di_koleksi": 0
  },
  "message": "Media berhasil ditambahkan."
}
```

### Validasi gagal

```json
POST /api/media
{
  "judul": "",
  "jenis": "film",
  "genre": "Action",
  "tahun_rilis": 2024
}
```

Respons `422 Unprocessable Entity`:

```json
{
  "message": "Judul media wajib diisi.",
  "errors": {
    "judul": ["Judul media wajib diisi."]
  }
}
```

### Tambah media ke koleksi

```text
POST /api/pengguna/1/koleksi/9
```

Respons `201 Created` berisi data pengguna dengan `jumlah_koleksi` dan daftar `koleksi` terbaru.

Jika media yang sama ditambahkan dua kali:

```json
HTTP 409 Conflict
{
  "message": "Media sudah ada di koleksi pengguna.",
  "errors": {
    "media": ["Media yang sama tidak boleh ditambahkan dua kali ke koleksi pengguna yang sama."]
  }
}
```

## Struktur Database

- `media`: `id`, `judul` (unik), `jenis`, `genre`, `tahun_rilis`, `deskripsi`, `rating`, timestamps.
- `pengguna`: `id`, `nama`, `email` (unik), timestamps.
- `koleksi_media`: `id`, `pengguna_id` (FK cascade), `media_id` (FK cascade), timestamps, unique (`pengguna_id`, `media_id`).

## Data Seeder

- Andi Pratama — `andi@flipplay.test` — 3 media.
- Siti Rahma — `siti@flipplay.test` — 2 media.
- Budi Santoso — `budi@flipplay.test` — 3 media.

## Pengujian

```bash
php artisan test
```

Hasil: 20 test lulus (95 assertion), mencakup CRUD, validasi judul kosong/duplikat, filter pencarian, detail relasi, tambah/hapus koleksi, duplikat koleksi (409), dan 404.

Verifikasi route:

```bash
php artisan route:list --path=api
```

## Postman

1. Jalankan `php artisan migrate:fresh --seed`.
2. Import `postman/FlipPlay-API.postman_collection.json` ke Postman.
3. Sesuaikan collection variable `base_url` bila port berbeda.
4. Jalankan folder secara berurutan, atau gunakan Collection Runner.

Folder di dalam collection: `Media (CRUD)`, `Pengguna`, `Koleksi (Custom Action)`, `Skenario 404`, dan `Cleanup`.

## Struktur Proyek

```text
app/Http/Controllers/Api/   MediaController, PenggunaController, KoleksiController
app/Http/Requests/          StoreMediaRequest, UpdateMediaRequest, StorePenggunaRequest
app/Http/Resources/         MediaResource, PenggunaResource
app/Models/                 Media, Pengguna
database/migrations/        media, pengguna, koleksi_media
database/seeders/           MediaSeeder, PenggunaSeeder
routes/api.php              Seluruh endpoint API
tests/Feature/              MediaApiTest, PenggunaApiTest, KoleksiApiTest
postman/                    Koleksi Postman untuk pengujian manual
```
