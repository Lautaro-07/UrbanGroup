# UrbanPropiedades - Portal Inmobiliario

## Overview
UrbanPropiedades is a professional real estate portal built with PHP, HTML, CSS, and JavaScript. It features property listings, search functionality by Chilean regions/comunas, partner dashboards for property management, and an admin panel.

## Technology Stack
- **Backend**: PHP 8.3
- **Database**: SQLite (built-in with PHP)
- **Frontend**: HTML5, CSS3 (custom styles), JavaScript
- **Server**: PHP built-in development server

## Project Structure
```
php-app/
├── config/
│   ├── config.php          # Application configuration
│   └── database.php        # Database connection and schema
├── includes/
│   ├── helpers.php         # Utility functions
│   ├── PropertyModel.php   # Property CRUD operations
│   ├── UserModel.php       # User authentication and management
│   └── LocationModel.php   # Regions and comunas
├── templates/
│   ├── header.php          # Common header/navbar
│   ├── footer.php          # Common footer
│   └── property-card.php   # Property card component
├── public/
│   ├── index.php           # Home page
│   ├── propiedades.php     # Property listing page
│   ├── propiedad.php       # Property detail page
│   ├── nosotros.php        # About page
│   ├── login.php           # Login page
│   ├── logout.php          # Logout handler
│   ├── admin/index.php     # Admin dashboard
│   └── partner/index.php   # Partner dashboard
├── api/
│   ├── contact.php         # Contact form handler
│   └── comunas.php         # Comunas API endpoint
├── assets/
│   ├── css/style.css       # Main stylesheet
│   └── js/main.js          # JavaScript utilities
├── data/                   # SQLite database (auto-created)
└── router.php              # URL routing
```

## Demo Credentials
- **Admin**: username: `admin`, password: `admin123`
- **Partner**: username: `socio1`, password: `socio123`

## Features
1. **Home Page**: Hero section with search form, featured properties
2. **Properties**: Browse and filter properties by type, region, bedrooms
3. **Property Detail**: Full property information with image gallery
4. **About**: Company information and values
5. **Login**: Session-based authentication
6. **Admin Dashboard**: Manage properties and partners
7. **Partner Dashboard**: Manage own properties

## Chilean Data
- All 16 regions of Chile included
- Major comunas for each region
- Property types: Casa, Departamento, Oficina, Local Comercial, Bodega, Terreno, Galpón, Estacionamiento

## Running the Application
The application runs on PHP's built-in server on port 5000:
```bash
cd php-app && php -S 0.0.0.0:5000 router.php
```

## Recent Changes
- Migrated from Node.js/React/TypeScript to PHP/HTML/CSS/JavaScript
- Created SQLite database with Chilean regions and comunas
- Built responsive design matching original Tailwind-based styling
- Implemented session-based authentication
- Created admin and partner dashboards with full CRUD operations
