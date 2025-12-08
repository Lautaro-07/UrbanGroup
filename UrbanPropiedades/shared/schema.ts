import { sql } from "drizzle-orm";
import { pgTable, text, varchar, integer, boolean, decimal, timestamp } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";

// Users table - for admin and partners
export const users = pgTable("users", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  username: text("username").notNull().unique(),
  password: text("password").notNull(),
  name: text("name").notNull(),
  email: text("email").notNull(),
  phone: text("phone"),
  role: text("role").notNull().default("partner"), // admin or partner
  isActive: boolean("is_active").notNull().default(true),
  createdAt: timestamp("created_at").defaultNow(),
});

export const insertUserSchema = createInsertSchema(users).omit({
  id: true,
  createdAt: true,
});

export type InsertUser = z.infer<typeof insertUserSchema>;
export type User = typeof users.$inferSelect;

// Properties table
export const properties = pgTable("properties", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  title: text("title").notNull(),
  description: text("description").notNull(),
  propertyType: text("property_type").notNull(), // casa, departamento, oficina, local, bodega, terreno
  operationType: text("operation_type").notNull(), // venta, arriendo
  price: decimal("price", { precision: 15, scale: 2 }).notNull(),
  currency: text("currency").notNull().default("UF"), // UF, CLP, USD
  region: text("region").notNull(),
  comuna: text("comuna").notNull(),
  address: text("address").notNull(),
  squareMeters: integer("square_meters").notNull(),
  bedrooms: integer("bedrooms"),
  bathrooms: integer("bathrooms"),
  parkingSpots: integer("parking_spots"),
  images: text("images").array().notNull().default(sql`ARRAY[]::text[]`),
  isFeatured: boolean("is_featured").notNull().default(false),
  isActive: boolean("is_active").notNull().default(true),
  partnerId: varchar("partner_id").notNull(),
  createdAt: timestamp("created_at").defaultNow(),
});

export const insertPropertySchema = createInsertSchema(properties).omit({
  id: true,
  createdAt: true,
});

export type InsertProperty = z.infer<typeof insertPropertySchema>;
export type Property = typeof properties.$inferSelect;

// Company Info table - for About Us section
export const companyInfo = pgTable("company_info", {
  id: varchar("id").primaryKey().default(sql`gen_random_uuid()`),
  title: text("title").notNull(),
  description: text("description").notNull(),
  mission: text("mission"),
  vision: text("vision"),
  yearsExperience: integer("years_experience").default(15),
  phone: text("phone"),
  email: text("email"),
  address: text("address"),
  facebook: text("facebook"),
  instagram: text("instagram"),
  linkedin: text("linkedin"),
  whatsapp: text("whatsapp"),
});

export const insertCompanyInfoSchema = createInsertSchema(companyInfo).omit({
  id: true,
});

export type InsertCompanyInfo = z.infer<typeof insertCompanyInfoSchema>;
export type CompanyInfo = typeof companyInfo.$inferSelect;

// Chilean regions and comunas
export const chileanRegions = [
  "Región de Arica y Parinacota",
  "Región de Tarapacá",
  "Región de Antofagasta",
  "Región de Atacama",
  "Región de Coquimbo",
  "Región de Valparaíso",
  "Región Metropolitana de Santiago",
  "Región del Libertador General Bernardo O'Higgins",
  "Región del Maule",
  "Región de Ñuble",
  "Región del Biobío",
  "Región de La Araucanía",
  "Región de Los Ríos",
  "Región de Los Lagos",
  "Región de Aysén",
  "Región de Magallanes"
] as const;

