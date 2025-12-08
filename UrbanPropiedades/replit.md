# UrbanGroup - Portal Inmobiliario

## Overview
Portal inmobiliario profesional para UrbanGroup, inspirado en propercial.cl. Permite la gestión de propiedades inmobiliarias en Chile con búsqueda por comunas, tipos de propiedad, y sistema de socios.

## Tech Stack
- **Frontend**: React + TypeScript + Vite
- **Backend**: Express.js + Node.js
- **Styling**: Tailwind CSS + Shadcn UI
- **State Management**: TanStack Query
- **Routing**: Wouter

## Project Structure
```
client/
├── src/
│   ├── components/     # Reusable UI components
│   │   ├── ui/         # Shadcn components
│   │   ├── Navbar.tsx  # Main navigation
│   │   ├── Footer.tsx  # Site footer
│   │   ├── PropertyCard.tsx  # Property listing card
│   │   └── SearchForm.tsx    # Property search form
│   ├── pages/          # Page components
│   │   ├── Home.tsx           # Landing page
│   │   ├── Properties.tsx     # Property listings
│   │   ├── PropertyDetail.tsx # Single property view
│   │   ├── About.tsx          # About Us page
│   │   ├── Login.tsx          # Partner login
│   │   ├── AdminDashboard.tsx # Admin panel
│   │   └── PartnerDashboard.tsx # Partner panel
│   ├── lib/            # Utilities
│   └── hooks/          # Custom hooks
server/
├── routes.ts           # API endpoints
├── storage.ts          # In-memory data storage
└── index.ts            # Server entry point
shared/
└── schema.ts           # Data models and types
```

## Features

### Public Features
- **Hero Section**: Full-width banner with property search
- **Property Search**: Filter by type, operation, region, comuna, price, m²
- **Property Listings**: Grid view with pagination
- **Property Detail**: Gallery, specifications, contact options
- **About Us**: Company information and team

### Partner Features
- **Partner Login**: Secure authentication
- **Partner Dashboard**: Manage own properties
- **Property CRUD**: Create, edit, delete properties

### Admin Features
- **Admin Dashboard**: Full control panel
- **Partner Management**: Add, edit, remove partners
- **Featured Properties**: Toggle property visibility
- **All Properties View**: Manage all listings

## API Endpoints

### Authentication
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `GET /api/auth/me` - Get current user

### Public
- `GET /api/properties` - List properties (with filters)
- `GET /api/properties/featured` - Featured properties
- `GET /api/properties/:id` - Single property

### Admin (requires admin role)
- `GET /api/admin/partners` - List partners
- `POST /api/admin/partners` - Create partner
- `PATCH /api/admin/partners/:id` - Update partner
- `DELETE /api/admin/partners/:id` - Delete partner
- `GET /api/admin/properties` - All properties
- `PATCH /api/admin/properties/:id/featured` - Toggle featured

### Partner (requires auth)
- `GET /api/partner/properties` - Own properties
- `POST /api/partner/properties` - Create property
- `PATCH /api/partner/properties/:id` - Update property
- `DELETE /api/partner/properties/:id` - Delete property

## Demo Credentials

### Admin
- Username: `admin`
- Password: `admin123`

### Partner
- Username: `socio1`
- Password: `socio123`

## Chilean Regions and Comunas
The system includes all Chilean regions with their respective comunas for property location selection.

## Property Types
- Casa, Departamento, Oficina, Local Comercial, Bodega, Terreno, Galpón, Estacionamiento

## Operation Types
- Venta, Arriendo

## Currencies
- UF, CLP, USD

## Running Locally
```bash
npm run dev
```
Server runs on port 5000.

## Recent Changes
- Initial implementation of complete real estate platform
- Public pages: Home, Properties, Property Detail, About
- Authentication system for partners and admins
- Partner dashboard for property management
- Admin dashboard for partner and property management
- Featured properties system
- Search and filter functionality by region/comuna
