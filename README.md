# INVAS - Inventory Management System for SMK Assalaam

## Application Description

INVAS (Inventory System) is an inventory management application designed specifically for SMK Assalaam to record, manage, and track all school assets. This application makes it easy for school administrators to manage item stock, record incoming and outgoing items, and manage item loans between rooms with a structured and integrated system.

The main purposes of this application are:

- Record inventory digitally and in a structured manner
- Track item movement (incoming, outgoing, borrowing, returning)
- Manage item stock per room
- Generate inventory reports and statistics
- Maintain the integrity of school inventory data

## Key Features

### 1. Item Management

- Add, edit, and delete item data
- Item numbering system (unique item codes)
- Recording of brand, status, and item photos
- Real-time inventory tracking
- Export item data to PDF and Excel formats

### 2. Incoming Item Recording

- Adding new items to inventory
- Recording date and description of incoming items
- Grouping items by room
- Automatic stock updates when items arrive
- Export incoming item reports to PDF and Excel

### 3. Outgoing Item Recording

- Recording item removals from inventory
- Tracking items removed per room
- Recording date and description of removal
- Automatic stock updates when items leave
- Export outgoing item reports to PDF and Excel

### 4. Item Loan Management

- System for loaning items between rooms
- Recording borrower name and loan date
- Setting return date
- Tracking loan status (borrowed, returned)
- Export loan reports to PDF and Excel

### 5. Item Return Recording

- Process for returning borrowed items
- Item return validation system
- Recording return date and status
- Automatic stock updates after return
- Export return reports to PDF and Excel

### 6. Room Management

- Add and manage room/location data
- Recording room descriptions
- Item tracking per room
- Item distribution system between rooms

### 7. Statistics and Reports

- Inventory statistics dashboard
- Item and movement data visualization
- Real-time inventory condition reports
- Data analytics for decision making

### 8. Employee Management

- Master employee data
- Recording employee information
- Export employee data to PDF and Excel
- Integration with loan system

## Technology Stack

### Backend

- **Laravel 9.19** - PHP Web Framework
- **PHP 8.0.2+** - Programming Language
- **MySQL** - Database Management System
- **Composer** - PHP Package Manager

### Frontend

- **Bootstrap 5.2.3** - UI Framework CSS
- **Blade Template Engine** - Laravel's Template Engine
- **SASS/SCSS** - CSS Preprocessor
- **Vite 4.0** - Build Tool & Development Server
- **Axios** - HTTP Client
- **Lodash** - JavaScript Utility Library
- **Popper.js** - Positioning Engine

### Additional Libraries & Extensions

- **Maatwebsite Excel 3.1** - Excel Import/Export
- **Barryvdh DOMPDF 3.1** - PDF Generation
- **Sweet Alert 7.3** - Beautiful Alerts & Notifications
- **Realrashid Sweet Alert** - Alert Package
- **Laravel Sanctum 3.0** - API Token Authentication
- **Laravel Tinker 2.7** - Interactive Shell
- **Doctrine DBAL 4.2** - Database Abstraction Layer
- **Carbon 2.73** - Date/Time Library
- **Guzzle HTTP 7.2** - HTTP Client

### Development Tools

- **PHPUnit 9.5.10** - Testing Framework
- **Laravel Sail 1.0.1** - Local Development Environment
- **FakerPHP 1.9.1** - Fake Data Generator
- **Mockery 1.4.4** - Mocking Library
- **Spatie Ignition 1.0** - Error Debugging
- **Laravel Pint 1.0** - Code Style Fixer

## System Requirements

### Minimum Requirements

- PHP version 8.0.2 or higher
- MySQL version 5.7 or higher (or MariaDB 10.2+)
- Composer (PHP Dependency Manager)
- Node.js version 14+ and NPM or Yarn
- RAM at least 2GB
- Storage at least 500MB

### Recommended Requirements

- PHP version 8.1+
- MySQL version 8.0+
- Node.js version 16+
- RAM at least 4GB
- SSD Storage

## Installation

### 1. Clone Repository

```bash
git clone <repository-url>
cd inventaris-gudangku
```

### 2. Install Dependencies

#### Backend Dependencies

```bash
composer install
```

#### Frontend Dependencies

```bash
npm install
# or
yarn install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Then configure the `.env` file according to your local environment:

```env
APP_NAME="INVAS - Inventory Management System SMK Assalaam"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gudangku
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Database Setup

#### Run Migrations

```bash
php artisan migrate
```

#### Seed Database (Optional - Add Dummy Data)

```bash
php artisan db:seed
```

### 5. Build Assets

