# Bengkel Management System - Technical Specification

## 1. Overview

### Application Name
**Bengkel Management System** (Workshop Management System)

### Purpose
A comprehensive web-based management system designed to facilitate the operations of an automotive workshop (bengkel) including inventory management, sales transactions, purchase orders, employee management, and financial reporting.

### Target Users
- **Admin**: Full system access, manages administrators, items, employees, suppliers, and generates reports
- **Karyawan Admin** (Employee Admin): Limited admin privileges with time-restricted access
- **Karyawan** (Employees/Mechanics): Access to sales transactions with time-based working hour restrictions

### Problem It Solves
- **Inventory Management**: Track spare parts stock levels, manage product catalogs with pricing
- **Sales Operations**: Record vehicle service sales with parts used and employee labor costs
- **Purchase Management**: Handle supplier purchases and track procurement
- **Employee Management**: Manage employee profiles, working hours, and skill tracking
- **Financial Reporting**: Generate sales, purchase, and profit reports with date-based filtering
- **Access Control**: Time-based login restrictions to enforce working hour compliance

### High-Level Architecture Summary
CodeIgniter 3 MVC framework-based monolithic application with:
- Server-side session management for authentication
- MySQL database with relational structure
- Role-based access control (RBAC) at controller level
- Form validation and pagination for data listing
- Dynamic code generation for transaction IDs
- Session-based shopping cart pattern for transactions

---

## 2. Tech Stack

### Backend Framework
- **Framework**: CodeIgniter 3.x
- **Language**: PHP 5.2.4+ (Recommended 5.4+)
- **Architecture**: MVC (Model-View-Controller)

### Frontend
- **Template Engine**: PHP-based views
- **JavaScript**: jQuery 2.0.0 (minimal frontend logic)
- **Styling**: Not clearly defined in codebase
- **Rendering**: Server-side HTML generation

### Database
- **DBMS**: MySQL 5.1.9+/MariaDB 10.1.9+
- **Charset**: UTF8MB4
- **Engine**: MyISAM/InnoDB
- **Timezone**: Asia/Jakarta (GMT+7)

### Authentication System
- **Method**: PHP Session-based (`session_start()`)
- **Storage**: Server-side session variables (`$_SESSION`)
- **Credentials**: Username + Password (plaintext in database)
- **Roles**: admin, karyawan, karyawan_admin

### Infrastructure
- **Server**: PHP Development Server (php -S localhost:8000)
- **CI/CD**: Not configured
- **Docker**: Not implemented
- **Web Server**: Apache (via .htaccess)

### Third-Party Integrations
- **None explicitly configured** (vfsStream available in dev dependencies)

---

## 3. System Architecture

### Architecture Pattern
**Monolithic MVC application** with:
- Centralized business logic in models
- Controller-based routing and request handling
- Template-based view rendering
- Session-based state management

### Folder Structure

```
application/
├── controllers/          # 17 Request handlers
│   ├── Login.php        # Authentication entry point
│   ├── Admin.php        # Admin management CRUD
│   ├── Karyawan.php     # Employee management with skill tracking
│   ├── Barang.php       # Inventory/products CRUD
│   ├── Merk.php         # Brand management
│   ├── Suplier.php      # Supplier management
│   ├── Pembelian.php    # Purchase orders with session cart
│   ├── Penjualan.php    # Sales transactions with filters
│   ├── Pembelian_detail.php  # Purchase line items
│   ├── Penjualan_detail.php  # Sales line items
│   ├── Home.php         # Dashboard & main transaction form
│   ├── Laporan.php      # Financial reports (6 report types)
│   ├── Promo.php        # Promotional pricing management
│   ├── Ramal.php        # Forecast/estimation module
│   ├── Ramal_harga.php  # Price forecasting
│   ├── Sync.php         # Data synchronization
│   └── Logout.php       # Session termination
├── models/              # 12 Data access objects
│   ├── *_model.php      # CRUD operations + custom queries
│   ├── CodeGenerator.php # Transaction ID generation
│   └── Percobaan_karyawan_model.php  # Employee skill attempts/trials
├── views/               # Template files
│   ├── nav.php          # Navigation header
│   ├── foot.php         # Footer
│   ├── home.php         # Dashboard view
│   ├── login.php        # Login form
│   ├── admin/           # Admin views (CRUD)
│   ├── barang/          # Inventory views
│   ├── karyawan/        # Employee views
│   ├── pembelian/       # Purchase views
│   ├── penjualan/       # Sales views
│   ├── laporan/         # Report views
│   └── errors/          # Error pages
├── config/
│   ├── routes.php       # URI routing
│   ├── database.php     # Database connection
│   ├── config.php       # App configuration
│   └── constants.php    # Application constants
├── libraries/           # Custom libraries (if any)
└── helpers/             # Helper functions
```

### Key Modules and Responsibilities

