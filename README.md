# Jewellery Test Report ERP System

A professional, self-contained jewelry certification card management system that enables printing of branded PVC certification cards.

## Features

✨ **Complete System**
- Auto-installable setup (install.php)
- Beautiful, responsive dashboard
- Card management (Create, Edit, Publish, Delete)
- Professional PVC card preview (CR80 format)
- QR Code generation for verification
- Print-ready cards
- PDF/PNG export
- Excel export of all cards
- Public verification page
- Mobile responsive

## Installation

### Step 1: Upload Files
Upload all files to your web server.

### Step 2: Run Installer
Visit `http://yoursite.com/install.php`

Click through the steps:
1. System requirements check
2. Create directories
3. Setup database
4. Create configuration
5. Done!

### Step 3: Start Using
Go to `http://yoursite.com/index.html` and start creating certification cards!

## File Structure

```
.
├── install.php              # Auto installer
├── index.html              # Main dashboard
├── verify.php              # Public verification page
├── config.php              # Configuration
├── README.md               # This file
└── api/
    ├── save-card.php       # Save card endpoint
    ├── publish-card.php    # Publish card endpoint
    ├── get-card.php        # Get single card
    ├── get-cards.php       # Get all cards
    ├── delete-card.php     # Delete card
    └── export-excel.php    # Export to CSV
└── data/                   # Database (auto-created)
└── uploads/                # Images (auto-created)
```

## Usage

### Creating a Card
1. Click "Add New Card" button
2. Fill in the form:
   - Job Number (auto-generated)
   - Certification Number (auto-generated)
   - Customer Name (required)
   - Product details (Type, Weight, Colour, Clarity)
   - Description and Comments
   - Upload product image
3. Click "Save Card"
4. Click "Publish Card" to make it public
5. Click "Preview" to see the card
6. Click "Print" to print

### Sharing with Customers
Each published card can be verified at:
```
http://yoursite.com/verify.php?cert_id=CERT-20260423-ABC12
```

## Database Schema

```sql
CREATE TABLE cards (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    job_number TEXT UNIQUE NOT NULL,
    cert_number TEXT UNIQUE NOT NULL,
    customer_name TEXT NOT NULL,
    description TEXT,
    product_type TEXT,
    weight TEXT,
    colour TEXT,
    clarity TEXT,
    comments TEXT,
    image_path TEXT,
    status TEXT DEFAULT 'draft',
    qr_code TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## Card Design

**PVC Card Specifications:**
- Size: CR80 (85.6mm × 54mm)
- Theme: Grey/White luxury
- Contains:
  - Brand name and logo
  - Certification number (top right)
  - Product specifications (left side)
  - Product image (bottom right)
  - QR Code (right side)
  - Customer information

## Security

⚠️ **IMPORTANT: After Installation**
1. Delete or rename `install.php`
2. Restrict database file access
3. Use HTTPS in production
4. Set proper file permissions

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

## Requirements

- PHP 7.4+
- SQLite3 or PDO SQLite
- Web server (Apache, Nginx, etc.)
- JavaScript enabled for UI

## Troubleshooting

**Database not created?**
- Check write permissions on directory
- Ensure PHP has sqlite3 extension

**Images not uploading?**
- Check `/uploads` directory permissions
- Verify file size limits

**Cards not saving?**
- Check browser console for errors
- Verify API endpoints are accessible
- Check database connection

## License

Open Source - Feel free to modify and distribute

## Support

For issues or questions, check:
1. Browser console (F12)
2. Server error logs
3. Database permissions

## Version

v1.0.0 - Initial Release