```bash
npm run build
# Or for development with hot reload:
npm run dev
```

### 6. Run Application

#### Development Server

```bash
php artisan serve
```

Server will run at `http://localhost:8000`

#### Frontend Development Server (If using Vite)

```bash
npm run dev
```

## Directory Structure

```
inventaris-gudangku/
├── app/
│   ├── Console/              # Console Commands
│   ├── Exceptions/           # Exception Handlers
│   ├── Exports/              # Excel Export Classes
│   │   ├── BarangExport.php
│   │   ├── BarangKeluarExport.php
│   │   ├── BarangMasukExport.php
│   │   ├── BarangRuanganExport.php
│   │   ├── KaryawanExport.php
│   │   ├── PeminjamanExport.php
│   │   ├── PengembalianExport.php
│   │   └── RuanganExport.php
│   ├── Http/
│   │   ├── Controllers/      # Controller Classes
│   │   │   ├── Auth/
│   │   │   ├── BarangController.php
│   │   │   ├── BarangKeluarController.php
│   │   │   ├── BarangMasukController.php
│   │   │   ├── BarangRuangansController.php
│   │   │   ├── HomeController.php
│   │   │   ├── KaryawanController.php
│   │   │   ├── PeminjamanController.php
│   │   │   ├── PengembalianController.php
│   │   │   ├── RuangansController.php
│   │   │   ├── StatistikController.php
│   │   │   └── Controller.php
│   │   ├── Kernel.php        # HTTP Kernel
│   │   └── Middleware/       # Middleware Classes
│   ├── Models/               # Eloquent Model Classes
│   │   ├── Barangs.php
│   │   ├── BarangKeluars.php
│   │   ├── BarangMasuks.php
│   │   ├── BarangRuangans.php
│   │   ├── Peminjamans.php
│   │   ├── Pengembalians.php
│   │   ├── Ruangans.php
│   │   └── User.php
│   ├── Providers/            # Service Providers
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   └── View/
│       └── Components/       # Blade Components
├── bootstrap/
│   ├── app.php               # Application Bootstrap
│   └── cache/                # Bootstrap Cache
├── config/                   # Configuration Files
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── dompdf.php
│   ├── excel.php
│   ├── filesystems.php
│   ├── mail.php
│   └── ...
├── database/
│   ├── factories/            # Model Factories
│   │   └── UserFactory.php
│   ├── migrations/           # Database Migrations
│   └── seeders/              # Database Seeders
├── lang/                     # Translation Files
│   └── en/
├── public/                   # Web Root Directory
│   ├── index.php
│   ├── admin/                # Admin Assets
│   ├── image/                # Image Assets
│   └── vendor/               # Vendor Assets
├── resources/
│   ├── css/                  # CSS Files
│   ├── js/                   # JavaScript Files
│   ├── sass/                 # SASS/SCSS Files
│   └── views/                # Blade Template Files
├── routes/
│   ├── api.php               # API Routes
│   ├── channels.php          # Broadcasting Channels
│   ├── console.php           # Console Commands
│   └── web.php               # Web Routes
├── storage/
│   ├── app/                  # File Storage
│   ├── framework/            # Framework Storage
│   └── logs/                 # Application Logs
├── tests/                    # Test Files
│   ├── Feature/
│   └── Unit/
├── .env                      # Environment Configuration
├── .env.example              # Environment Example
├── artisan                   # Laravel Artisan CLI
├── composer.json             # PHP Dependencies
├── package.json              # Node Dependencies
├── vite.config.js            # Vite Configuration
├── phpunit.xml               # PHPUnit Configuration
└── README.md                 # Documentation
```

## Database Structure & Migrations

### 1. Users Table (Default Laravel)

Stores system user/admin data.

**Main Columns:**

- `id` - Primary Key
- `name` - Username
- `email` - User email (unique)
- `password` - Encrypted password
- `email_verified_at` - Email verification timestamp
- `remember_token` - Remember me token
- `created_at`, `updated_at` - Timestamp

### 2. Ruangans Table

Stores room/location data at school.

**Columns:**

- `id` - Primary Key
- `nama_ruangan` - Room name (unique, example: "Room A", "Computer Lab")
- `deskripsi` - Detailed room description
- `created_at`, `updated_at` - Timestamp

### 3. Barangs Table

Stores master inventory item data.

**Columns:**

- `id` - Primary Key
- `kode_barang` - Unique item code (example: "BRG001")
- `nama` - Item name
- `merek` - Item brand/manufacturer
- `foto` - Item photo path
- `stok` - Total item stock
- `status_barang` - Item status (new, old, damaged, etc.)
- `id_user` - Foreign Key to Users (Admin who entered the data)
- `created_at`, `updated_at` - Timestamp

