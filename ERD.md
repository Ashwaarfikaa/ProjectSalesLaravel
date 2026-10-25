# 📐 ERD — SummitGear Outdoor

Entity Relationship Diagram database **`inventory_baru`** (MySQL), sesuai file
`database/migrations/2024_01_01_000000_create_integrated_inventory_tables.php`
dan `2026_10_26_000001_add_performance_indexes.php`.

```mermaid
erDiagram
    USERS ||--o{ PURCHASES : "mencatat pembelian"
    USERS ||--o{ SALES : "mencatat penjualan"
    CATEGORIES ||--o{ PRODUCTS : "mengelompokkan"
    SUPPLIERS ||--o{ PURCHASES : "memasok"
    PURCHASES ||--o{ PURCHASE_ITEMS : "memuat rincian"
    PRODUCTS ||--o{ PURCHASE_ITEMS : "dibeli"
    CUSTOMERS |o--o{ SALES : "checkout (boleh tanpa pelanggan)"
    SALES ||--o{ SALE_ITEMS : "memuat rincian"
    PRODUCTS ||--o{ SALE_ITEMS : "terjual"

    USERS {
        int id PK
        string name
        string email UK
        string password
        string role "admin | staff"
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }
    CATEGORIES {
        int id PK
        string name UK
        timestamp created_at
        timestamp updated_at
    }
    PRODUCTS {
        int id PK
        int category_id FK
        string sku UK
        string name
        decimal purchase_price
        decimal selling_price
        int stock
        int minimum_stock
        timestamp created_at
        timestamp updated_at
    }
    SUPPLIERS {
        int id PK
        string name
        string phone
        string email
        string address
        timestamp created_at
        timestamp updated_at
    }
    PURCHASES {
        int id PK
        int supplier_id FK
        int user_id FK
        string purchase_number UK
        decimal total
        string status
        date purchased_at
        timestamp created_at
        timestamp updated_at
    }
    PURCHASE_ITEMS {
        int id PK
        int purchase_id FK
        int product_id FK
        int quantity
        decimal unit_cost
        decimal subtotal
    }
    CUSTOMERS {
        int id PK
        string name
        string phone
        string email
        string address
        timestamp created_at
        timestamp updated_at
    }
    SALES {
        int id PK
        int user_id FK "kasir"
        int customer_id FK "boleh kosong"
        string sale_number UK
        decimal total
        string status
        date sold_at
        timestamp created_at
        timestamp updated_at
    }
    SALE_ITEMS {
        int id PK
        int sale_id FK
        int product_id FK
        int quantity
        decimal unit_price
        decimal subtotal
    }
```

## Penjelasan Relasi

| Relasi | Kardinalitas | Keterangan |
|---|---|---|
| `categories` → `products` | 1 : N (nullable) | Setiap produk opsional dimiliki satu kategori (`products.category_id`). |
| `users` → `purchases` | 1 : N | Admin/staff yang mencatat pembelian barang (`purchases.user_id`). |
| `suppliers` → `purchases` | 1 : N | Barang dibeli dari supplier tertentu (`purchases.supplier_id`). |
| `purchases` → `purchase_items` | 1 : N | Satu pembelian berisi banyak baris barang (`purchase_items.purchase_id`). |
| `products` → `purchase_items` | 1 : N | Item pembelian merujuk produk (`purchase_items.product_id`, `unit_cost`). |
| `users` → `sales` | 1 : N | Admin/staff bertindak sebagai kasir (`sales.user_id`). |
| `customers` → `sales` | 0..1 : N | Pelanggan opsional; transaksi walk-in dapat tanpa customer (`sales.customer_id` nullable, ON DELETE SET NULL). |
| `sales` → `sale_items` | 1 : N | Satu penjualan berisi banyak baris barang (`sale_items.sale_id`). |
| `products` → `sale_items` | 1 : N | Item penjualan mengunci harga saat transaksi (`unit_price` disalin dari `selling_price`). |

## Catatan Skema

- **Role pengguna hanya `admin` dan `staff`** — tidak ada role pelanggan; data pembeli disimpan di tabel `customers`.
- Kolom `status` pada `purchases` mengikuti alur pembelian (`pending` → `completed`), sedangkan `status` pada `sales` bernilai `completed`/`cancelled`.
- Tabel `sales` pada SummitGear **tidak memiliki kolom `payment_method`** (pencatatan kasir tunai).
- **Alur stok**: stok produk bertambah ketika pembelian item selesai (diproses `PurchaseService`), berkurang ketika penjualan selesai (diproses `SaleService`, dengan validasi anti-oversell). Jika penjualan dibatalkan, stok dikembalikan.
- Nomor dokumen unik: `sales.sale_number` (`SG-YYYYMMDD-XXXXX`) dan `purchases.purchase_number` (`PO-YYYYMMDD-XXXXX`), dibuat otomatis lewat service.
- Indeks performa tambahan (migration `2026_10_26_000001`) pada kolom `products.sku`, `sales.sold_at`, `sale_items.sale_id`, `purchase_items.purchase_id`, dsb. agar filter/laporan lebih cepat.
- Harga (`purchase_price`, `selling_price`, `unit_cost`, `unit_price`, `total`, `subtotal`) memakai tipe `decimal(12,2)` dalam Rupiah.
