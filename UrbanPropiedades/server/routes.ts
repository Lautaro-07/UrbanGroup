import type { Express, Request, Response, NextFunction } from "express";
import { createServer, type Server } from "http";
import session from "express-session";
import { storage } from "./storage";
import { insertUserSchema, insertPropertySchema } from "@shared/schema";
import { z } from "zod";

// Extend Express Request to include session user
declare module "express-session" {
  interface SessionData {
    userId?: string;
    userRole?: string;
  }
}

// Middleware to check if user is authenticated
const requireAuth = (req: Request, res: Response, next: NextFunction) => {
  if (!req.session.userId) {
    return res.status(401).json({ message: "No autorizado" });
  }
  next();
};

// Middleware to check if user is admin
const requireAdmin = (req: Request, res: Response, next: NextFunction) => {
  if (!req.session.userId || req.session.userRole !== "admin") {
    return res.status(403).json({ message: "Acceso denegado" });
  }
  next();
};

export async function registerRoutes(
  httpServer: Server,
  app: Express
): Promise<Server> {
  // Configure session
  app.use(
    session({
      secret: process.env.SESSION_SECRET || "urbangroup-secret-key-2024",
      resave: false,
      saveUninitialized: false,
      cookie: {
        secure: false,
        httpOnly: true,
        maxAge: 24 * 60 * 60 * 1000, // 24 hours
      },
    })
  );

  // ==================== AUTH ROUTES ====================

  // Login
  app.post("/api/auth/login", async (req, res) => {
    try {
      const { username, password } = req.body;
      
      if (!username || !password) {
        return res.status(400).json({ message: "Usuario y contraseña requeridos" });
      }

      const user = await storage.getUserByUsername(username);
      
      if (!user || user.password !== password) {
        return res.status(401).json({ message: "Usuario o contraseña incorrectos" });
      }

      if (!user.isActive) {
        return res.status(401).json({ message: "Usuario desactivado" });
      }

      req.session.userId = user.id;
      req.session.userRole = user.role;

      const { password: _, ...userWithoutPassword } = user;
      res.json({ user: userWithoutPassword });
    } catch (error) {
      console.error("Login error:", error);
      res.status(500).json({ message: "Error en el servidor" });
    }
  });

  // Logout
  app.post("/api/auth/logout", (req, res) => {
    req.session.destroy((err) => {
      if (err) {
        return res.status(500).json({ message: "Error al cerrar sesión" });
      }
      res.json({ message: "Sesión cerrada" });
    });
  });

  // Get current user
  app.get("/api/auth/me", requireAuth, async (req, res) => {
    try {
      const user = await storage.getUser(req.session.userId!);
      if (!user) {
        return res.status(404).json({ message: "Usuario no encontrado" });
      }
      const { password: _, ...userWithoutPassword } = user;
      res.json(userWithoutPassword);
    } catch (error) {
      res.status(500).json({ message: "Error en el servidor" });
    }
  });

  // ==================== PUBLIC PROPERTY ROUTES ====================

  // Get all properties with filters
  app.get("/api/properties", async (req, res) => {
    try {
      const filters = {
        tipo: req.query.tipo as string | undefined,
        operacion: req.query.operacion as string | undefined,
        region: req.query.region as string | undefined,
        comuna: req.query.comuna as string | undefined,
        minPrice: req.query.minPrice ? parseFloat(req.query.minPrice as string) : undefined,
        maxPrice: req.query.maxPrice ? parseFloat(req.query.maxPrice as string) : undefined,
        minM2: req.query.minM2 ? parseInt(req.query.minM2 as string) : undefined,
        maxM2: req.query.maxM2 ? parseInt(req.query.maxM2 as string) : undefined,
      };

      const hasFilters = Object.values(filters).some(v => v !== undefined);
      
      const properties = hasFilters 
        ? await storage.searchProperties(filters)
        : await storage.getAllProperties();
      
      res.json(properties);
    } catch (error) {
      console.error("Error fetching properties:", error);
      res.status(500).json({ message: "Error al obtener propiedades" });
    }
  });

  // Get featured properties
  app.get("/api/properties/featured", async (req, res) => {
    try {
      const properties = await storage.getFeaturedProperties();
      res.json(properties);
    } catch (error) {
      res.status(500).json({ message: "Error al obtener propiedades destacadas" });
    }
  });

  // Get single property
  app.get("/api/properties/:id", async (req, res) => {
    try {
      const property = await storage.getProperty(req.params.id);
      if (!property) {
        return res.status(404).json({ message: "Propiedad no encontrada" });
      }
      res.json(property);
    } catch (error) {
      res.status(500).json({ message: "Error al obtener la propiedad" });
    }
  });

  // ==================== ADMIN ROUTES ====================

  // Get all partners
  app.get("/api/admin/partners", requireAdmin, async (req, res) => {
    try {
      const partners = await storage.getAllPartners();
      const partnersWithoutPassword = partners.map(({ password, ...rest }) => rest);
      res.json(partnersWithoutPassword);
    } catch (error) {
      res.status(500).json({ message: "Error al obtener socios" });
    }
  });

  // Create partner
  app.post("/api/admin/partners", requireAdmin, async (req, res) => {
    try {
      const data = { ...req.body, role: "partner", isActive: true };
      const validated = insertUserSchema.parse(data);
      
      const existing = await storage.getUserByUsername(validated.username);
      if (existing) {
        return res.status(400).json({ message: "El nombre de usuario ya existe" });
      }

      const partner = await storage.createUser(validated);
      const { password, ...partnerWithoutPassword } = partner;
      res.status(201).json(partnerWithoutPassword);
    } catch (error) {
      if (error instanceof z.ZodError) {
        return res.status(400).json({ message: error.errors[0].message });
      }
      res.status(500).json({ message: "Error al crear socio" });
    }
  });

  // Update partner
  app.patch("/api/admin/partners/:id", requireAdmin, async (req, res) => {
    try {
      const updates = { ...req.body };
      if (updates.password === "") delete updates.password;
      
      const partner = await storage.updateUser(req.params.id, updates);
      if (!partner) {
        return res.status(404).json({ message: "Socio no encontrado" });
      }
      const { password, ...partnerWithoutPassword } = partner;
      res.json(partnerWithoutPassword);
    } catch (error) {
      res.status(500).json({ message: "Error al actualizar socio" });
    }
  });

  // Delete partner
  app.delete("/api/admin/partners/:id", requireAdmin, async (req, res) => {
    try {
      const deleted = await storage.deleteUser(req.params.id);
      if (!deleted) {
        return res.status(404).json({ message: "Socio no encontrado" });
      }
      res.json({ message: "Socio eliminado" });
    } catch (error) {
      res.status(500).json({ message: "Error al eliminar socio" });
    }
  });

  // Get all properties (admin)
  app.get("/api/admin/properties", requireAdmin, async (req, res) => {
    try {
      const allProperties = await storage.getAllProperties();
      res.json(allProperties);
    } catch (error) {
      res.status(500).json({ message: "Error al obtener propiedades" });
    }
  });

  // Toggle featured property
  app.patch("/api/admin/properties/:id/featured", requireAdmin, async (req, res) => {
    try {
      const { isFeatured } = req.body;
      const property = await storage.updateProperty(req.params.id, { isFeatured });
      if (!property) {
        return res.status(404).json({ message: "Propiedad no encontrada" });
      }
      res.json(property);
    } catch (error) {
      res.status(500).json({ message: "Error al actualizar propiedad" });
    }
  });

  // Get admin's properties
  app.get("/api/admin/my-properties", requireAdmin, async (req, res) => {
    try {
      const properties = await storage.getPropertiesByPartner(req.session.userId!);
      res.json(properties);
    } catch (error) {
      res.status(500).json({ message: "Error al obtener propiedades" });
    }
  });

  // Create property (admin)
  app.post("/api/admin/properties", requireAdmin, async (req, res) => {
    try {
      const data = {
        ...req.body,
        partnerId: req.session.userId!,
        isFeatured: false,
        isActive: true,
      };
      
      const validated = insertPropertySchema.parse(data);
      const property = await storage.createProperty(validated);
      res.status(201).json(property);
    } catch (error) {
      console.error("Create property error:", error);
      if (error instanceof z.ZodError) {
        return res.status(400).json({ message: error.errors[0].message });
      }
      res.status(500).json({ message: "Error al crear propiedad" });
    }
  });

  // Update property (admin's own)
  app.patch("/api/admin/properties/:id", requireAdmin, async (req, res) => {
    try {
      const property = await storage.getProperty(req.params.id);
      if (!property) {
        return res.status(404).json({ message: "Propiedad no encontrada" });
      }
      
      if (property.partnerId !== req.session.userId) {
        return res.status(403).json({ message: "No tienes permiso para editar esta propiedad" });
      }

      const updated = await storage.updateProperty(req.params.id, req.body);
      res.json(updated);
    } catch (error) {
      res.status(500).json({ message: "Error al actualizar propiedad" });
    }
  });

  // Delete property (admin's own)
  app.delete("/api/admin/properties/:id", requireAdmin, async (req, res) => {
    try {
      const property = await storage.getProperty(req.params.id);
      if (!property) {
        return res.status(404).json({ message: "Propiedad no encontrada" });
      }
      
      if (property.partnerId !== req.session.userId) {
        return res.status(403).json({ message: "No tienes permiso para eliminar esta propiedad" });
      }

      await storage.deleteProperty(req.params.id);
      res.json({ message: "Propiedad eliminada" });
    } catch (error) {
      res.status(500).json({ message: "Error al eliminar propiedad" });
    }
  });

  // ==================== PARTNER ROUTES ====================

  // Get partner's properties
  app.get("/api/partner/properties", requireAuth, async (req, res) => {
    try {
      const properties = await storage.getPropertiesByPartner(req.session.userId!);
      res.json(properties);
    } catch (error) {
      res.status(500).json({ message: "Error al obtener propiedades" });
    }
  });

  // Create property (partner)
  app.post("/api/partner/properties", requireAuth, async (req, res) => {
    try {
      const data = {
        ...req.body,
        partnerId: req.session.userId!,
        isFeatured: false,
        isActive: true,
      };
      
      const validated = insertPropertySchema.parse(data);
      const property = await storage.createProperty(validated);
      res.status(201).json(property);
    } catch (error) {
      console.error("Create property error:", error);
      if (error instanceof z.ZodError) {
        return res.status(400).json({ message: error.errors[0].message });
      }
      res.status(500).json({ message: "Error al crear propiedad" });
    }
  });

  // Update property (partner)
  app.patch("/api/partner/properties/:id", requireAuth, async (req, res) => {
    try {
      const property = await storage.getProperty(req.params.id);
      if (!property) {
        return res.status(404).json({ message: "Propiedad no encontrada" });
      }
      
      // Check ownership or admin
      if (property.partnerId !== req.session.userId && req.session.userRole !== "admin") {
        return res.status(403).json({ message: "No tienes permiso para editar esta propiedad" });
      }

      const updated = await storage.updateProperty(req.params.id, req.body);
      res.json(updated);
    } catch (error) {
      res.status(500).json({ message: "Error al actualizar propiedad" });
    }
  });

  // Delete property (partner)
  app.delete("/api/partner/properties/:id", requireAuth, async (req, res) => {
    try {
      const property = await storage.getProperty(req.params.id);
      if (!property) {
        return res.status(404).json({ message: "Propiedad no encontrada" });
      }
      
      // Check ownership or admin
      if (property.partnerId !== req.session.userId && req.session.userRole !== "admin") {
        return res.status(403).json({ message: "No tienes permiso para eliminar esta propiedad" });
      }

      await storage.deleteProperty(req.params.id);
      res.json({ message: "Propiedad eliminada" });
    } catch (error) {
      res.status(500).json({ message: "Error al eliminar propiedad" });
    }
  });

  // ==================== COMPANY INFO ROUTES ====================

  app.get("/api/company", async (req, res) => {
    try {
      const info = await storage.getCompanyInfo();
      res.json(info);
    } catch (error) {
      res.status(500).json({ message: "Error al obtener información" });
    }
  });

  app.patch("/api/company", requireAdmin, async (req, res) => {
    try {
      const info = await storage.updateCompanyInfo(req.body);
      res.json(info);
    } catch (error) {
      res.status(500).json({ message: "Error al actualizar información" });
    }
  });

  return httpServer;
}
