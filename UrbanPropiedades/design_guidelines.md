# Design Guidelines: UrbanGroup Real Estate Platform

## Design Approach

**Reference-Based Design**: This platform will closely mirror propercial.cl's proven real estate marketplace design, adapting it for UrbanGroup's brand identity. The design prioritizes property showcase, intuitive search, and professional credibility.

**Key Design Principles**:
- Clean, professional aesthetic that builds trust
- Image-forward property presentation
- Efficient search and filtering experience
- Clear information hierarchy
- Minimal visual noise to keep focus on properties

## Typography

**Font System** (via Google Fonts CDN):
- **Primary**: Inter (400, 500, 600, 700)
- **Secondary**: Roboto (400, 500) for data/numbers

**Type Scale**:
- Hero headline: text-4xl lg:text-5xl font-bold
- Section titles: text-3xl lg:text-4xl font-bold
- Property titles: text-xl font-semibold
- Body text: text-base font-normal
- Captions/meta: text-sm font-medium
- Search labels: text-sm font-medium

## Layout System

**Spacing Primitives**: Use Tailwind units of 2, 4, 6, 8, 12, 16, 20, 24 for consistent rhythm
- Component padding: p-4 to p-6
- Section spacing: py-12 to py-20
- Card gaps: gap-4 to gap-6
- Element margins: mb-2, mb-4, mb-8

**Container Strategy**:
- Max width: max-w-7xl mx-auto
- Page padding: px-4 lg:px-8
- Content areas: max-w-6xl

**Grid Systems**:
- Property cards: grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4
- Featured properties: grid-cols-1 md:grid-cols-2 lg:grid-cols-3
- Search filters: flex flex-wrap gap-4

## Component Library

### Navigation
- Sticky header with logo left, navigation center, CTA button right
- Mobile: Hamburger menu with slide-out navigation
- Transparent overlay on hero, solid white on scroll
- Height: h-20

### Hero Section
- Full-width hero banner with large property image
- Overlay with semi-transparent dark gradient
- Centered search form with white/light background card
- Search form includes: Property Type dropdown, Operation dropdown (Venta/Arriendo), Location/Comuna autocomplete, Search button
- Height: h-[500px] lg:h-[600px]

### Search & Filters
- Horizontal filter bar below hero
- Dropdowns styled with rounded corners, shadows
- Filter chips for active filters (removable)
- "Búsqueda avanzada" expandable section with additional filters (price range, m², bedrooms, bathrooms)

### Property Cards
- Aspect ratio 4:3 image with rounded corners
- Image carousel indicator dots
- Price badge overlay (top-left, semi-transparent background)
- Property info: Title, location, operation type, m², bedrooms, bathrooms icons
- Card shadow: shadow-sm hover:shadow-lg transition
- Border: border border-gray-200
- Padding: p-4

### Featured Properties Section
- Section title "Propiedades Destacadas" with underline accent
- 3-column grid on desktop
- "Ver más" button at bottom
- Background: subtle gray (bg-gray-50)

### About Us Section
- 2-column layout: Text content left, image/map right
- Title with accent line
- Paragraph text with comfortable line-height
- Stats/highlights in badge format
- Background: white

### Footer
- Dark background (not specified - avoid color references, but structured in 4 columns)
- Logo and description column
- Quick links column
- Contact information column
- Social media icons column
- Bottom bar with copyright and legal links
- Spacing: py-12 px-8

### Partner/Admin Dashboard
- Sidebar navigation (fixed left)
- Main content area (flex-1)
- Data tables with alternating row backgrounds
- Form cards with clear section divisions
- Action buttons grouped in button bars

### Property Detail Page
- Large image gallery (main image + thumbnails)
- Two-column layout: Details left (70%), contact form right (30%)
- Specifications in grid format with icons
- Map integration section
- Related properties carousel at bottom

### Forms
- Input fields with clear labels above
- Rounded corners: rounded-lg
- Border styling: border-2 focus states
- File upload for multiple images with preview
- Dropdowns for selects (Comuna, Property Type, Operation)
- Price input with UF/$ toggle
- Textarea for descriptions
- Submit buttons: Primary style, full-width on mobile

### Buttons
- Primary: Rounded, medium padding (px-6 py-3), font-medium
- Secondary: Outlined variant
- Icon buttons: Square with icon centered
- CTA buttons: Slightly larger (px-8 py-4)

### Icons
Use Heroicons via CDN for:
- Search, filter, location, bed, bath, square footage
- Navigation arrows, close, menu
- Social media icons
- Upload, edit, delete for admin

## Images

**Hero Section**: 
- Large, high-quality commercial property image (modern office building or urban development)
- Dimensions: 1920x600px minimum
- Professional photography with good lighting
- Subtle dark overlay for text readability

**Property Listings**:
- Multiple images per property (4-10 images)
- Primary image: 800x600px minimum
- Gallery format with main image and thumbnail strip
- Professional real estate photography

**About Us Section**:
- Company team photo or modern office space
- Infographic elements showing stats/experience
- Dimensions: 600x400px

**Logo**:
- UrbanGroup logo in header (transparent background)
- Footer variant in light version
- Dimensions: 200x60px

**Property Type Icons**:
- Use icon library (Heroicons) for property categories
- Casa, Departamento, Oficina, Local, Bodega, Terreno

**No Animations**: Keep interactions instant and clean without distracting animations. Focus on immediate feedback and clarity.