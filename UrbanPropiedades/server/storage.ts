import {
  type User,
  type InsertUser,
  type Property,
  type InsertProperty,
  type CompanyInfo,
  type InsertCompanyInfo,
} from "@shared/schema";
import { randomUUID } from "crypto";

export interface IStorage {
  // Users
  getUser(id: string): Promise<User | undefined>;
  getUserByUsername(username: string): Promise<User | undefined>;
  createUser(user: InsertUser): Promise<User>;
  updateUser(id: string, user: Partial<InsertUser>): Promise<User | undefined>;
  deleteUser(id: string): Promise<boolean>;
  getAllPartners(): Promise<User[]>;

  // Properties
  getProperty(id: string): Promise<Property | undefined>;
  getAllProperties(): Promise<Property[]>;
  getFeaturedProperties(): Promise<Property[]>;
  getPropertiesByPartner(partnerId: string): Promise<Property[]>;
  createProperty(property: InsertProperty): Promise<Property>;
  updateProperty(id: string, property: Partial<InsertProperty>): Promise<Property | undefined>;
  deleteProperty(id: string): Promise<boolean>;
  searchProperties(filters: PropertyFilters): Promise<Property[]>;

  // Company Info
  getCompanyInfo(): Promise<CompanyInfo | undefined>;
  updateCompanyInfo(info: Partial<InsertCompanyInfo>): Promise<CompanyInfo>;
}

export interface PropertyFilters {
  tipo?: string;
  operacion?: string;
  region?: string;
  comuna?: string;
  minPrice?: number;
  maxPrice?: number;
  minM2?: number;
  maxM2?: number;
}

export class MemStorage implements IStorage {
  private users: Map<string, User>;
  private properties: Map<string, Property>;
  private companyInfo: CompanyInfo | undefined;

  constructor() {
    this.users = new Map();
    this.properties = new Map();

    // Initialize with default admin and demo partner
    this.initializeDefaultData();
  }