| Module | Responsibility |
|--------|-----------------|
| **Login/Auth** | Session creation, credential validation, role assignment, working hour enforcement |
| **Admin Management** | CRUD for administrator accounts with username/password |
| **Employee Management** | CRUD for employees, working hours, permissions (can_see_stock, can_see_sales), skill tracking |
| **Inventory** | CRUD for spare parts with purchase/sale pricing, stock levels, brand associations |
| **Brand Management** | CRUD for product brands/manufacturers |
| **Supplier Management** | CRUD for supplier contact information |
| **Sales Transaction** | Multi-step sales creation with line items, customer info, employee labor, payment tracking, session-based cart |
| **Purchase Transaction** | Multi-step purchase order creation with line items, supplier selection, session-based cart |
| **Reporting** | 6 report types: purchase by date, sales by date, profit analysis, employee performance, stock history, forecasting |
| **Skill Tracking** | Employee attempt/trial tracking (percobaan) per product with 2-trial blocking mechanism |
| **Forecasting** | Price and demand estimation modules |

### Data Flow Overview

1. **User Authentication**: Login → Credential Check → Session Creation → Access Control → Dashboard
2. **Sales Flow**: Dashboard → Add Items to Cart (Session) → Set Customer/Employee Info → Confirm → Insert to DB → Update Stock
3. **Purchase Flow**: Pembelian Form → Add Items to Cart (Session) → Select Supplier → Confirm → Insert to DB → Update Stock
4. **Report Generation**: Select Filters (Date, Employee, Product) → Query DB → Aggregate Data → Display/Print
5. **Employee Access**: Session-based permissions (can_see_stock, can_see_sales) + time-based hour validation

---

## 4. Features

### Feature 1: User Authentication & Session Management

**Description**: Multi-role authentication system with session-based login and time-based access control for employees.

**User Roles Involved**: 
- Admin (full access)
- Karyawan Admin (limited admin access with time restrictions)
- Karyawan (sales access only)

**API Endpoints**:
- `POST /login` - Submit credentials
- `GET /login/logout` - Clear session
- `GET /login/session` - View session data (debug)

**Request/Response Structure**:
```
POST /login
{
  "username": "admin",
  "psswd": "123",
  "login": "true"
}

Response on success:
Redirect to /home with $_SESSION containing:
{
  "username": "admin",
  "kode": "ADM00001",
  "level": "admin|karyawan|karyawan_admin",
  "can_see_stock": true/false,
  "can_see_sales": true/false
}

Response on time restriction:
JavaScript alert: "Karyawan ini hanya dapat login antara jam HH:MM - HH:MM"
```

**Validation Rules**:
- Username: Required, must exist in admin or karyawan table
- Password: Required, plaintext comparison
- Working Hours: For karyawan, current time must be within start_working_hour and end_working_hour
- Level Check: Admin redirects to home, karyawan redirects appropriately

**Business Logic Summary**:
- Attempts admin table first, then karyawan table
- Enforces working hour restrictions for employees
- Sets session permissions based on karyawan.can_see_stock and can_see_sales flags
- Timezone hardcoded to Asia/Jakarta (GMT+7)

---

### Feature 2: Admin Management (CRUD)

**Description**: Create, read, update, delete administrator user accounts.

**User Roles Involved**: Admin, Karyawan Admin only

**API Endpoints**:
- `GET /admin` - List admins with pagination & search
- `GET /admin/view/{kode_admin}` - View single admin details
- `GET /admin/datainsert` - Show insert form
- `POST /admin/insert` - Save new admin
- `GET /admin/dataupdate/{kode_admin}` - Show edit form
- `POST /admin/update` - Save admin changes
- `GET /admin/delete/{kode_admin}` - Delete admin

**Request/Response Structure**:
```
POST /admin/insert
{
  "kode_admin": "ADM00002",
  "nama_admin": "John Doe",
  "username": "johndoe",
  "psswd": "password123"
}

Response:
Redirect to /admin (after auto-generation of kode_admin)

GET /admin?start=0&cari=john
Response:
View with pagination showing filtered results:
{
  "admin_data": [...],
  "pagination": "...",
  "cari": "john",
  "total_rows": 1
}
```

