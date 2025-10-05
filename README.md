# Simple POS CI3 - Sales Transaction System

A simple Point of Sale (POS) system built with CodeIgniter 3 that simulates sales transactions and generates printable receipts.

## 🚀 Features

- **Master Data Management**
  - Product (Barang) management with CRUD operations
  - Promotional discount management
  
- **Sales Transaction**
  - Create new sales transactions
  - Multi-item transactions with quantity and discounts
  - Automatic calculation of subtotal, PPN (10%), and grand total
  - Promotional code application with discount calculation
  
- **Receipt Generation**
  - Printable receipt format
  - Professional invoice layout
  - Print-ready CSS styling
  
- **Reporting**
  - Sales transaction listing with filters
  - Sales reports by date range
  - Top-selling products analytics
  - Dashboard with key statistics

## 🛠️ Technology Stack

- **Framework**: CodeIgniter 3
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Font Awesome, jQuery
- **PHP Version**: 7.4
- **Built with**: CodeIgniter 3 and PHP 7.4

## 📊 Database Schema

The system uses 4 main tables:

### penjualan_header
```sql
- no_transaksi (string, Primary Key)
- tgl_transaksi (date)
- customer (string)
- kode_promo (string, Foreign Key)
- total_bayar (int)
- ppn (int)
- grand_total (int)
```

### penjualan_header_detail
```sql
- id (int, Primary Key, Auto Increment)
- no_transaksi (string, Foreign Key)
- kode_barang (string, Foreign Key)
- qty (int)
- harga (int)
- discount (int)
- subtotal (int)
```

### master_barang
```sql
- kode_barang (string, Primary Key)
- nama_barang (string)
- harga (int)
```

### promo
```sql
- kode_promo (string, Primary Key)
- nama_promo (string)
- ketereangan (string)
```

## 🔧 Installation & Setup

### Environment Configuration

The application supports environment variables for configuration. To use environment variables:

1. Copy the [.env.example](d:\Proj\dddd\simple-pos-ci3\.env.example) file to `.env`:
   ```bash
   cp .env.example .env
   ```
   On Windows:
   ```cmd
   copy .env.example .env
   ```

2. Modify the `.env` file with your specific settings:
   ```env
   # Application Settings
   BASE_URL=http://localhost:8080/
   APP_ENV=development

   # Database Configuration
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=simple_pos_ci3
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   ```

### Option 1: Docker Setup (Recommended)

**Prerequisites:**
- Docker Engine 20.10+
- Docker Compose 2.0+

**Quick Start:**
```bash
# Clone the project
git clone [your-repository-url]
cd simple-pos-ci3

# Copy .env file and modify as needed
cp .env.example .env
# Edit .env file to match your Docker configuration

# Start with Docker
docker-compose up -d --build

# Access the application
# Web App: http://localhost:8080
# phpMyAdmin: http://localhost:8081
```

**Services:**
- **App**: PHP 7.4 + Apache + Simple POS CI3 (Port 8080)
- **Database**: MySQL 5.7 (Port 3306)
- **phpMyAdmin**: Database admin interface (Port 8081)

For detailed Docker setup instructions, see [DOCKER_SETUP.md](DOCKER_SETUP.md).

### Option 2: XAMPP Setup

If you prefer to use XAMPP instead of Docker:

**Prerequisites:**
- XAMPP with PHP 7.4
- MySQL database server

**Setup Steps:**
1. **Copy files to XAMPP:**
   - Copy the entire project folder to your XAMPP `htdocs` directory
   - For example: `C:\xampp\htdocs\simple-pos-ci3\`

2. **Create database:**
   - Start XAMPP and ensure Apache and MySQL services are running
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Create a new database named `simple_pos_ci3`
   - Import the database schema from `databases/database_setup.sql`

3. **Configure environment variables:**
   - Copy `.env.example` to `.env`:
   ```cmd
   copy .env.example .env
   ```
   - Edit the `.env` file with your XAMPP database settings:
   ```env
   BASE_URL=http://localhost/simple-pos-ci3/
   DB_HOST=localhost
   DB_DATABASE=simple_pos_ci3
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Set file permissions:**
   - Ensure the following directories are writable:
   ```bash
   chmod 755 application/cache/
   chmod 755 application/logs/
   ```
   - On Windows, right-click these folders → Properties → Security → Ensure Apache/PHP has write permissions

5. **Access the application:**
   - Open your browser and navigate to:
   ```
   http://localhost/simple-pos-ci3/
   ```

### Option 3: Manual Installation

### Prerequisites
- Web Server (Apache/Nginx)
- PHP 7.4
- MySQL 5.7 or higher
- Composer (optional, for dependencies)

### Step 1: Clone/Download Project
```bash
git clone https://github.com/radityaIch/simple-pos-ci3.git
# or download and extract the ZIP file
```

