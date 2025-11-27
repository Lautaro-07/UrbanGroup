import { useState } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { Link, useLocation } from "wouter";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
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
import { Skeleton } from "@/components/ui/skeleton";
import { useToast } from "@/hooks/use-toast";
import { useRequireAuth } from "@/hooks/useAuth";
import { apiRequest, queryClient } from "@/lib/queryClient";
import {
  Building2,
  Plus,
  Pencil,
  Trash2,
  LogOut,
  Home,
  Loader2,
  Eye,
  MapPin,
  Star,
} from "lucide-react";
import {
  propertyTypes,
  operationTypes,
  currencies,
  chileanRegions,
  comunasByRegion,
} from "@shared/schema";
import type { User, Property } from "@shared/schema";

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

type PropertyFormData = z.infer<typeof propertySchema>;

export default function PartnerDashboard() {
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [editingProperty, setEditingProperty] = useState<Property | null>(null);

  const { user: currentUser, isLoading: userLoading } = useRequireAuth();

  const { data: myProperties, isLoading: propertiesLoading } = useQuery<
    Property[]
  >({
    queryKey: ["/api/partner/properties"],
    enabled: !!currentUser,
  });

  const form = useForm<PropertyFormData>({
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

  const selectedRegion = form.watch("region");
  const availableComunas = selectedRegion
    ? comunasByRegion[selectedRegion] || []
    : [];

  const createPropertyMutation = useMutation({
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
      return apiRequest("POST", "/api/partner/properties", payload);
    },
    onSuccess: () => {
      toast({ title: "Propiedad creada exitosamente" });
      queryClient.invalidateQueries({ queryKey: ["/api/partner/properties"] });
      setIsDialogOpen(false);
      form.reset();
    },
    onError: (error: any) => {
      toast({
        variant: "destructive",
        title: "Error",
        description: error.message || "No se pudo crear la propiedad",
      });
    },
  });

  const updatePropertyMutation = useMutation({
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
      return apiRequest("PATCH", `/api/partner/properties/${id}`, payload);
    },
    onSuccess: () => {
      toast({ title: "Propiedad actualizada" });
      queryClient.invalidateQueries({ queryKey: ["/api/partner/properties"] });
      setIsDialogOpen(false);
      setEditingProperty(null);
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

  const deletePropertyMutation = useMutation({
    mutationFn: async (id: string) => {
      return apiRequest("DELETE", `/api/partner/properties/${id}`);
    },
    onSuccess: () => {
      toast({ title: "Propiedad eliminada" });
      queryClient.invalidateQueries({ queryKey: ["/api/partner/properties"] });
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

  const openEditDialog = (property: Property) => {
    setEditingProperty(property);
    form.reset({
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
    setIsDialogOpen(true);
  };

  const openCreateDialog = () => {
    setEditingProperty(null);
    form.reset();
    setIsDialogOpen(true);
  };

  const onSubmit = (data: PropertyFormData) => {
    if (editingProperty) {
      updatePropertyMutation.mutate({ id: editingProperty.id, data });
    } else {
      createPropertyMutation.mutate(data);
    }
  };

  if (userLoading) {
    return (
      <div className="min-h-screen pt-20 flex items-center justify-center">
        <Loader2 className="w-8 h-8 animate-spin" />
      </div>
    );
  }

  if (!currentUser) {
    return (
      <div className="min-h-screen pt-20 flex items-center justify-center">
        <div className="text-center">
          <h1 className="text-2xl font-bold mb-4">Acceso Requerido</h1>
          <p className="text-muted-foreground mb-4">
            Inicia sesión para acceder a tu panel
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
      data-testid="page-partner-dashboard"
    >
      <div className="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <div className="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-8">
          <div>
            <h1 className="text-3xl font-bold">Mi Panel</h1>
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

        <div className="grid md:grid-cols-2 gap-6 mb-8">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                  <Building2 className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <div className="text-2xl font-bold">
                    {myProperties?.length || 0}
                  </div>
                  <div className="text-muted-foreground text-sm">
                    Mis Propiedades
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
                    {myProperties?.filter((p) => p.isFeatured).length || 0}
                  </div>
                  <div className="text-muted-foreground text-sm">Destacadas</div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <CardTitle>Mis Propiedades</CardTitle>
            <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
              <DialogTrigger asChild>
                <Button
                  onClick={openCreateDialog}
                  data-testid="button-add-property"
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
                <Form {...form}>
                  <form
                    onSubmit={form.handleSubmit(onSubmit)}
                    className="space-y-4"
                  >
                    <FormField
                      control={form.control}
                      name="title"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Título</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="Ej: Hermoso departamento en Las Condes"
                              data-testid="input-property-title"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <div className="grid grid-cols-2 gap-4">
                      <FormField
                        control={form.control}
                        name="propertyType"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Tipo de Propiedad</FormLabel>
                            <Select
                              onValueChange={field.onChange}
                              value={field.value}
                            >
                              <FormControl>
                                <SelectTrigger data-testid="select-property-type">
                                  <SelectValue placeholder="Seleccionar" />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                {propertyTypes.map((type) => (
                                  <SelectItem
                                    key={type.value}
                                    value={type.value}
                                  >
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
                        control={form.control}
                        name="operationType"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Operación</FormLabel>
                            <Select
                              onValueChange={field.onChange}
                              value={field.value}
                            >
                              <FormControl>
                                <SelectTrigger data-testid="select-operation">
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
                        control={form.control}
                        name="price"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Precio</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                placeholder="5000"
                                data-testid="input-price"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={form.control}
                        name="currency"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Moneda</FormLabel>
                            <Select
                              onValueChange={field.onChange}
                              value={field.value}
                            >
                              <FormControl>
                                <SelectTrigger data-testid="select-currency">
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
                        control={form.control}
                        name="region"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Región</FormLabel>
                            <Select
                              onValueChange={(value) => {
                                field.onChange(value);
                                form.setValue("comuna", "");
                              }}
                              value={field.value}
                            >
                              <FormControl>
                                <SelectTrigger data-testid="select-region">
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
                        control={form.control}
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
                                <SelectTrigger data-testid="select-comuna">
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
                      control={form.control}
                      name="address"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Dirección</FormLabel>
                          <FormControl>
                            <Input
                              placeholder="Av. Apoquindo 1234"
                              data-testid="input-address"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <div className="grid grid-cols-4 gap-4">
                      <FormField
                        control={form.control}
                        name="squareMeters"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>m²</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                placeholder="120"
                                data-testid="input-m2"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={form.control}
                        name="bedrooms"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Dormitorios</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                placeholder="3"
                                data-testid="input-bedrooms"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={form.control}
                        name="bathrooms"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Baños</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                placeholder="2"
                                data-testid="input-bathrooms"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />

                      <FormField
                        control={form.control}
                        name="parkingSpots"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel>Estac.</FormLabel>
                            <FormControl>
                              <Input
                                type="number"
                                placeholder="1"
                                data-testid="input-parking"
                                {...field}
                              />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>

                    <FormField
                      control={form.control}
                      name="description"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Descripción</FormLabel>
                          <FormControl>
                            <Textarea
                              placeholder="Describe la propiedad en detalle..."
                              className="min-h-[100px]"
                              data-testid="input-description"
                              {...field}
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="images"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>
                            URLs de Imágenes (separadas por coma)
                          </FormLabel>
                          <FormControl>
                            <Textarea
                              placeholder="https://ejemplo.com/imagen1.jpg, https://ejemplo.com/imagen2.jpg"
                              data-testid="input-images"
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
                          createPropertyMutation.isPending ||
                          updatePropertyMutation.isPending
                        }
                        data-testid="button-save-property"
                      >
                        {createPropertyMutation.isPending ||
                        updatePropertyMutation.isPending ? (
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
            {propertiesLoading ? (
              <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                {[...Array(3)].map((_, i) => (
                  <Skeleton key={i} className="h-48" />
                ))}
              </div>
            ) : myProperties && myProperties.length > 0 ? (
              <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                {myProperties.map((property) => (
                  <Card
                    key={property.id}
                    className="overflow-hidden"
                    data-testid={`card-my-property-${property.id}`}
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
                            onClick={() => openEditDialog(property)}
                            data-testid={`button-edit-${property.id}`}
                          >
                            <Pencil className="w-4 h-4" />
                          </Button>
                          <Button
                            variant="ghost"
                            size="icon"
                            className="text-destructive"
                            onClick={() =>
                              deletePropertyMutation.mutate(property.id)
                            }
                            data-testid={`button-delete-${property.id}`}
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
                <Button onClick={openCreateDialog}>
                  <Plus className="w-4 h-4 mr-2" />
                  Agregar tu primera propiedad
                </Button>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