**Validation Rules**:
- kode_admin: Required, auto-generated (format: ADM#####)
- nama_admin: Required, trimmed
- username: Required, trimmed
- psswd: Required, trimmed, stored plaintext

**Business Logic Summary**:
- Code generation via CodeGenerator model with prefix "ADM"
- Pagination with 10 items per page
- Search across all fields (like operator)
- No duplicate check on username

---

### Feature 3: Employee Management (CRUD)

**Description**: Manage employee profiles including working hours, permissions, and skill attempt tracking.

**User Roles Involved**: Admin, Karyawan Admin only

**API Endpoints**:
- `GET /karyawan` - List employees with pagination
- `GET /karyawan/view/{kode_karyawan}` - View employee + skill attempts
- `GET /karyawan/datainsert` - Show insert form
- `POST /karyawan/insert` - Save new employee
- `GET /karyawan/dataupdate/{kode_karyawan}` - Show edit form
- `POST /karyawan/update` - Save employee changes
- `GET /karyawan/delete/{kode_karyawan}` - Delete employee
- `GET /karyawan/unlock/{kode_karyawan}/{id_barang}` - Remove skill block

**Request/Response Structure**:
```
POST /karyawan/insert
{
  "kode_karyawan": "KRY0000001",
  "nama_karyawan": "Paimen",
  "alamat_karyawan": "Jl. Raya",
  "telp_karyawan": "089765421",
  "username": "paimen",
  "password": "pass123",
  "level": "0",
  "can_see_stock": "1",
  "can_see_sales": "1",
  "start_working_hour": "09:00:00",
  "end_working_hour": "17:00:00"
}

GET /karyawan/view/KRY0000001
Response shows:
- Employee details
- List of attempted items (percobaan_karyawan) with attempt counts
- Blocked items (2+ attempts on same item)
```

**Validation Rules**:
- kode_karyawan: Required, auto-generated (format: KRY#####)
- nama_karyawan: Required
- alamat_karyawan: Required
- telp_karyawan: Required
- username: Required, must be unique
- password: Required
- start_working_hour: Required (HH:MM:SS format)
- end_working_hour: Required (HH:MM:SS format)
- level: 0=karyawan, 1=karyawan_admin
- can_see_stock: Boolean (0/1)
- can_see_sales: Boolean (0/1)

**Business Logic Summary**:
- Working hours support next-day ranges (e.g., 22:00 to 02:00)
- Skill attempt tracking: blocks employee after 2 failed attempts on same item
- List view includes "isblocked" flag for products with 2+ attempts
- Pagination with 10 items per page
- Search across kode, nama, alamat, telp

---

### Feature 4: Inventory/Product Management (CRUD)

**Description**: Manage spare part catalog with purchase/sale pricing, stock levels, and brand associations.

**User Roles Involved**: Admin, Karyawan Admin only

**API Endpoints**:
- `GET /barang` - List products with pagination & search
- `GET /barang/view/{kode_barang}` - View single product
- `GET /barang/datainsert` - Show insert form (includes brand list)
- `POST /barang/insert` - Save new product
- `GET /barang/dataupdate/{kode_barang}` - Show edit form
- `POST /barang/update` - Save product changes
- `GET /barang/delete/{kode_barang}` - Delete product

**Request/Response Structure**:
```
POST /barang/insert
{
  "kode_barang": "BRG0000001",
  "nama_barang": "shock",
  "kode_merk": "MRK0000001",
  "harga_beli": 1000,
  "harga_jual": 1200,
  "stok": 24,
  "keterangan": "masih bagus"
}

Response:
Redirect to /barang after successful insert

GET /barang?cari=shock&start=0
Response includes:
{
  "barang_data": [
    {
      "kode_barang": "BRG0000001",
      "nama_barang": "shock",
      "kode_merk": "MRK0000001",
      "merk": "Honda",
      "harga_beli": 1000,
      "harga_jual": 1200,
      "stok": 24,
      "keterangan": "masih bagus"
    }
  ],
  "pagination": "..."
}
```

**Validation Rules**:
- kode_barang: Required, auto-generated (format: BRG#####)
- nama_barang: Required
- kode_merk: Required, must exist in merk table
- harga_beli: Required, integer
- harga_jual: Required, integer
- stok: Required, integer
- keterangan: Required

**Business Logic Summary**:
- Code generation via CodeGenerator with prefix "BRG"
- Automatic join with merk table for brand name display
- Pagination with 10 items per page
- Search across 8 fields (kode, nama, merk, prices, stok, keterangan)
- Stock validation on sales (prevents overselling)

---

### Feature 5: Brand Management (CRUD)

**Description**: Manage product brand/manufacturer master data.

**User Roles Involved**: Admin, Karyawan Admin only

**API Endpoints**:
- `GET /merk` - List brands
- `GET /merk/view/{kode_merk}` - View brand
- `GET /merk/datainsert` - Show insert form
- `POST /merk/insert` - Save new brand
- `GET /merk/dataupdate/{kode_merk}` - Show edit form
- `POST /merk/update` - Save brand changes
- `GET /merk/delete/{kode_merk}` - Delete brand

**Request/Response Structure**:
```
POST /merk/insert
{
  "kode_merk": "MRK0000001",
  "merk": "Honda",
  "keterangan": "Spare part Honda"
}
```

**Validation Rules**:
- kode_merk: Required, auto-generated (format: MRK#####)
- merk: Required
- keterangan: Required

---

### Feature 6: Supplier Management (CRUD)

**Description**: Maintain supplier contact database for purchase orders.

**User Roles Involved**: Admin, Karyawan Admin only

**API Endpoints**:
- `GET /suplier` - List suppliers
- `GET /suplier/view/{kode_suplier}` - View supplier
- `GET /suplier/datainsert` - Show insert form
- `POST /suplier/insert` - Save new supplier
- `GET /suplier/dataupdate/{kode_suplier}` - Show edit form
- `POST /suplier/update` - Save supplier changes
- `GET /suplier/delete/{kode_suplier}` - Delete supplier

**Request/Response Structure**:
```
POST /suplier/insert
{
  "kode_suplier": "SUP0000001",
  "nama_suplier": "PT. Bahagia",
  "alamat_suplier": "Jl. Merdeka No. 1",
  "no_telp": "021-12345678",
  "keterangan": "Supplier resmi"
}
```

**Validation Rules**:
- kode_suplier: Required, auto-generated (format: SUP#####)
- nama_suplier: Required
- alamat_suplier: Required
- no_telp: Required
- keterangan: Required

---

### Feature 7: Sales Transaction Management

**Description**: Record vehicle service sales with parts used, employee labor costs, and payment tracking. Supports multi-item transactions with session-based shopping cart.

**User Roles Involved**: All authenticated users (with can_see_sales permission)

**API Endpoints**:
- `GET /home` - Sales dashboard with cart display
- `POST /home/insert` - Add item to cart or finalize transaction
- `GET /penjualan` - List all sales with advanced filtering
- `GET /penjualan/view/{kode_jual}` - View sales details + line items
- `POST /penjualan/insert` - Add item to transaction
- `GET /penjualan/delete_detail/{kode_jual}/{kode_barang}` - Remove item from cart
- `POST /home/transaksi` - Quick service entry (service-only, no parts)
- `GET /home/get_percobaan_stok` - Employee skill restrictions (AJAX)

**Request/Response Structure**:
```
POST /home/insert (add item to cart)
{
  "submitlist": "true",
  "kode_jual": "TRJ0000001",
  "kode_barang": "BRG0000001",
  "jumlah": 2,
  "harga_custom": "1500"  // optional: override standard price
}

Response:
Redirect to /penjualan/insert with cart updated

POST /penjualan (finalize transaction)
{
  "kode_jual": "TRJ0000001",
  "tanggal_jual": "30-07-2016",
  "kode_admin": "ADM00001",
  "kode_karyawan": "KRY0000001",
  "nomor_polisi": "B1234CD",
  "km_kendaraan": "5000",
  "keterangan": "Servis rutin",
  "ongkos_karyawan": 50000,
  "total": 1250000,
  "bayar": 1250000,
  "pelanggan": "Budi Santoso"
}

Response:
Redirect to /penjualan (list view)
```

**Session Cart Structure**:
```
$_SESSION["{kode}_detailbarang"] = [
  {
    "kode_barang": "BRG0000001",
    "nama_barang": "shock",
    "harga_jual": 1200,
    "harga_beli": 1000,
    "jumlah": 2,
    "subtotal": 2400,
    "harga_custom": null,
    "is_using_rupiah": false
  },
  ...
]
```

**Validation Rules**:
- kode_jual: Required
- tanggal_jual: Required (DD-MM-YYYY format)
- kode_admin: Required
- ongkos_karyawan: Required, integer
- total: Required, integer
- bayar: Required, integer
- Stock validation: Quantity <= available stock
- At least 1 item must be in cart

**Business Logic Summary**:
- Code generation for new sales (format: TRJ#####)
- Session-based cart allows multi-item transactions before finalization
- Stock deduction on transaction finalization
- Supports custom pricing per item (rupiah mode)
- Line item includes both purchase price and sale price (for profit calculation)
- Employee labor cost added to total
- Payment tracking (bayar field for cash on delivery scenarios)
- Customer name optional (optional pelanggan field)
- Vehicle info tracked (nomor_polisi, km_kendaraan)
- Employee trial/attempt tracking (percobaan) recorded per item

**Advanced Filters** (on list view):
- Search: kode_jual, tanggal_jual, kode_admin, kete, nomor_polisi, ongkos, total, bayar, pelanggan
- Filter by employee: kode_karyawan
- Filter by date range: tgl_awal to tgl_akhir
- Filter by product: kode_barang

---

### Feature 8: Purchase Transaction Management

**Description**: Record purchase orders from suppliers with line items. Session-based cart workflow.

**User Roles Involved**: Admin, Karyawan Admin only

**API Endpoints**:
- `GET /pembelian` - List all purchases with filtering
- `GET /pembelian/view/{kode_beli}` - View purchase details + line items
- `GET /pembelian/datainsert` - Show purchase form (with session cart)
- `POST /pembelian/insert` - Add item to cart or finalize purchase
- `GET /pembelian/delete_detail/{kode_beli}/{kode_barang}` - Remove item from cart
- `GET /pembelian/tempdelete/{kode_beli}/{kode_barang}` - Delete from session cart

**Request/Response Structure**:
```
GET /pembelian/datainsert
Response shows form with:
- Auto-generated kode_beli
- Current date (DD-MM-YYYY)
- Supplier dropdown
- Admin code (auto-filled from session)
- Item list (empty initially)

POST /pembelian/insert (add item)
{
  "submitlist": "true",
  "kode_beli": "TRB0000001",
  "kode_barang": "BRG0000001",
  "harga_beli": 1000,
  "jumlah": 10
}

POST /pembelian/insert (finalize)
{
  "kode_beli": "TRB0000001",
  "tanggal_beli": "30-07-2016",
  "waktu_beli": "14:30:00",
  "no_faktur": "INV-001",
  "kode_suplier": "SUP0000001",
  "kode_admin": "ADM00001",
  "total": 10190
}

Response:
Redirect to /pembelian (list view)
```

**Session Cart Structure**:
```
$_SESSION["{kode}_detailbarang_pembelian"] = [
  {
    "kode_barang": "BRG0000001",
    "nama_barang": "shock",
    "harga_beli": 1000,
    "jumlah": 10,
    "subtotal": 10000
  },
  ...
]
```

**Validation Rules**:
- kode_beli: Required (auto-generated or manual)
- tanggal_beli: Required (DD-MM-YYYY)
- kode_admin: Required
- kode_suplier: Required (must exist in suplier table)
- total: Required, integer
- Stock update: Increases stok field in barang table

**Business Logic Summary**:
- Manual kode_beli entry (not auto-generated like sales)
- Session-based cart before finalization
- Stock increment on purchase completion
- Supports multiple suppliers per session
- Filters by date range and product

---

### Feature 9: Financial Reporting

**Description**: Generate financial reports for sales, purchases, profit, employee performance, and stock history.

**User Roles Involved**: Admin, Karyawan Admin only

**API Endpoints**:
- `GET /laporan/beli` - Purchase report form
- `GET /laporan/pbeli/{mulai}/{rad}` - Purchase report by month/year
- `GET /laporan/pbeli_2/{tgl_awal}/{tgl_akhir}/{kode_barang}` - Purchase by date range & product
- `GET /laporan/pbeliprint/{mulai}/{rad}` - Printable purchase report
- `GET /laporan/pjual/{mulai}/{rad}` - Sales report by month/year
- `GET /laporan/pjualprint/{mulai}/{rad}` - Printable sales report
- `GET /laporan/history_stok` - Stock history with updates
- `GET /laporan/history_stok/{kode_barang}/update` - Update stock history entry

**Request/Response Structure**:
```
GET /laporan/beli (form selection)
Response shows:
- Option 1: Select month/year for monthly report
- Option 2: Select date range + product for detailed report

GET /laporan/pbeli/01-07-2016/Y (yearly)
GET /laporan/pbeli/01-07-2016/M (monthly)

Response includes:
{
  "listbeli": [
    {
      "kode_beli": "TRB0000001",
      "tanggal_beli": "30-07-2016",
      "kode_admin": "ADM00001",
      "total": 10190
    }
  ],
  "listdetail": [
    {
      "kode_beli": "TRB0000001",
      "kode_barang": "BRG0000001",
      "harga_beli": 1000,
      "jumlah": 10,
      "subtotal": 10000
    }
  ],
  "hbeli": {
    "total_purchase": 10190
  }
}

GET /laporan/pjual/01-07-2016/M

Response includes:
{
  "listjual": [...],
  "listdetail": [...],
  "byTgl": { "daily_sales": [...] },
  "hbeli": { "total_cost": ... },
  "hjual": { "total_sales": ... },
  "htotal": { "gross_profit": ... },
  "hgaji": { "employee_wages": ... }
}
```

**Report Types**:
1. **Purchase Report** - By month/year or date range with product filter
2. **Sales Report** - By month/year with daily breakdown, totals, costs, profits, wages
3. **Stock History** - Track stock changes with timestamps
4. **Employee Performance** - Sales by employee for date range
5. **Profit Analysis** - Revenue vs. Cost vs. Profit by date
6. **Forecasting** - Price/demand estimation (ramal modules)

**Aggregation Functions**:
- Sum by date, employee, product
- Group by transaction type (pembelian/penjualan)
- Calculate profit (harga_jual - harga_beli) × quantity
- Employee wages (ongkos_karyawan per transaction)

---

### Feature 10: Promo/Pricing Management

**Description**: Manage promotional pricing for products.

**User Roles Involved**: Admin, Karyawan Admin only

**API Endpoints**:
- `GET /promo` - List promotions
- `GET /promo/view/{id}` - View promo details
- `GET /promo/datainsert` - Show insert form
- `POST /promo/insert` - Create promotion
- `GET /promo/dataupdate/{id}` - Show edit form
- `POST /promo/update` - Update promotion
- `GET /promo/delete/{id}` - Delete promotion
- `POST /promo/toggle/{id}` - Enable/disable promotion

**Business Logic Summary**:
- Allows temporary pricing overrides
- Toggle status (active/inactive)
- Applied at transaction time (optional)

---

### Feature 11: Forecasting Modules

**Description**: Price estimation and demand forecasting tools.

**User Roles Involved**: Admin, Karyawan Admin only

**Modules**:
- **Ramal.php** - Main forecasting controller
- **Ramal_harga.php** - Price trend analysis
- **Forecasting logic**: Trend-based estimation from historical data

**Business Logic Summary**:
- Analyze historical pricing and sales trends
- Generate price recommendations
- Predict demand based on past transactions

---

### Feature 12: Data Synchronization

**Description**: Sync operations between local and external systems (if configured).

**User Roles Involved**: Admin, Karyawan Admin only

**API Endpoints**:
- `GET /sync` - Trigger synchronization
- `POST /sync` - Process sync requests

**Business Logic Summary**:
- Not clearly defined in codebase - appears to be placeholder module
- Could be used for multi-location sync or cloud backup

---

### Feature 13: Dashboard

**Description**: Home page showing summary statistics and sales form.

**User Roles Involved**: All authenticated users

**API Endpoints**:
- `GET /home` - Main dashboard

**Response Data**:
```
{
  "listbarang": [...],          // All products
  "listkaryawan": [...],        // All employees
  "admin": {...},               // Current admin (if level=admin)
  "karyawan": {...},            // Current employee (if level=karyawan)
  "listgaji": {...},            // Daily wages
  "semua": {...},               // Total sales today
  "byTgl": {...},               // Daily summary
  "byTgl2": {...},              // Omset by date
  "totaltr": {...},             // Transaction count
  "totalkr": {...},             // Total employees
  "kasir": {                    // Per-employee sales
    "KRY0000001": {...}
  },
  "listdetail": [...],          // Current session cart items
  "pesan": ""                   // Error/success messages
}
```

**Dashboard Features**:
- Quick sales entry form
- Session cart display (items added but not finalized)
- Employee list for sale assignment
- Today's sales summary
- Daily employee performance
- Employee wage tracking

---

## 5. Data Model

### Core Entities

#### **admin**
Primary key management for administrators.

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| kode_admin | varchar(10) | NO | - | PRIMARY | Format: ADM##### |
| nama_admin | varchar(50) | NO | - | - | Full name |
| username | varchar(100) | NO | - | - | Unique (not enforced in DB) |
| psswd | varchar(100) | NO | - | - | Plaintext password |

---

#### **karyawan**
Employee records with working hour and permission tracking.

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| kode_karyawan | varchar(10) | NO | - | PRIMARY | Format: KRY##### |
| nama_karyawan | varchar(100) | NO | - | - | Full name |
| alamat_karyawan | text | NO | - | - | Address |
| telp_karyawan | varchar(15) | NO | - | - | Phone number |
| username | varchar(100) | NO | - | - | Login username |
| password | varchar(100) | NO | - | - | Plaintext password |
| level | int(1) | NO | 0 | - | 0=karyawan, 1=karyawan_admin |
| can_see_stock | tinyint(1) | NO | 0 | - | Stock visibility permission |
| can_see_sales | tinyint(1) | NO | 0 | - | Sales visibility permission |
| start_working_hour | time | NO | - | - | HH:MM:SS format |
| end_working_hour | time | NO | - | - | HH:MM:SS format (can be next day) |
| percobaan_stok | int(11) | NO | 0 | - | Skill attempt counter |

---

#### **barang**
Product/spare part inventory.

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| kode_barang | varchar(10) | NO | - | PRIMARY | Format: BRG##### |
| nama_barang | varchar(100) | NO | - | - | Product name |
| kode_merk | varchar(10) | NO | - | FOREIGN | References merk.kode_merk |
| harga_beli | int(11) | NO | - | - | Purchase price |
| harga_jual | int(11) | NO | - | - | Sale price |
| stok | int(11) | NO | 0 | - | Current stock level |
| keterangan | text | NO | - | - | Product description |

**Indexes**: PRIMARY KEY (kode_barang), FOREIGN KEY (kode_merk)

---

#### **merk**
Product brands/manufacturers.

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| kode_merk | varchar(10) | NO | - | PRIMARY | Format: MRK##### |
| merk | varchar(100) | NO | - | - | Brand name |
| keterangan | text | NO | - | - | Brand description |

---

#### **suplier**
Supplier contact information.

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| kode_suplier | varchar(10) | NO | - | PRIMARY | Format: SUP##### |
| nama_suplier | varchar(100) | NO | - | - | Supplier name |
| alamat_suplier | text | NO | - | - | Address |
| no_telp | varchar(15) | NO | - | - | Phone number |
| keterangan | text | NO | - | - | Notes/description |

---

#### **penjualan**
Sales transactions (invoices).

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| kode_jual | varchar(10) | NO | - | PRIMARY | Format: TRJ##### |
| tanggal_jual | varchar(20) | NO | - | - | DD-MM-YYYY format |
| kode_admin | varchar(10) | NO | - | FOREIGN | Admin who recorded sale |
| kode_karyawan | varchar(10) | YES | - | FOREIGN | Employee who performed service |
| nomor_polisi | varchar(20) | YES | - | - | Vehicle license plate |
| km_kendaraan | int(11) | YES | - | - | Vehicle odometer reading |
| keterangan | text | NO | - | - | Service description |
| pelanggan | varchar(100) | YES | - | - | Customer name |
| ongkos_karyawan | int(11) | NO | 0 | - | Employee labor cost |
| total | int(11) | NO | 0 | - | Grand total (parts + labor) |
| bayar | int(11) | NO | 0 | - | Amount paid |

**Relationships**: 
- Many-to-many with barang via penjualan_detail
- References admin.kode_admin
- References karyawan.kode_karyawan (optional)

---

#### **penjualan_detail**
Line items in sales transactions.

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| kode_jual | varchar(10) | NO | - | PRIMARY | References penjualan.kode_jual |
| kode_barang | varchar(10) | NO | - | PRIMARY | References barang.kode_barang |
| harga_jual | int(11) | NO | - | - | Sale price at time of transaction |
| harga_beli | int(11) | NO | - | - | Purchase price (for profit calc) |
| jumlah | int(11) | NO | 0 | - | Quantity sold |
| subtotal | double | NO | 0 | - | harga_jual × jumlah |

**Indexes**: PRIMARY KEY (kode_jual, kode_barang), FOREIGN KEYs on both

---

#### **pembelian**
Purchase orders (from suppliers).

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| kode_beli | varchar(10) | NO | - | PRIMARY | Manual entry (format: TRB#####) |
| tanggal_beli | varchar(20) | NO | - | - | DD-MM-YYYY format |
| waktu_beli | time | YES | - | - | HH:MM:SS format |
| no_faktur | varchar(50) | YES | - | - | Supplier invoice number |
| kode_suplier | varchar(10) | NO | - | FOREIGN | References suplier.kode_suplier |
| kode_admin | varchar(10) | NO | - | FOREIGN | Admin who created PO |
| total | int(11) | NO | 0 | - | Total purchase amount |

---

#### **pembelian_detail**
Line items in purchase orders.

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| kode_beli | varchar(10) | NO | - | PRIMARY | References pembelian.kode_beli |
| kode_barang | varchar(10) | NO | - | PRIMARY | References barang.kode_barang |
| harga_beli | int(11) | NO | 0 | - | Unit purchase price |
| jumlah | int(11) | NO | 0 | - | Quantity ordered |
| subtotal | int(11) | NO | 0 | - | harga_beli × jumlah |

---

#### **promo**
Promotional pricing rules.

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| id_promo | int(11) | NO | - | PRIMARY | Auto-increment |
| kode_barang | varchar(10) | NO | - | FOREIGN | References barang.kode_barang |
| harga_promo | int(11) | NO | - | - | Discounted price |
| tanggal_mulai | date | NO | - | - | Promo start date |
| tanggal_selesai | date | NO | - | - | Promo end date |
| status | tinyint(1) | NO | 1 | - | 1=active, 0=inactive |

---

#### **percobaan_karyawan**
Employee skill/product attempt tracking (trial/error logging).

| Field | Type | Null | Default | Key | Notes |
|-------|------|------|---------|-----|-------|
| id | int(11) | NO | - | PRIMARY | Auto-increment |
| id_karyawan | varchar(10) | NO | - | FOREIGN | References karyawan.kode_karyawan |
| id_barang | varchar(10) | NO | - | FOREIGN | References barang.kode_barang |
| isactive | tinyint(1) | NO | 1 | - | Active/inactive flag |
| created_at | timestamp | NO | CURRENT_TIMESTAMP | - | Attempt timestamp |

**Business Logic**: When count >= 2 for same employee+product, employee is blocked from that product

---

#### **ramal** & **ramal_harga**
Forecasting tables (structure not clearly defined in SQL dump).

---

### Data Relationships Diagram

```
admin (1) ──────── (*) penjualan
   ↑                      ↓
   |                      | (many-to-many)
   |                      ↓
karyawan (1) ────── (*) penjualan_detail
   |                      ↓
   |                      ↑
   |              (*) barang (1)
   |                      ↑
   |                      |
   |         (1) merk
   |
   └────── (*) percobaan_karyawan ─────→ barang

admin (1) ───────── (*) pembelian
                         ↓
suplier (1) ────────── pembelian
                         ↓ (many-to-many)
                         ↓
                    pembelian_detail
                         ↓
                        barang

barang (1) ───────── (*) promo
```

### Constraints

**Primary Keys**: All tables have single-column string PKs
**Foreign Keys**: 
- penjualan → admin, karyawan (optional)
- penjualan_detail → penjualan, barang
- pembelian_detail → pembelian, barang
- barang → merk
- percobaan_karyawan → karyawan, barang
- promo → barang

**Unique Constraints**: None explicitly defined (username should be unique but isn't enforced)

**Check Constraints**: None defined

**Default Values**: None except timestamps

### Indexes

| Table | Columns | Type |
|-------|---------|------|
| admin | kode_admin | PRIMARY KEY |
| barang | kode_barang | PRIMARY KEY |
| karyawan | kode_karyawan | PRIMARY KEY |
| merk | kode_merk | PRIMARY KEY |
| pembelian | kode_beli | PRIMARY KEY |
| pembelian_detail | (kode_beli, kode_barang) | PRIMARY KEY |
| penjualan | kode_jual | PRIMARY KEY |
| penjualan_detail | (kode_jual, kode_barang) | PRIMARY KEY |
| suplier | kode_suplier | PRIMARY KEY |

---

## 6. Authentication & Authorization

### Authentication Mechanism

**Type**: Session-based (server-side)
**Duration**: Session persists until explicit logout or browser close
**Token Format**: PHP SESSION superglobal
**Credentials**: Username + plaintext password

### Authentication Flow

```
User submits /login
    ↓
Check admin table for username
    ↓
If found: Compare plaintext password
    ↓ (match)
Check if admin? → YES → Set level="admin"
    ↓
If not admin, check karyawan table
    ↓
If found: Compare plaintext password
    ↓ (match)
Check working hours: isWithinWorkingHours()
    ↓ (within hours)
Set level="karyawan" or "karyawan_admin" (based on level field)
    ↓
Set session variables
    ↓
Redirect to /home
```

### Session Structure

```php
$_SESSION = [
    "username" => "admin",           // Login username
    "kode" => "ADM00001",            // User code (kode_admin or kode_karyawan)
    "level" => "admin",              // Role: admin, karyawan, karyawan_admin
    "can_see_stock" => true,         // Stock visibility (karyawan only)
    "can_see_sales" => true,         // Sales visibility (karyawan only)
    // Additional session data for transactions:
    "{kode}detailbarang" => [...],   // Sales cart items
    "{kode}detailbarang_pembelian" => [...], // Purchase cart items
    "{kode}ongkos_karyawan" => 50000, // Employee cost accumulator
]
```

### Authorization Rules

#### By Controller

| Controller | Admin | Karyawan | Karyawan_Admin | Anonymous |
|-----------|-------|----------|---|-----------|
| Login | ✓ (redirect home) | ✓ (redirect home) | ✓ (redirect home) | ✓ |
| Admin | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Karyawan | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Barang | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Merk | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Suplier | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Pembelian | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Penjualan | ✓ | ✓ | ✓ | ✗ |
| Laporan | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Home | ✓ | ✓ | ✓ | ✗ |
| Promo | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Ramal | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Ramal_harga | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Sync | ✓ | ✗ (redirect home) | ✓ | ✗ |
| Logout | ✓ | ✓ | ✓ | ✓ |

#### By Feature

| Feature | Required Role | Permission Checks |
|---------|---|---|
| Master Data CRUD (Admin, Barang, Merk, Suplier, Karyawan) | admin, karyawan_admin | Session check + level check |
| Sales (Penjualan, Home) | Any authenticated + can_see_sales=1 | Session check + can_see_sales flag |
| Purchase (Pembelian) | admin, karyawan_admin | Session check + level check |
| Reports (Laporan) | admin, karyawan_admin | Session check + level check |
| Forecasting (Ramal) | admin, karyawan_admin | Session check + level check |

#### Granular Access Controls

1. **Employee Working Hours**: 
   - Enforced at login time via `Karyawan_model->isWithinWorkingHours()`
   - Prevents out-of-hours access
   - Next-day hour ranges supported

2. **Employee Skill Blocking**:
   - Employees blocked from specific products after 2 failed attempts
   - Tracked in percobaan_karyawan table
   - Managers can unblock via `/karyawan/unlock/{kode}/{id_barang}`

3. **Stock Visibility**:
   - can_see_stock flag controls if employee can view inventory
   - can_see_sales flag controls if employee can view sales data

4. **Role-Based Menus**:
   - Navigation bar (nav.php) likely filters menu items by level
   - Not clearly defined in provided code

### Middleware/Guards

**No explicit middleware framework used**. Authorization implemented via:
- Constructor-level session checks: `if (!isset($_SESSION['level'])) redirect('login')`
- Controller method guards: `if ($_SESSION['level'] == 'karyawan') redirect('home')`
- Working hour validation in Login controller

### Security Concerns (Not Implemented)

- **Password Storage**: Plaintext passwords in database (no hashing)
- **SQL Injection**: Raw SQL queries in some models (e.g., Karyawan_model::get_limit_data)
- **CSRF Protection**: No CSRF token validation
- **Session Fixation**: Session not regenerated on login
- **XSS Protection**: No output escaping shown in models/controllers
- **Brute Force**: No login attempt rate limiting
- **Password Complexity**: No validation rules on password strength
- **HTTPS**: Not enforced or mentioned

---

## 7. Deployment & Infrastructure

### Environment Configuration
- **Timezone**: Asia/Jakarta (GMT+7) - hardcoded in Login.php
- **Database Charset**: UTF8MB4
- **Session Handler**: PHP native file-based

### Configuration Files
- `application/config/database.php` - Database connection (not provided in analysis)
- `application/config/routes.php` - URI routing rules
- `application/config/config.php` - Application settings
- `application/config/constants.php` - App constants

### Database Configuration
- **Host**: localhost (assumed)
- **Port**: 3306 (default MySQL)
- **Charset**: UTF8MB4
- **Collation**: Not specified

### File Structure Requirements
- Writable `/application/logs/` directory for error logs
- Writable `/application/views/` for dynamic views
- Session storage in PHP tmp directory (default)

---

## 8. Known Limitations & Technical Debt

1. **No Data Validation on Update Operations**: Form validation rules (_rule) defined but some controllers don't enforce them
2. **No Transaction Rollback**: Multi-step operations (cart → finalize) not wrapped in DB transactions
3. **Session-Based Cart**: Vulnerable to session hijacking; not suitable for long-term carts
4. **Raw SQL Queries**: Some models use string concatenation (SQL injection risk)
5. **No Soft Deletes**: Hard deletes permanently remove data
6. **Limited Error Handling**: No try-catch blocks in controllers/models
7. **No API Layer**: All endpoints are server-side rendered views
8. **No Logging**: Transaction/audit logs not implemented
9. **No Notification System**: No email/SMS alerts on transactions
10. **No Multi-User Concurrency Control**: Two users editing same record = last-write-wins
11. **Hardcoded Timezone**: Asia/Jakarta timezone not configurable
12. **No Foreign Key Constraints**: Database-level referential integrity not enforced
13. **No Prepared Statements in Some Queries**: Direct string interpolation in raw SQL

---

## 9. Version & Compatibility

- **CodeIgniter Version**: 3.x (based on syntax and structure)
- **PHP Minimum**: 5.2.4 (per composer.json)
- **PHP Recommended**: 5.4+
- **MySQL/MariaDB**: 5.1.9+ / 10.1.9+
- **Browser Support**: Modern browsers with jQuery 2.0 support (no IE8 or older)

---

## 10. Future Enhancement Opportunities

1. **API Endpoints**: Add RESTful API for mobile app integration
2. **Database**: Implement foreign key constraints, add timestamps to all tables
3. **Authentication**: Replace plaintext passwords with bcrypt hashing, add 2FA
4. **Workflow**: Implement approval workflows for large transactions
5. **Notifications**: Email/SMS alerts for low stock, overdue payments
6. **Dashboard**: Real-time charts and KPI widgets
7. **Mobile App**: React Native app with REST API backend
8. **Inventory Alerts**: Automatic reorder points and threshold notifications
9. **Multi-Location**: Support for multiple workshop branches
10. **Integration**: APIs to connect with accounting/ERP systems

---

**Document Generated**: Reverse-engineered from source code
**Last Updated**: Current codebase state
**Schema Version**: Based on bengkel.sql (generated July 30, 2016)