### Step 2: Database Setup
1. Create a new MySQL database named `simple_pos_ci3`
2. Import the database schema and sample data:
```sql
mysql -u root -p simple_pos_ci3 < databases/database_setup.sql
```

### Step 3: Environment Configuration
1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```
2. Edit the `.env` file with your database settings:
   ```env
   BASE_URL=http://your-domain.com/simple-pos-ci3/
   DB_HOST=localhost
   DB_DATABASE=simple_pos_ci3
   DB_USERNAME=your_db_username
   DB_PASSWORD=your_db_password
   ```

### Step 4: Server Configuration
1. Ensure your web server document root points to the project directory
2. Make sure `mod_rewrite` is enabled (for Apache)
3. Set appropriate file permissions:
```bash
chmod 755 application/cache/
chmod 755 application/logs/
```

### Step 5: Access the Application
Open your browser and navigate to:
```
http://your-domain.com/simple-pos-ci3/
```

## 🎯 Usage Guide

### Dashboard
- Access via: `http://localhost/simple-pos-ci3/`
- View daily/monthly statistics
- Quick access to main functions
- Recent transactions overview

### Master Data Management

#### Products (Master Barang)
- **URL**: `/master_barang`
- **Add**: Click "Tambah Barang" button
- **Edit**: Click edit icon in actions column
- **Delete**: Click delete icon (only if not used in transactions)
- **Search**: Use search form to find products

#### Promotions (Promo)
- **URL**: `/promo`
- **Add**: Click "Tambah Promo" button
- **Edit**: Click edit icon in actions column
- **Delete**: Click delete icon (only if not used in transactions)

### Sales Transactions

#### Create New Transaction
1. Navigate to `/sales/create`
2. Fill in customer name
3. Select promotional code (optional)
4. Add items by:
   - Selecting product from dropdown
   - Setting quantity
   - Adding item discount (optional)
5. System automatically calculates:
   - Item subtotals
   - Total amount
   - PPN (10%)
   - Promotional discount
   - Grand total
6. Click "Simpan Transaksi" to save

#### View Transactions
- **List**: Navigate to `/sales`
- **Filter**: Use date range filters
- **Detail**: Click transaction number to view details
- **Receipt**: Click "Print Receipt" in transaction detail

### Reports
- **URL**: `/sales/report`
- **Features**:
  - Sales summary by date range
  - Top-selling products
  - Total transactions and revenue

## 🔗 URL Routes

| URL | Description |
|-----|-------------|
| `/` or `/dashboard` | Main dashboard |
| `/master_barang` | Product management |
| `/master_barang/add` | Add new product |
| `/master_barang/edit/{code}` | Edit product |
| `/promo` | Promotion management |
| `/promo/add` | Add new promotion |
| `/sales` | Transaction listing |
| `/sales/create` | Create new transaction |
| `/sales/view/{transaction_no}` | View transaction detail |
| `/sales/receipt/{transaction_no}` | Print receipt |
| `/sales/report` | Sales reports |

## 💡 Sample Data

The system comes with pre-loaded sample data:

### Sample Products
- BRG001: Laptop Dell Inspiron 15 (Rp 8,500,000)
- BRG002: Mouse Wireless Logitech (Rp 250,000)
- BRG003: Keyboard Mechanical RGB (Rp 750,000)
- And 7 more products...

### Sample Promotions
- **DISC10**: 10% discount for purchases ≥ Rp 1,000,000
- **DISC50K**: Rp 50,000 discount for purchases ≥ Rp 500,000
- **NEWCUST**: 15% discount for new customers ≥ Rp 2,000,000
- **WEEKEND**: Rp 100,000 discount for weekend shopping ≥ Rp 1,500,000

## 🧮 Business Logic

### Transaction Calculation
1. **Item Subtotal**: (Quantity × Price) - Item Discount
2. **Total**: Sum of all item subtotals
3. **PPN**: Total × 10%
4. **Grand Total**: Total + PPN

### Promotional Rules
Promo codes serve as labels only and do not calculate discounts automatically:
- Promo codes validate that the entered code exists in the system
- Actual discounts are applied at the item level using the "Discount" field in [`penjualan_header_detail`]
- This approach provides more flexibility for individual item discounts

## 🔒 Security Features

- Input validation and sanitization
- XSS protection via CodeIgniter's security class
- Foreign key constraints to maintain data integrity
- Session management for flash messages

## 📱 Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Responsive design for mobile devices
- Print-optimized receipt layout

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `.env` file
   - Ensure MySQL service is running
   - Verify database exists

2. **Base URL Issues**
   - Update `BASE_URL` in `.env` file
   - Check web server configuration

3. **File Permission Issues**
   - Set proper permissions for cache and logs directories
   - Ensure web server can read/write to application folders

4. **Receipt Not Printing**
   - Check browser print settings
   - Ensure print CSS is loaded
   - Test with different browsers

---

**Note**: This is a demonstration project. For production use, additional security measures and features should be implemented.