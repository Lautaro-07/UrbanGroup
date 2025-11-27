import { useState } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Link, useLocation } from "wouter";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Switch } from "@/components/ui/switch";
import { Skeleton } from "@/components/ui/skeleton";
import { useToast } from "@/hooks/use-toast";
import { useRequireAuth } from "@/hooks/useAuth";
import { apiRequest, queryClient } from "@/lib/queryClient";
import {
  Users,
  Building2,
  Plus,
  Pencil,
  Trash2,
  LogOut,
  Star,
  Home,
  Loader2,
  Eye,
  EyeOff,
  MapPin,
} from "lucide-react";
import {
  propertyTypes,
  operationTypes,
  currencies,
  chileanRegions,
  comunasByRegion,
} from "@shared/schema";
import { Textarea } from "@/components/ui/textarea";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import type { User, Property } from "@shared/schema";

const partnerSchema = z.object({
  username: z.string().min(3, "Mínimo 3 caracteres"),
  password: z.string().min(6, "Mínimo 6 caracteres"),
  name: z.string().min(2, "Ingrese el nombre"),
  email: z.string().email("Email inválido"),
  phone: z.string().optional(),
});

const propertySchema = z.object({
  title: z.string().min(5, "Mínimo 5 caracteres"),
  description: z.string().min(20, "Mínimo 20 caracteres"),
  propertyType: z.string().min(1, "Seleccione un tipo"),
  operationType: z.string().min(1, "Seleccione operación"),
  price: z.string().min(1, "Ingrese el precio"),
  currency: z.string().default("UF"),
  region: z.string().min(1, "Seleccione región"),
  comuna: z.string().min(1, "Seleccione comuna"),
  address: z.string().min(5, "Ingrese la dirección"),
  squareMeters: z.string().min(1, "Ingrese los m²"),
  bedrooms: z.string().optional(),
  bathrooms: z.string().optional(),
  parkingSpots: z.string().optional(),
  images: z.string().optional(),
});

type PartnerFormData = z.infer<typeof partnerSchema>;
type PropertyFormData = z.infer<typeof propertySchema>;