**Relations:**

- Belongs to User

### 4. Barang Ruangans Table (Junction/Pivot)

Stores item stock distribution per room (Many-to-Many).

**Columns:**

- `id` - Primary Key
- `barang_id` - Foreign Key to Barangs
- `ruangan_id` - Foreign Key to Ruangans
- `stok` - Number of items in this room
- `created_at`, `updated_at` - Timestamp

**Relations:**

- Belongs to Barangs
- Belongs to Ruangans
- Unique constraint on combination of `barang_id` and `ruangan_id`

### 5. Barang Masuks Table

Stores history of incoming items to inventory.

**Columns:**

- `id` - Primary Key
- `kode_barang` - Incoming item code (unique per transaction)
- `id_barang` - Foreign Key to Barangs
- `jumlah` - Quantity of incoming items
- `tanggal_masuk` - Item arrival date
- `keterangan` - Item arrival description/reason
- `ruangan_id` - Foreign Key to Ruangans (destination room)
- `id_user` - Foreign Key to Users (Admin who recorded it)
- `created_at`, `updated_at` - Timestamp

**Relations:**

- Belongs to Barangs
- Belongs to Ruangans
- Belongs to User

### 6. Barang Keluars Table

Stores history of outgoing items from inventory.

**Columns:**

- `id` - Primary Key
- `kode_barang` - Outgoing item code (unique per transaction)
- `id_barang` - Foreign Key to Barangs
- `jumlah` - Quantity of outgoing items
- `tanggal_keluar` - Item departure date
- `keterangan` - Item departure description/reason
- `ruangan_id` - Foreign Key to Ruangans (source room)
- `id_user` - Foreign Key to Users (Admin who recorded it)
- `created_at`, `updated_at` - Timestamp

**Relations:**

- Belongs to Barangs
- Belongs to Ruangans
- Belongs to User

### 7. Peminjamans Table

Stores data for items loaned between rooms.

**Columns:**

- `id` - Primary Key
- `kode_barang` - Loan code (unique per transaction)
- `id_barang` - Foreign Key to Barangs
- `jumlah` - Quantity of loaned items
- `tanggal_pinjam` - Loan date
- `tanggal_kembali` - Planned return date
- `nama_peminjam` - Name of borrower
- `status` - Loan status (borrowed, returned, lost, etc.)
- `ruangan_id` - Foreign Key to Ruangans (borrowing room)
- `id_user` - Foreign Key to Users (Admin who recorded it)
- `created_at`, `updated_at` - Timestamp

**Relations:**

- Belongs to Barangs
- Belongs to Ruangans
- Belongs to User

### 8. Pengembalians Table

Stores data for returned borrowed items.

**Columns:**

- `id` - Primary Key
- `kode_barang` - Return code (unique per transaction)
- `id_barang` - Foreign Key to Barangs
- `jumlah` - Quantity of returned items
- `tanggal_kembali` - Actual return date
- `nama_peminjam` - Name of person returning
- `status` - Return status (complete, damaged, lost, etc.)
- `id_peminjam` - Foreign Key to Peminjamans (nullable)
- `ruangan_id` - Foreign Key to Ruangans (source room)
- `id_user` - Foreign Key to Users (Admin who recorded it)
- `created_at`, `updated_at` - Timestamp

**Relations:**

- Belongs to Barangs
- Belongs to Ruangans
- Belongs to Peminjamans
- Belongs to User

### Database Relationship Diagram

```
Users
  ├── has Many -> Barangs
  ├── has Many -> Barang Masuks
  ├── has Many -> Barang Keluars
  ├── has Many -> Peminjamans
  └── has Many -> Pengembalians

Ruangans
  ├── has Many -> Barang Masuks
  ├── has Many -> Barang Keluars
  ├── has Many -> Peminjamans
  ├── has Many -> Pengembalians
  └── has Many Through -> Barang Ruangans

Barangs
  ├── belongs To User
  ├── has Many -> Barang Masuks
  ├── has Many -> Barang Keluars
  ├── has Many -> Peminjamans
  ├── has Many -> Pengembalians
  └── belongs To Many -> Ruangans (Through Barang Ruangans)

Barang Ruangans (Pivot/Junction)
  ├── belongs To Barangs
  └── belongs To Ruangans

Barang Masuks
  ├── belongs To Barangs
  ├── belongs To Ruangans
  └── belongs To User

Barang Keluars
  ├── belongs To Barangs
  ├── belongs To Ruangans
  └── belongs To User

Peminjamans
  ├── belongs To Barangs
  ├── belongs To Ruangans
  ├── belongs To User
  └── has Many -> Pengembalians

Pengembalians
  ├── belongs To Barangs
  ├── belongs To Ruangans
  ├── belongs To Peminjamans
  └── belongs To User
```