export const comunasByRegion: Record<string, string[]> = {
  "Región Metropolitana de Santiago": [
    "Santiago", "Providencia", "Las Condes", "Vitacura", "Lo Barnechea",
    "Ñuñoa", "La Reina", "Peñalolén", "Macul", "San Joaquín", "La Granja",
    "La Florida", "Puente Alto", "San Miguel", "La Cisterna", "El Bosque",
    "Pedro Aguirre Cerda", "Lo Espejo", "Estación Central", "Cerrillos",
    "Maipú", "Quilicura", "Pudahuel", "Cerro Navia", "Renca", "Independencia",
    "Conchalí", "Huechuraba", "Recoleta", "San Bernardo", "Buin", "Paine",
    "Calera de Tango", "Talagante", "Peñaflor", "El Monte", "Isla de Maipo",
    "Padre Hurtado", "Melipilla", "San Pedro", "Alhué", "Curacaví", "María Pinto",
    "Colina", "Lampa", "Til Til"
  ],
  "Región de Valparaíso": [
    "Valparaíso", "Viña del Mar", "Concón", "Quilpué", "Villa Alemana",
    "Quillota", "La Calera", "Limache", "San Antonio", "Cartagena",
    "El Quisco", "Algarrobo", "El Tabo", "Santo Domingo", "Los Andes",
    "San Felipe", "Putaendo", "Catemu", "Llay-Llay", "Panquehue"
  ],
  "Región del Biobío": [
    "Concepción", "Talcahuano", "Hualpén", "San Pedro de la Paz", "Chiguayante",
    "Coronel", "Lota", "Tomé", "Penco", "Los Ángeles", "Mulchén", "Nacimiento",
    "Negrete", "Quilaco", "Quilleco", "San Rosendo", "Santa Bárbara", "Tucapel",
    "Yumbel", "Alto Biobío", "Antuco", "Cabrero", "Laja"
  ],
  "Región de Coquimbo": [
    "La Serena", "Coquimbo", "Ovalle", "Illapel", "Los Vilos", "Salamanca",
    "Vicuña", "Paihuano", "Andacollo", "La Higuera", "Combarbalá", "Monte Patria",
    "Punitaqui", "Río Hurtado", "Canela"
  ],
  "Región de Los Lagos": [
    "Puerto Montt", "Puerto Varas", "Osorno", "Castro", "Ancud", "Quellón",
    "Frutillar", "Llanquihue", "Calbuco", "Maullín", "Los Muermos", "Cochamó",
    "Chonchi", "Curaco de Vélez", "Dalcahue", "Puqueldón", "Queilén", "Quemchi"
  ],
  "Región de Los Ríos": [
    "Valdivia", "La Unión", "Río Bueno", "Panguipulli", "Lanco", "Mariquina",
    "Máfil", "Los Lagos", "Futrono", "Lago Ranco", "Corral", "Paillaco"
  ],
  "Región de La Araucanía": [
    "Temuco", "Padre Las Casas", "Villarrica", "Pucón", "Angol", "Victoria",
    "Lautaro", "Nueva Imperial", "Pitrufquén", "Freire", "Cunco", "Curarrehue",
    "Gorbea", "Loncoche", "Toltén", "Teodoro Schmidt"
  ],
  "Región de Arica y Parinacota": ["Arica", "Camarones", "General Lagos", "Putre"],
  "Región de Tarapacá": ["Iquique", "Alto Hospicio", "Pozo Almonte", "Pica", "Huara", "Camiña", "Colchane"],
  "Región de Antofagasta": ["Antofagasta", "Mejillones", "Sierra Gorda", "Taltal", "Calama", "Ollagüe", "San Pedro de Atacama", "Tocopilla", "María Elena"],
  "Región de Atacama": ["Copiapó", "Caldera", "Tierra Amarilla", "Chañaral", "Diego de Almagro", "Vallenar", "Alto del Carmen", "Freirina", "Huasco"],
  "Región del Libertador General Bernardo O'Higgins": ["Rancagua", "Machalí", "Graneros", "San Fernando", "Santa Cruz", "Pichilemu", "Rengo", "Requínoa", "San Vicente de Tagua Tagua"],
  "Región del Maule": ["Talca", "Curicó", "Linares", "Cauquenes", "Constitución", "Molina", "San Clemente", "Maule", "Pelarco"],
  "Región de Ñuble": ["Chillán", "Chillán Viejo", "San Carlos", "Bulnes", "Coihueco", "Quillón", "El Carmen", "Pemuco", "Pinto", "Yungay"],
  "Región de Aysén": ["Coyhaique", "Puerto Aysén", "Chile Chico", "Cochrane", "Puerto Cisnes", "Puerto Ibáñez", "Río Ibáñez", "O'Higgins", "Tortel", "Guaitecas"],
  "Región de Magallanes": ["Punta Arenas", "Puerto Natales", "Porvenir", "Puerto Williams", "Primavera", "Timaukel", "Cabo de Hornos", "Laguna Blanca", "Río Verde", "San Gregorio", "Torres del Paine", "Antártica"]
};

export const propertyTypes = [
  { value: "casa", label: "Casa" },
  { value: "departamento", label: "Departamento" },
  { value: "oficina", label: "Oficina" },
  { value: "local", label: "Local Comercial" },
  { value: "bodega", label: "Bodega" },
  { value: "terreno", label: "Terreno" },
  { value: "galpon", label: "Galpón" },
  { value: "estacionamiento", label: "Estacionamiento" }
] as const;

export const operationTypes = [
  { value: "venta", label: "Venta" },
  { value: "arriendo", label: "Arriendo" }
] as const;

export const currencies = [
  { value: "UF", label: "UF" },
  { value: "CLP", label: "CLP ($)" },
  { value: "USD", label: "USD" }
] as const;
