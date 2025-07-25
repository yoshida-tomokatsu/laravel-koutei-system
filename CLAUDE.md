# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview
This is a Laravel-based order management system ("工程管理システム") for Kiryu Factory, migrated from PHP to Laravel 8. It manages textile manufacturing orders, PDF documents, and production workflows.

## Common Commands

### Development
```bash
# Start development server
php artisan serve

# Install dependencies
composer install
npm install

# Frontend development
npm run dev           # Development build
npm run watch         # Watch for changes
npm run hot          # Hot module replacement
npm run prod         # Production build

# Database
php artisan migrate          # Run migrations
php artisan migrate:fresh    # Fresh database
php artisan db:seed         # Run seeders
```

### Testing & Debugging
```bash
# Run tests
php artisan test
./vendor/bin/phpunit

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Generate application key
php artisan key:generate
```

### Production Deployment
```bash
# Automatic deployment via GitHub Actions to main branch
git push origin main

# Manual deployment
./deploy.sh
```

## Architecture & Structure

### Core Models
- **Order** (`wp_wqorders_editable` table): Main order entity with complex JSON content parsing
- **OrderHandler, PaymentMethod, PrintFactory, SewingFactory**: Manufacturing process entities
- **User**: Authentication with custom user_id field

### Key Controllers
- **OrderController**: Order CRUD operations, inline editing, PDF/image management
- **PdfController**: PDF viewing, management, upload, and organization
- **OrderManagementController**: Production workflow management APIs

### Database Schema
- Uses existing WordPress-based table `wp_wqorders_editable` 
- Complex JSON content field parsing for customer data extraction
- Process tracking fields (dates for each workflow stage)
- Authentication via `users` table with roles (admin/employee)

### PDF Management System
- PDFs stored in `/public/aforms-pdf/` with folder-based organization:
  - `01-000/`: Orders 483-999
  - `01-001/`: Orders 1001-1313
  - `tmp/`: Temporary PDFs
- Supports multiple PDFs per order with automatic file discovery
- Order-based file naming with ID padding (5 digits)

### Frontend Architecture
- Laravel Breeze for authentication
- Blade templating with Tailwind CSS
- JavaScript modules in `/resources/js/`:
  - `order-management.js`: Order list functionality
  - `pdf-management.js`: PDF viewer and management
  - `ui-utilities.js`: Common UI helpers

### Route Structure
- **Web routes**: Dashboard, orders, PDF management (some routes temporarily unprotected)
- **Management routes**: Production workflow APIs (`/management/api/`)
- **PDF routes**: Viewing, uploading, management (`/pdf/`)
- **Auth routes**: Laravel Breeze authentication

## Environment Configuration

### Database Connection
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=factory0328_wp2
DB_USERNAME=factory0328_wp2
DB_PASSWORD=ctwjr3mmf5
```

### Production Environment
- **URL**: https://koutei.kiryu-factory.com
- **Server**: sv14052.xserver.jp (X-Server)
- **Deployment**: GitHub Actions automatic deployment

### Test Users
- **Admin**: admin / password
- **Employee**: employee / employee123

## Important Notes

### Order ID System
- Orders use formatted IDs with # prefix (e.g., #0001)
- Complex PDF file discovery logic handles ID mismatches
- Multiple search patterns for file location (4-digit, 5-digit padding)

### Content Parsing
The Order model contains extensive JSON content parsing methods for extracting:
- Customer information (name, email, phone, address)
- Company details
- Delivery dates
- Publication permissions
- Product categories (automatic detection from form titles)

### Security & Access Control
- PDFs protected from direct access via Laravel routing
- Authentication required for most functionality
- CSRF protection enabled
- Production environment has debug disabled

### File Management
- Image uploads in `/public/uploads/{order_id}/`
- PDF management with reordering, renaming capabilities
- Fallback mechanisms for file discovery

## Migration Status
This system was migrated from existing PHP codebase. Key legacy aspects:
- Maintains WordPress table structure compatibility
- Preserves existing PDF file organization
- Supports existing user authentication data
- Complex content field JSON structure for backward compatibility