## Initial Configuration

### 1. Database Setup

Make sure MySQL is running and update `.env` with your database credentials.

### 2. Run Migrations

Create the production database according to the defined structure:

```bash
php artisan migrate
```

### 3. Seed Data (Optional)

If you want to add dummy data for testing:

```bash
php artisan db:seed
```

### 4. Setup Storage Folder

Make sure `storage` and `bootstrap/cache` folders have the correct permissions:

```bash
chmod -R 775 storage bootstrap/cache
```

### 5. Generate Key

Make sure APP_KEY is generated:

```bash
php artisan key:generate
```

## How to Use the Application

### 1. Login to Application

- Access the application at `http://localhost:8000`
- Login with the admin credentials created

### 2. Item Management

- Navigate to "Items" menu
- Add new item by clicking "Add Item"
- Fill in item data, upload photo, and save
- Edit or delete items as needed

### 3. Incoming Item Recording

- Navigate to "Incoming Items" menu
- Click "Add Incoming Item"
- Select item, enter quantity, date, and destination room
- Stock automatically updates
- Export report if needed

### 4. Outgoing Item Recording

- Navigate to "Outgoing Items" menu
- Click "Add Outgoing Item"
- Select item, enter quantity, date, and source room
- Stock automatically updates
- Export report if needed

### 5. Room Management

- Navigate to "Rooms" menu
- Add or edit room data
- View item distribution per room

### 6. Borrowing and Returning Items

- Recording loans: "Loans" Menu > Add Loan
- Recording returns: "Returns" Menu > Add Return
- Track loan and return status

### 7. View Statistics & Reports

- Navigate to "Statistics" menu
- View inventory data visualization
- Analyze item movement and stock conditions
- Export reports in PDF or Excel format

## Routes API & Web

### Web Routes Admin (Protected by middleware: auth, RoleMiddleware)

**Dashboard**

- `GET /admin/home` - Admin main dashboard

**Statistics**

- `GET /admin/statistik` - Inventory statistics and analytics

**Items**

- `GET /admin/barang` - List all items
- `GET /admin/barang/create` - Add item form
- `POST /admin/barang` - Store new item
- `GET /admin/barang/{id}` - Item details
- `GET /admin/barang/{id}/edit` - Edit item form
- `PUT /admin/barang/{id}` - Update item
- `DELETE /admin/barang/{id}` - Delete item
- `GET /admin/barang-export` - Export items to PDF
- `GET /admin/barang-export-excel` - Export items to Excel

**Incoming Items**

- `GET /admin/brg-masuk` - List incoming items
- `GET /admin/brg-masuk/create` - Add incoming item form
- `POST /admin/brg-masuk` - Store incoming item
- `GET /admin/brg-masuk/{id}` - Incoming item details
- `GET /admin/brg-masuk/{id}/edit` - Edit incoming item form
- `PUT /admin/brg-masuk/{id}` - Update incoming item
- `DELETE /admin/brg-masuk/{id}` - Delete incoming item
- `GET /admin/brg-masuk-export` - Export to PDF
- `GET /admin/brg-masuk-export-excel` - Export to Excel

**Outgoing Items**

- `GET /admin/brg-keluar` - List outgoing items
- `GET /admin/brg-keluar/create` - Add outgoing item form
- `POST /admin/brg-keluar` - Store outgoing item
- `GET /admin/brg-keluar/{id}` - Outgoing item details
- `GET /admin/brg-keluar/{id}/edit` - Edit outgoing item form
- `PUT /admin/brg-keluar/{id}` - Update outgoing item
- `DELETE /admin/brg-keluar/{id}` - Delete outgoing item
- `GET /admin/brg-keluar-export` - Export to PDF
- `GET /admin/brg-keluar-export-excel` - Export to Excel
- `GET /get-barang-by-ruangan/{ruanganId}` - Get items per room (AJAX)

**Loans**

- `GET /admin/peminjaman` - List loans
- `GET /admin/peminjaman/create` - Add loan form
- `POST /admin/peminjaman` - Store loan
- `GET /admin/peminjaman/{id}` - Loan details
- `GET /admin/peminjaman/{id}/edit` - Edit loan form
- `PUT /admin/peminjaman/{id}` - Update loan
- `DELETE /admin/peminjaman/{id}` - Delete loan
- `GET /admin/peminjaman-export` - Export to PDF
- `GET /admin/peminjaman-export-excel` - Export to Excel