  private initializeDefaultData() {
    // Create admin user
    const adminId = randomUUID();
    const admin: User = {
      id: adminId,
      username: "admin",
      password: "admin123",
      name: "Administrador",
      email: "admin@urbangroup.cl",
      phone: "+56 9 1234 5678",
      role: "admin",
      isActive: true,
      createdAt: new Date(),
    };
    this.users.set(adminId, admin);

    // Create demo partner
    const partnerId = randomUUID();
    const partner: User = {
      id: partnerId,
      username: "socio1",
      password: "socio123",
      name: "Juan Pérez",
      email: "juan@ejemplo.com",
      phone: "+56 9 8765 4321",
      role: "partner",
      isActive: true,
      createdAt: new Date(),
    };
    this.users.set(partnerId, partner);

    // Create demo properties
    const demoProperties: Omit<Property, "id" | "createdAt">[] = [
      {
        title: "Hermoso Departamento en Las Condes",
        description: "Amplio departamento de 3 dormitorios con vista panorámica. Ubicado en excelente sector de Las Condes, cerca de colegios, comercio y transporte público. Cuenta con estacionamiento y bodega.",
        propertyType: "departamento",
        operationType: "venta",
        price: "8500",
        currency: "UF",
        region: "Región Metropolitana de Santiago",
        comuna: "Las Condes",
        address: "Av. Apoquindo 5000",
        squareMeters: 120,
        bedrooms: 3,
        bathrooms: 2,
        parkingSpots: 2,
        images: [
          "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&h=600&fit=crop",
          "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop",
          "https://images.unsplash.com/photo-1560185893-a55cbc8c57e8?w=800&h=600&fit=crop",
        ],
        isFeatured: true,
        isActive: true,
        partnerId: partnerId,
      },
      {
        title: "Casa Familiar en Providencia",
        description: "Preciosa casa de 2 pisos en tranquilo barrio de Providencia. Jardín amplio, 4 dormitorios, living comedor, cocina equipada. Ideal para familias.",
        propertyType: "casa",
        operationType: "venta",
        price: "15000",
        currency: "UF",
        region: "Región Metropolitana de Santiago",
        comuna: "Providencia",
        address: "Av. Providencia 1234",
        squareMeters: 180,
        bedrooms: 4,
        bathrooms: 3,
        parkingSpots: 2,
        images: [
          "https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&h=600&fit=crop",
          "https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop",
        ],
        isFeatured: true,
        isActive: true,
        partnerId: partnerId,
      },
      {
        title: "Oficina Moderna en Santiago Centro",
        description: "Oficina completamente equipada en edificio de categoría. Recepción, salas de reunión, espacios de trabajo abiertos. Excelente conectividad.",
        propertyType: "oficina",
        operationType: "arriendo",
        price: "45",
        currency: "UF",
        region: "Región Metropolitana de Santiago",
        comuna: "Santiago",
        address: "Alameda 1200",
        squareMeters: 85,
        bedrooms: null,
        bathrooms: 2,
        parkingSpots: 1,
        images: [
          "https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop",
          "https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=800&h=600&fit=crop",
        ],
        isFeatured: true,
        isActive: true,
        partnerId: partnerId,
      },
      {
        title: "Local Comercial en Viña del Mar",
        description: "Local comercial en excelente ubicación de Viña del Mar. Gran vitrina, alto flujo peatonal. Ideal para retail o servicios.",
        propertyType: "local",
        operationType: "arriendo",
        price: "60",
        currency: "UF",
        region: "Región de Valparaíso",
        comuna: "Viña del Mar",
        address: "Av. Valparaíso 500",
        squareMeters: 65,
        bedrooms: null,
        bathrooms: 1,
        parkingSpots: 0,
        images: [
          "https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=800&h=600&fit=crop",
        ],
        isFeatured: true,
        isActive: true,
        partnerId: partnerId,
      },
      {
        title: "Departamento Amoblado en Ñuñoa",
        description: "Moderno departamento amoblado, listo para habitar. Ubicado cerca de metro, ideal para profesionales jóvenes o estudiantes.",
        propertyType: "departamento",
        operationType: "arriendo",
        price: "25",
        currency: "UF",
        region: "Región Metropolitana de Santiago",
        comuna: "Ñuñoa",
        address: "Av. Irarrázaval 3000",
        squareMeters: 55,
        bedrooms: 1,
        bathrooms: 1,
        parkingSpots: 1,
        images: [
          "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop",
          "https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=800&h=600&fit=crop",
        ],
        isFeatured: true,
        isActive: true,
        partnerId: partnerId,
      },
      {
        title: "Bodega Industrial en Quilicura",
        description: "Amplia bodega industrial con acceso para camiones. Altura de cielo 8m, oficinas administrativas incluidas. Excelente conectividad vial.",
        propertyType: "bodega",
        operationType: "arriendo",
        price: "80",
        currency: "UF",
        region: "Región Metropolitana de Santiago",
        comuna: "Quilicura",
        address: "Panamericana Norte 5000",
        squareMeters: 500,
        bedrooms: null,
        bathrooms: 2,
        parkingSpots: 5,
        images: [
          "https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop",
        ],
        isFeatured: false,
        isActive: true,
        partnerId: partnerId,
      },
      {
        title: "Terreno en Colina",
        description: "Terreno de 5000m² en zona de desarrollo. Uso mixto, ideal para proyecto inmobiliario. Servicios básicos en el límite del terreno.",
        propertyType: "terreno",
        operationType: "venta",
        price: "25000",
        currency: "UF",
        region: "Región Metropolitana de Santiago",
        comuna: "Colina",
        address: "Camino a Chicureo km 5",
        squareMeters: 5000,
        bedrooms: null,
        bathrooms: null,
        parkingSpots: null,
        images: [
          "https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&h=600&fit=crop",
        ],
        isFeatured: true,
        isActive: true,
        partnerId: partnerId,
      },
      {
        title: "Casa en Valdivia con Vista al Río",
        description: "Hermosa casa con vista al río Calle-Calle. Arquitectura sureña, 3 dormitorios, calefacción central. Jardín y quincho.",
        propertyType: "casa",
        operationType: "venta",
        price: "6500",
        currency: "UF",
        region: "Región de Los Ríos",
        comuna: "Valdivia",
        address: "Costanera 800",
        squareMeters: 150,
        bedrooms: 3,
        bathrooms: 2,
        parkingSpots: 2,
        images: [
          "https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&h=600&fit=crop",
          "https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=600&fit=crop",
        ],
        isFeatured: true,
        isActive: true,
        partnerId: partnerId,
      },
    ];

    demoProperties.forEach((prop) => {
      const id = randomUUID();
      this.properties.set(id, {
        ...prop,
        id,
        createdAt: new Date(),
      });
    });

    // Initialize company info
    this.companyInfo = {
      id: randomUUID(),
      title: "Urban Group",
      description: "Urban Group es un equipo multidisciplinario formado por Arquitectos, Abogados y una extensa Red de Corredores de Propiedades con años de experiencia en el mercado. Con más de 15 años en el mercado, hemos transformado el corretaje de propiedades en un servicio profesional, logrando el éxito en cada compraventa inmobiliaria.",
      mission: "Brindar un servicio inmobiliario integral y profesional, enfocado en satisfacer las necesidades de nuestros clientes.",
      vision: "Ser reconocidos como el portal inmobiliario líder en Chile.",
      yearsExperience: 15,
      phone: "+56 9 1234 5678",
      email: "contacto@urbangroup.cl",
      address: "Av. Apoquindo 4700, Of. 1802, Las Condes, Santiago",
      facebook: "https://facebook.com/urbangroup",
      instagram: "https://instagram.com/urbangroup",
      linkedin: "https://linkedin.com/company/urbangroup",
      whatsapp: "+56912345678",
    };
  }