export default function AdminDashboard() {
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const [activeTab, setActiveTab] = useState<"partners" | "properties" | "my-properties">(
    "partners"
  );
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [isPropertyDialogOpen, setIsPropertyDialogOpen] = useState(false);
  const [editingPartner, setEditingPartner] = useState<User | null>(null);
  const [editingProperty, setEditingProperty] = useState<Property | null>(null);
  const [showPassword, setShowPassword] = useState(false);

  const { user: currentUser, isLoading: authLoading } = useRequireAuth("admin");

  const { data: partners, isLoading: partnersLoading } = useQuery<User[]>({
    queryKey: ["/api/admin/partners"],
  });

  const { data: allProperties, isLoading: propertiesLoading } = useQuery<
    Property[]
  >({
    queryKey: ["/api/admin/properties"],
  });

  const { data: myAdminProperties, isLoading: myPropertiesLoading } = useQuery<
    Property[]
  >({
    queryKey: ["/api/admin/my-properties"],
  });

  const form = useForm<PartnerFormData>({
    resolver: zodResolver(partnerSchema),
    defaultValues: {
      username: "",
      password: "",
      name: "",
      email: "",
      phone: "",
    },
  });

  const propertyForm = useForm<PropertyFormData>({
    resolver: zodResolver(propertySchema),
    defaultValues: {
      title: "",
      description: "",
      propertyType: "",
      operationType: "",
      price: "",
      currency: "UF",
      region: "",
      comuna: "",
      address: "",
      squareMeters: "",
      bedrooms: "",
      bathrooms: "",
      parkingSpots: "",
      images: "",
    },
  });

  const selectedRegion = propertyForm.watch("region");
  const availableComunas = selectedRegion
    ? comunasByRegion[selectedRegion] || []
    : [];

  const createPartnerMutation = useMutation({
    mutationFn: async (data: PartnerFormData) => {
      return apiRequest("POST", "/api/admin/partners", data);
    },
    onSuccess: () => {
      toast({ title: "Socio creado exitosamente" });
      queryClient.invalidateQueries({ queryKey: ["/api/admin/partners"] });
      setIsDialogOpen(false);
      form.reset();
    },
    onError: (error: any) => {
      toast({
        variant: "destructive",
        title: "Error",
        description: error.message || "No se pudo crear el socio",
      });
    },
  });

  const updatePartnerMutation = useMutation({
    mutationFn: async ({ id, data }: { id: string; data: Partial<User> }) => {
      return apiRequest("PATCH", `/api/admin/partners/${id}`, data);
    },
    onSuccess: () => {
      toast({ title: "Socio actualizado" });
      queryClient.invalidateQueries({ queryKey: ["/api/admin/partners"] });
      setIsDialogOpen(false);
      setEditingPartner(null);
      form.reset();
    },
    onError: (error: any) => {
      toast({
        variant: "destructive",
        title: "Error",
        description: error.message,
      });
    },
  });

  const deletePartnerMutation = useMutation({
    mutationFn: async (id: string) => {
      return apiRequest("DELETE", `/api/admin/partners/${id}`);
    },
    onSuccess: () => {
      toast({ title: "Socio eliminado" });
      queryClient.invalidateQueries({ queryKey: ["/api/admin/partners"] });
    },
    onError: (error: any) => {
      toast({
        variant: "destructive",
        title: "Error",
        description: error.message,
      });
    },
  });

  const toggleFeaturedMutation = useMutation({
    mutationFn: async ({
      id,
      isFeatured,
    }: {
      id: string;
      isFeatured: boolean;
    }) => {
      return apiRequest("PATCH", `/api/admin/properties/${id}/featured`, {
        isFeatured,
      });
    },
    onSuccess: () => {
      toast({ title: "Propiedad actualizada" });
      queryClient.invalidateQueries({ queryKey: ["/api/admin/properties"] });
      queryClient.invalidateQueries({ queryKey: ["/api/properties/featured"] });
    },
    onError: (error: any) => {
      toast({
        variant: "destructive",
        title: "Error",
        description: error.message,
      });
    },
  });

  const createAdminPropertyMutation = useMutation({
    mutationFn: async (data: PropertyFormData) => {
      const payload = {
        ...data,
        price: data.price,
        squareMeters: parseInt(data.squareMeters),
        bedrooms: data.bedrooms ? parseInt(data.bedrooms) : null,
        bathrooms: data.bathrooms ? parseInt(data.bathrooms) : null,
        parkingSpots: data.parkingSpots ? parseInt(data.parkingSpots) : null,
        images: data.images
          ? data.images.split(",").map((url) => url.trim())
          : [],
      };
      return apiRequest("POST", "/api/admin/properties", payload);
    },
    onSuccess: () => {
      toast({ title: "Propiedad creada exitosamente" });
      queryClient.invalidateQueries({ queryKey: ["/api/admin/my-properties"] });
      queryClient.invalidateQueries({ queryKey: ["/api/properties/featured"] });
      setIsPropertyDialogOpen(false);
      propertyForm.reset();
    },
    onError: (error: any) => {
      toast({
        variant: "destructive",
        title: "Error",
        description: error.message || "No se pudo crear la propiedad",
      });
    },
  });

  const updateAdminPropertyMutation = useMutation({
    mutationFn: async ({
      id,
      data,
    }: {
      id: string;
      data: PropertyFormData;
    }) => {
      const payload = {
        ...data,
        price: data.price,
        squareMeters: parseInt(data.squareMeters),
        bedrooms: data.bedrooms ? parseInt(data.bedrooms) : null,
        bathrooms: data.bathrooms ? parseInt(data.bathrooms) : null,
        parkingSpots: data.parkingSpots ? parseInt(data.parkingSpots) : null,
        images: data.images
          ? data.images.split(",").map((url) => url.trim())
          : [],
      };
      return apiRequest("PATCH", `/api/admin/properties/${id}`, payload);
    },
    onSuccess: () => {
      toast({ title: "Propiedad actualizada" });
      queryClient.invalidateQueries({ queryKey: ["/api/admin/my-properties"] });
      queryClient.invalidateQueries({ queryKey: ["/api/properties/featured"] });
      setIsPropertyDialogOpen(false);
      setEditingProperty(null);
      propertyForm.reset();
    },
    onError: (error: any) => {
      toast({
        variant: "destructive",
        title: "Error",
        description: error.message,
      });
    },
  });

  const deleteAdminPropertyMutation = useMutation({
    mutationFn: async (id: string) => {
      return apiRequest("DELETE", `/api/admin/properties/${id}`);
    },
    onSuccess: () => {
      toast({ title: "Propiedad eliminada" });
      queryClient.invalidateQueries({ queryKey: ["/api/admin/my-properties"] });
    },
    onError: (error: any) => {
      toast({
        variant: "destructive",
        title: "Error",
        description: error.message,
      });
    },
  });

  const handleLogout = async () => {
    await apiRequest("POST", "/api/auth/logout");
    queryClient.clear();
    setLocation("/login");
  };

  const openEditDialog = (partner: User) => {
    setEditingPartner(partner);
    form.reset({
      username: partner.username,
      password: "",
      name: partner.name,
      email: partner.email,
      phone: partner.phone || "",
    });
    setIsDialogOpen(true);
  };

  const openCreateDialog = () => {
    setEditingPartner(null);
    form.reset({
      username: "",
      password: "",
      name: "",
      email: "",
      phone: "",
    });
    setIsDialogOpen(true);
  };

  const onSubmit = (data: PartnerFormData) => {
    if (editingPartner) {
      const updateData: any = { ...data };
      if (!data.password) delete updateData.password;
      updatePartnerMutation.mutate({ id: editingPartner.id, data: updateData });
    } else {
      createPartnerMutation.mutate(data);
    }
  };

  const onPropertySubmit = (data: PropertyFormData) => {
    if (editingProperty) {
      updateAdminPropertyMutation.mutate({ id: editingProperty.id, data });
    } else {
      createAdminPropertyMutation.mutate(data);
    }
  };

  const openEditPropertyDialog = (property: Property) => {
    setEditingProperty(property);
    propertyForm.reset({
      title: property.title,
      description: property.description,
      propertyType: property.propertyType,
      operationType: property.operationType,
      price: property.price,
      currency: property.currency,
      region: property.region,
      comuna: property.comuna,
      address: property.address,
      squareMeters: property.squareMeters.toString(),
      bedrooms: property.bedrooms?.toString() || "",
      bathrooms: property.bathrooms?.toString() || "",
      parkingSpots: property.parkingSpots?.toString() || "",
      images: property.images?.join(", ") || "",
    });
    setIsPropertyDialogOpen(true);
  };

  const openCreatePropertyDialog = () => {
    setEditingProperty(null);
    propertyForm.reset();
    setIsPropertyDialogOpen(true);
  };

  if (!currentUser || currentUser.role !== "admin") {
    return (
      <div className="min-h-screen pt-20 flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-2xl font-bold mb-4">Acceso Denegado</h1>
          <p className="text-muted-foreground mb-4">
            No tienes permisos para acceder a esta sección
          </p>
          <Link href="/login">
            <Button>Iniciar Sesión</Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div
      className="min-h-screen pt-20 bg-background"
      data-testid="page-admin-dashboard"
    >
      <div className="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <div className="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-8">
          <div>
            <h1 className="text-3xl font-bold">Panel de Administración</h1>
            <p className="text-muted-foreground">
              Bienvenido, {currentUser.name}
            </p>
          </div>
          <div className="flex items-center gap-2">
            <Link href="/">
              <Button variant="outline" data-testid="button-go-home">
                <Home className="w-4 h-4 mr-2" />
                Ir al sitio
              </Button>
            </Link>
            <Button
              variant="ghost"
              onClick={handleLogout}
              data-testid="button-logout"
            >
              <LogOut className="w-4 h-4 mr-2" />
              Cerrar Sesión
            </Button>
          </div>
        </div>

        <div className="grid md:grid-cols-3 gap-6 mb-8">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                  <Users className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <div className="text-2xl font-bold">
                    {partners?.length || 0}
                  </div>
                  <div className="text-muted-foreground text-sm">
                    Socios Activos
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                  <Building2 className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <div className="text-2xl font-bold">
                    {allProperties?.length || 0}
                  </div>
                  <div className="text-muted-foreground text-sm">
                    Propiedades Totales
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-lg bg-amber-500/10 flex items-center justify-center">
                  <Star className="w-6 h-6 text-amber-500" />
                </div>
                <div>
                  <div className="text-2xl font-bold">
                    {allProperties?.filter((p) => p.isFeatured).length || 0}
                  </div>
                  <div className="text-muted-foreground text-sm">
                    Destacadas
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <div className="flex gap-2 mb-6 flex-wrap">
          <Button
            variant={activeTab === "partners" ? "default" : "outline"}
            onClick={() => setActiveTab("partners")}
            data-testid="tab-partners"
          >
            <Users className="w-4 h-4 mr-2" />
            Socios
          </Button>
          <Button
            variant={activeTab === "properties" ? "default" : "outline"}
            onClick={() => setActiveTab("properties")}
            data-testid="tab-properties"
          >
            <Building2 className="w-4 h-4 mr-2" />
            Todas las Propiedades
          </Button>
          <Button
            variant={activeTab === "my-properties" ? "default" : "outline"}
            onClick={() => setActiveTab("my-properties")}
            data-testid="tab-my-properties"
          >
            <Home className="w-4 h-4 mr-2" />
            Mis Propiedades
          </Button>
        </div>

        {activeTab === "partners" && (
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle>Gestión de Socios</CardTitle>
              <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
                <DialogTrigger asChild>
                  <Button
                    onClick={openCreateDialog}
                    data-testid="button-add-partner"
                  >
                    <Plus className="w-4 h-4 mr-2" />
                    Agregar Socio
                  </Button>
                </DialogTrigger>
                <DialogContent>
                  <DialogHeader>
                    <DialogTitle>
                      {editingPartner ? "Editar Socio" : "Nuevo Socio"}
                    </DialogTitle>
                  </DialogHeader>
                  <Form {...form}>
                    <form
                      onSubmit={form.handleSubmit(onSubmit)}
                      className="space-y-4"
                    >
                      <FormField
                        control={form.control}
                        name="name"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Nombre Completo</FormLabel>
                            <FormControl>
                              <Input
                                placeholder="Juan Pérez"
                                data-testid="input-partner-name"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <FormField
                        control={form.control}
                        name="username"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Usuario</FormLabel>
                            <FormControl>
                              <Input
                                placeholder="usuario"
                                data-testid="input-partner-username"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <FormField
                        control={form.control}
                        name="password"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>
                              Contraseña{" "}
                              {editingPartner && "(dejar vacío para no cambiar)"}
                            </FormLabel>
                            <FormControl>
                              <div className="relative">
                                <Input
                                  type={showPassword ? "text" : "password"}
                                  placeholder="******"
                                  data-testid="input-partner-password"
                                  {...field}
                                />
                                <Button
                                  type="button"
                                  variant="ghost"
                                  size="icon"
                                  className="absolute right-0 top-0 h-full px-3"
                                  onClick={() => setShowPassword(!showPassword)}
                                >
                                  {showPassword ? (
                                    <EyeOff className="w-4 h-4" />
                                  ) : (
                                    <Eye className="w-4 h-4" />
                                  )}
                                </Button>
                              </div>
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <FormField
                        control={form.control}
                        name="email"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Email</FormLabel>
                            <FormControl>
                              <Input
                                type="email"
                                placeholder="correo@ejemplo.com"
                                data-testid="input-partner-email"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <FormField
                        control={form.control}
                        name="phone"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Teléfono (opcional)</FormLabel>
                            <FormControl>
                              <Input
                                placeholder="+56 9 1234 5678"
                                data-testid="input-partner-phone"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      <div className="flex justify-end gap-2 pt-4">
                        <Button
                          type="button"
                          variant="outline"
                          onClick={() => setIsDialogOpen(false)}
                        >
                          Cancelar
                        </Button>
                        <Button
                          type="submit"
                          disabled={
                            createPartnerMutation.isPending ||
                            updatePartnerMutation.isPending
                          }
                          data-testid="button-save-partner"
                        >
                          {createPartnerMutation.isPending ||
                          updatePartnerMutation.isPending ? (
                            <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                          ) : null}
                          {editingPartner ? "Guardar Cambios" : "Crear Socio"}
                        </Button>
                      </div>
                    </form>
                  </Form>
                </DialogContent>
              </Dialog>
            </CardHeader>
            <CardContent>
              {partnersLoading ? (
                <div className="space-y-3">
                  {[...Array(3)].map((_, i) => (
                    <Skeleton key={i} className="h-16" />
                  ))}
                </div>
              ) : partners && partners.length > 0 ? (
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Nombre</TableHead>
                      <TableHead>Usuario</TableHead>
                      <TableHead>Email</TableHead>
                      <TableHead>Estado</TableHead>
                      <TableHead className="text-right">Acciones</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {partners.map((partner) => (
                      <TableRow
                        key={partner.id}
                        data-testid={`row-partner-${partner.id}`}
                      >
                        <TableCell className="font-medium">
                          {partner.name}
                        </TableCell>
                        <TableCell>{partner.username}</TableCell>
                        <TableCell>{partner.email}</TableCell>
                        <TableCell>
                          <Badge
                            variant={partner.isActive ? "default" : "secondary"}
                          >
                            {partner.isActive ? "Activo" : "Inactivo"}
                          </Badge>
                        </TableCell>
                        <TableCell className="text-right">
                          <div className="flex justify-end gap-2">
                            <Button
                              variant="ghost"
                              size="icon"
                              onClick={() => openEditDialog(partner)}
                              data-testid={`button-edit-partner-${partner.id}`}
                            >
                              <Pencil className="w-4 h-4" />
                            </Button>
                            <Button
                              variant="ghost"
                              size="icon"
                              className="text-destructive"
                              onClick={() =>
                                deletePartnerMutation.mutate(partner.id)
                              }
                              data-testid={`button-delete-partner-${partner.id}`}
                            >
                              <Trash2 className="w-4 h-4" />
                            </Button>
                          </div>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              ) : (
                <div className="text-center py-12 text-muted-foreground">
                  <Users className="w-12 h-12 mx-auto mb-4 opacity-50" />
                  <p>No hay socios registrados</p>
                </div>
              )}
            </CardContent>
          </Card>
        )}

        {activeTab === "properties" && (
          <Card>
            <CardHeader>
              <CardTitle>Todas las Propiedades</CardTitle>
            </CardHeader>
            <CardContent>
              {propertiesLoading ? (
                <div className="space-y-3">
                  {[...Array(5)].map((_, i) => (
                    <Skeleton key={i} className="h-16" />
                  ))}
                </div>
              ) : allProperties && allProperties.length > 0 ? (
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Propiedad</TableHead>
                      <TableHead>Tipo</TableHead>
                      <TableHead>Ubicación</TableHead>
                      <TableHead>Precio</TableHead>
                      <TableHead>Destacada</TableHead>
                      <TableHead className="text-right">Acciones</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {allProperties.map((property) => (
                      <TableRow
                        key={property.id}
                        data-testid={`row-property-${property.id}`}
                      >
                        <TableCell className="font-medium max-w-[200px] truncate">
                          {property.title}
                        </TableCell>
                        <TableCell>
                          <Badge variant="outline">
                            {property.propertyType}
                          </Badge>
                        </TableCell>
                        <TableCell>{property.comuna}</TableCell>
                        <TableCell>
                          {property.currency}{" "}
                          {parseFloat(property.price).toLocaleString("es-CL")}
                        </TableCell>
                        <TableCell>
                          <Switch
                            checked={property.isFeatured}
                            onCheckedChange={(checked) =>
                              toggleFeaturedMutation.mutate({
                                id: property.id,
                                isFeatured: checked,
                              })
                            }
                            data-testid={`switch-featured-${property.id}`}
                          />
                        </TableCell>
                        <TableCell className="text-right">
                          <Link href={`/propiedad/${property.id}`}>
                            <Button variant="ghost" size="sm">
                              Ver
                            </Button>
                          </Link>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              ) : (
                <div className="text-center py-12 text-muted-foreground">
                  <Building2 className="w-12 h-12 mx-auto mb-4 opacity-50" />
                  <p>No hay propiedades registradas</p>
                </div>
              )}
            </CardContent>
          </Card>
        )}

        {activeTab === "my-properties" && (
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle>Mis Propiedades</CardTitle>
              <Dialog open={isPropertyDialogOpen} onOpenChange={setIsPropertyDialogOpen}>
                <DialogTrigger asChild>
                  <Button
                    onClick={openCreatePropertyDialog}
                    data-testid="button-add-admin-property"
                  >
                    <Plus className="w-4 h-4 mr-2" />
                    Nueva Propiedad
                  </Button>
                </DialogTrigger>
                <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
                  <DialogHeader>
                    <DialogTitle>
                      {editingProperty ? "Editar Propiedad" : "Nueva Propiedad"}
                    </DialogTitle>
                  </DialogHeader>
                  <Form {...propertyForm}>
                    <form
                      onSubmit={propertyForm.handleSubmit(onPropertySubmit)}
                      className="space-y-4"
                    >
                      <FormField
                        control={propertyForm.control}
                        name="title"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Título</FormLabel>
                            <FormControl>
                              <Input
                                placeholder="Ej: Hermoso departamento en Las Condes"
                                data-testid="input-admin-title"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <div className="grid grid-cols-2 gap-4">
                        <FormField
                          control={propertyForm.control}
                          name="propertyType"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Tipo</FormLabel>
                              <Select
                                onValueChange={field.onChange}
                                value={field.value}
                              >
                                <FormControl>
                                  <SelectTrigger>
                                    <SelectValue placeholder="Seleccionar" />
                                  </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                  {propertyTypes.map((type) => (
                                    <SelectItem key={type.value} value={type.value}>
                                      {type.label}
                                    </SelectItem>
                                  ))}
                                </SelectContent>
                              </Select>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={propertyForm.control}
                          name="operationType"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Operación</FormLabel>
                              <Select
                                onValueChange={field.onChange}
                                value={field.value}
                              >
                                <FormControl>
                                  <SelectTrigger>
                                    <SelectValue placeholder="Seleccionar" />
                                  </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                  {operationTypes.map((op) => (
                                    <SelectItem key={op.value} value={op.value}>
                                      {op.label}
                                    </SelectItem>
                                  ))}
                                </SelectContent>
                              </Select>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </div>

                      <div className="grid grid-cols-2 gap-4">
                        <FormField
                          control={propertyForm.control}
                          name="price"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Precio</FormLabel>
                              <FormControl>
                                <Input
                                  type="number"
                                  placeholder="5000"
                                  {...field}
                                />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={propertyForm.control}
                          name="currency"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Moneda</FormLabel>
                              <Select
                                onValueChange={field.onChange}
                                value={field.value}
                              >
                                <FormControl>
                                  <SelectTrigger>
                                    <SelectValue />
                                  </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                  {currencies.map((curr) => (
                                    <SelectItem key={curr.value} value={curr.value}>
                                      {curr.label}
                                    </SelectItem>
                                  ))}
                                </SelectContent>
                              </Select>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </div>

                      <div className="grid grid-cols-2 gap-4">
                        <FormField
                          control={propertyForm.control}
                          name="region"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Región</FormLabel>
                              <Select
                                onValueChange={(value) => {
                                  field.onChange(value);
                                  propertyForm.setValue("comuna", "");
                                }}
                                value={field.value}
                              >
                                <FormControl>
                                  <SelectTrigger>
                                    <SelectValue placeholder="Seleccionar" />
                                  </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                  {chileanRegions.map((reg) => (
                                    <SelectItem key={reg} value={reg}>
                                      {reg}
                                    </SelectItem>
                                  ))}
                                </SelectContent>
                              </Select>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={propertyForm.control}
                          name="comuna"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Comuna</FormLabel>
                              <Select
                                onValueChange={field.onChange}
                                value={field.value}
                                disabled={!selectedRegion}
                              >
                                <FormControl>
                                  <SelectTrigger>
                                    <SelectValue placeholder="Seleccionar" />
                                  </SelectTrigger>
                                </FormControl>
                                <SelectContent>
                                  {availableComunas.map((com) => (
                                    <SelectItem key={com} value={com}>
                                      {com}
                                    </SelectItem>
                                  ))}
                                </SelectContent>
                              </Select>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </div>

                      <FormField
                        control={propertyForm.control}
                        name="address"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Dirección</FormLabel>
                            <FormControl>
                              <Input
                                placeholder="Av. Apoquindo 1234"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <div className="grid grid-cols-4 gap-4">
                        <FormField
                          control={propertyForm.control}
                          name="squareMeters"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>m²</FormLabel>
                              <FormControl>
                                <Input type="number" placeholder="120" {...field} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={propertyForm.control}
                          name="bedrooms"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Dorm.</FormLabel>
                              <FormControl>
                                <Input type="number" placeholder="3" {...field} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={propertyForm.control}
                          name="bathrooms"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Baños</FormLabel>
                              <FormControl>
                                <Input type="number" placeholder="2" {...field} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />

                        <FormField
                          control={propertyForm.control}
                          name="parkingSpots"
                          render={({ field }) => (
                            <FormItem>
                              <FormLabel>Estac.</FormLabel>
                              <FormControl>
                                <Input type="number" placeholder="1" {...field} />
                              </FormControl>
                              <FormMessage />
                            </FormItem>
                          )}
                        />
                      </div>

                      <FormField
                        control={propertyForm.control}
                        name="description"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Descripción</FormLabel>
                            <FormControl>
                              <Textarea
                                placeholder="Describe la propiedad..."
                                className="min-h-[100px]"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={propertyForm.control}
                        name="images"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>URLs de Imágenes (separadas por coma)</FormLabel>
                            <FormControl>
                              <Textarea
                                placeholder="https://ejemplo.com/img1.jpg, https://ejemplo.com/img2.jpg"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <div className="flex justify-end gap-2 pt-4">
                        <Button
                          type="button"
                          variant="outline"
                          onClick={() => setIsPropertyDialogOpen(false)}
                        >
                          Cancelar
                        </Button>
                        <Button
                          type="submit"
                          disabled={
                            createAdminPropertyMutation.isPending ||
                            updateAdminPropertyMutation.isPending
                          }
                        >
                          {createAdminPropertyMutation.isPending ||
                          updateAdminPropertyMutation.isPending ? (
                            <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                          ) : null}
                          {editingProperty ? "Guardar Cambios" : "Crear Propiedad"}
                        </Button>
                      </div>
                    </form>
                  </Form>
                </DialogContent>
              </Dialog>
            </CardHeader>
            <CardContent>
              {myPropertiesLoading ? (
                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {[...Array(3)].map((_, i) => (
                    <Skeleton key={i} className="h-48" />
                  ))}
                </div>
              ) : myAdminProperties && myAdminProperties.length > 0 ? (
                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                  {myAdminProperties.map((property) => (
                    <Card
                      key={property.id}
                      className="overflow-hidden"
                      data-testid={`card-admin-property-${property.id}`}
                    >
                      <div className="aspect-video relative overflow-hidden bg-muted">
                        {property.images && property.images.length > 0 ? (
                          <img
                            src={property.images[0]}
                            alt={property.title}
                            className="w-full h-full object-cover"
                          />
                        ) : (
                          <div className="w-full h-full flex items-center justify-center">
                            <Building2 className="w-12 h-12 text-muted-foreground" />
                          </div>
                        )}
                        {property.isFeatured && (
                          <Badge className="absolute top-2 right-2 bg-amber-500">
                            <Star className="w-3 h-3 mr-1" />
                            Destacada
                          </Badge>
                        )}
                      </div>
                      <CardContent className="p-4">
                        <h3 className="font-semibold line-clamp-1 mb-1">
                          {property.title}
                        </h3>
                        <div className="flex items-center gap-1 text-sm text-muted-foreground mb-2">
                          <MapPin className="w-3 h-3" />
                          {property.comuna}
                        </div>
                        <div className="flex items-center justify-between">
                          <span className="font-bold text-primary">
                            {property.currency}{" "}
                            {parseFloat(property.price).toLocaleString("es-CL")}
                          </span>
                          <div className="flex gap-1">
                            <Link href={`/propiedad/${property.id}`}>
                              <Button variant="ghost" size="icon">
                                <Eye className="w-4 h-4" />
                              </Button>
                            </Link>
                            <Button
                              variant="ghost"
                              size="icon"
                              onClick={() => openEditPropertyDialog(property)}
                            >
                              <Pencil className="w-4 h-4" />
                            </Button>
                            <Button
                              variant="ghost"
                              size="icon"
                              className="text-destructive"
                              onClick={() =>
                                deleteAdminPropertyMutation.mutate(property.id)
                              }
                            >
                              <Trash2 className="w-4 h-4" />
                            </Button>
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              ) : (
                <div className="text-center py-12 text-muted-foreground">
                  <Building2 className="w-12 h-12 mx-auto mb-4 opacity-50" />
                  <p className="mb-4">No tienes propiedades registradas</p>
                  <Button onClick={openCreatePropertyDialog}>
                    <Plus className="w-4 h-4 mr-2" />
                    Agregar tu primera propiedad
                  </Button>
                </div>
              )}
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  );
}