**Returns**

- `GET /admin/pengembalian` - List returns
- `GET /admin/pengembalian/create` - Add return form
- `POST /admin/pengembalian` - Store return
- `GET /admin/pengembalian/{id}` - Return details
- `GET /admin/pengembalian/{id}/edit` - Edit return form
- `PUT /admin/pengembalian/{id}` - Update return
- `DELETE /admin/pengembalian/{id}` - Delete return
- `GET /admin/pengembalian-export` - Export to PDF
- `GET /admin/pengembalian-export-excel` - Export to Excel

**Rooms**

- `GET /admin/ruang` - List rooms
- `GET /admin/ruang/create` - Add room form
- `POST /admin/ruang` - Store room
- `GET /admin/ruang/{id}` - Room details
- `GET /admin/ruang/{id}/edit` - Edit room form
- `PUT /admin/ruang/{id}` - Update room
- `DELETE /admin/ruang/{id}` - Delete room

**Item Rooms**

- `GET /admin/barang-ruangan` - List items per room
- `GET /admin/barang-ruangan/create` - Add item to room form
- `POST /admin/barang-ruangan` - Store item to room
- `GET /admin/barang-ruangan/{id}` - Item room details
- `GET /admin/barang-ruangan/{id}/edit` - Edit item room form
- `PUT /admin/barang-ruangan/{id}` - Update item room
- `DELETE /admin/barang-ruangan/{id}` - Delete item room

**Employees**

- `GET /admin/karyawan` - List employees
- `GET /admin/karyawan/create` - Add employee form
- `POST /admin/karyawan` - Store employee
- `GET /admin/karyawan/{id}` - Employee details
- `GET /admin/karyawan/{id}/edit` - Edit employee form
- `PUT /admin/karyawan/{id}` - Update employee
- `DELETE /admin/karyawan/{id}` - Delete employee
- `GET /admin/karyawan-export` - Export to PDF
- `GET /admin/karyawan-export-excel` - Export to Excel

**Authentication**

- `POST /logout` - User logout
- `GET /login` - Login form
- `POST /login` - Login process

## Troubleshooting

### 1. Database Connection Error

**Issue:** "Connection refused" or "SQLSTATE[HY000]"

**Solution:**

- Make sure MySQL/MariaDB is running
- Verify `.env` has correct database credentials
- Retry: `php artisan migrate`

### 2. Permission Denied on Storage

**Issue:** "Permission denied" when uploading files

**Solution:**

```bash
chmod -R 775 storage bootstrap/cache
```

### 3. Composer or NPM Dependencies Error

**Issue:** Error when running `composer install` or `npm install`

**Solution:**

- Delete lock file: `rm composer.lock` or `rm package-lock.json`
- Run again: `composer install` or `npm install`

### 4. Vite Auto-Reload Not Working

**Issue:** Assets not updating during development

**Solution:**

- Make sure `npm run dev` is running in a separate terminal
- Clear browser cache (Ctrl+Shift+Delete)
- Restart `npm run dev`

### 5. Login Failed

**Issue:** Cannot login even with correct password

**Solution:**

- Make sure `php artisan migrate` has been run
- Make sure user exists in database
- Clear sessions: `php artisan cache:clear`

## Development Tips

### 1. Using Artisan Tinker

```bash
php artisan tinker
```

For testing queries or logic in an interactive shell.

### 2. Generate Model & Migration

```bash
php artisan make:model ModelName -m
```

### 3. Clear Cache & Config

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 4. Database Reset

```bash
php artisan migrate:reset
php artisan migrate
```

### 5. Running Tests

```bash
php artisan test
# or
./vendor/bin/phpunit
```

## Contributing

Contributions are welcome! To contribute:

1. Fork this repository
2. Create a feature branch: `git checkout -b feature/AmazingFeature`
3. Commit your changes: `git commit -m 'Add some AmazingFeature'`
4. Push to the branch: `git push origin feature/AmazingFeature`
5. Open a Pull Request

## License

This application is licensed under the MIT License. See the `LICENSE` file for more information.

## Support & Contact

For questions, issues, or suggestions about this application, please contact the SMK Assalaam administrator or open an issue in this repository.

## Changelog

### Version 1.0.0

- Initial Release
- Basic inventory management features
- Incoming and outgoing items system
- Item loan and return system
- Dashboard and statistics
- Export to PDF and Excel

---

**Created for SMK Assalaam**
INVAS Application - Inventory Management System for SMK Assalaam
Version 1.0.0 | 2024-2025