  // User methods
  async getUser(id: string): Promise<User | undefined> {
    return this.users.get(id);
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    return Array.from(this.users.values()).find(
      (user) => user.username === username
    );
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const id = randomUUID();
    const user: User = {
      ...insertUser,
      id,
      createdAt: new Date(),
    };
    this.users.set(id, user);
    return user;
  }

  async updateUser(
    id: string,
    updates: Partial<InsertUser>
  ): Promise<User | undefined> {
    const user = this.users.get(id);
    if (!user) return undefined;
    const updated = { ...user, ...updates };
    this.users.set(id, updated);
    return updated;
  }

  async deleteUser(id: string): Promise<boolean> {
    return this.users.delete(id);
  }

  async getAllPartners(): Promise<User[]> {
    return Array.from(this.users.values()).filter(
      (user) => user.role === "partner"
    );
  }

  // Property methods
  async getProperty(id: string): Promise<Property | undefined> {
    return this.properties.get(id);
  }

  async getAllProperties(): Promise<Property[]> {
    return Array.from(this.properties.values()).filter((p) => p.isActive);
  }

  async getFeaturedProperties(): Promise<Property[]> {
    return Array.from(this.properties.values()).filter(
      (p) => p.isFeatured && p.isActive
    );
  }

  async getPropertiesByPartner(partnerId: string): Promise<Property[]> {
    return Array.from(this.properties.values()).filter(
      (p) => p.partnerId === partnerId
    );
  }

  async createProperty(insertProperty: InsertProperty): Promise<Property> {
    const id = randomUUID();
    const property: Property = {
      ...insertProperty,
      id,
      createdAt: new Date(),
    };
    this.properties.set(id, property);
    return property;
  }

  async updateProperty(
    id: string,
    updates: Partial<InsertProperty>
  ): Promise<Property | undefined> {
    const property = this.properties.get(id);
    if (!property) return undefined;
    const updated = { ...property, ...updates };
    this.properties.set(id, updated);
    return updated;
  }

  async deleteProperty(id: string): Promise<boolean> {
    return this.properties.delete(id);
  }

  async searchProperties(filters: PropertyFilters): Promise<Property[]> {
    let results = Array.from(this.properties.values()).filter((p) => p.isActive);

    if (filters.tipo && filters.tipo !== "all") {
      results = results.filter((p) => p.propertyType === filters.tipo);
    }
    if (filters.operacion && filters.operacion !== "all") {
      results = results.filter((p) => p.operationType === filters.operacion);
    }
    if (filters.region && filters.region !== "all") {
      results = results.filter((p) => p.region === filters.region);
    }
    if (filters.comuna && filters.comuna !== "all") {
      results = results.filter((p) => p.comuna === filters.comuna);
    }
    if (filters.minPrice) {
      results = results.filter(
        (p) => parseFloat(p.price) >= filters.minPrice!
      );
    }
    if (filters.maxPrice) {
      results = results.filter(
        (p) => parseFloat(p.price) <= filters.maxPrice!
      );
    }
    if (filters.minM2) {
      results = results.filter((p) => p.squareMeters >= filters.minM2!);
    }
    if (filters.maxM2) {
      results = results.filter((p) => p.squareMeters <= filters.maxM2!);
    }

    return results;
  }

  // Company Info methods
  async getCompanyInfo(): Promise<CompanyInfo | undefined> {
    return this.companyInfo;
  }

  async updateCompanyInfo(
    updates: Partial<InsertCompanyInfo>
  ): Promise<CompanyInfo> {
    if (!this.companyInfo) {
      const id = randomUUID();
      this.companyInfo = { id, ...updates } as CompanyInfo;
    } else {
      this.companyInfo = { ...this.companyInfo, ...updates };
    }
    return this.companyInfo;
  }
}

export const storage = new MemStorage();
