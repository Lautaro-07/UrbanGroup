import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { useParams, Link } from "wouter";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Bed,
  Bath,
  Maximize,
  MapPin,
  Car,
  ArrowLeft,
  Phone,
  Mail,
  Share2,
  Heart,
  ChevronLeft,
  ChevronRight,
  Building2,
} from "lucide-react";
import { SiWhatsapp } from "react-icons/si";
import type { Property } from "@shared/schema";

export default function PropertyDetail() {
  const { id } = useParams<{ id: string }>();
  const [currentImageIndex, setCurrentImageIndex] = useState(0);

  const { data: property, isLoading } = useQuery<Property>({
    queryKey: ["/api/properties", id],
  });

  const formatPrice = (price: string, currency: string) => {
    const numPrice = parseFloat(price);
    if (currency === "UF") {
      return `UF ${numPrice.toLocaleString("es-CL")}`;
    } else if (currency === "USD") {
      return `USD ${numPrice.toLocaleString("es-CL")}`;
    }
    return `$${numPrice.toLocaleString("es-CL")}`;
  };

  const getPropertyTypeLabel = (type: string) => {
    const types: Record<string, string> = {
      casa: "Casa",
      departamento: "Departamento",
      oficina: "Oficina",
      local: "Local Comercial",
      bodega: "Bodega",
      terreno: "Terreno",
      galpon: "Galpón",
      estacionamiento: "Estacionamiento",
    };
    return types[type] || type;
  };

  const images =
    property?.images && property.images.length > 0
      ? property.images
      : [
          "https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop",
        ];

  const nextImage = () => {
    setCurrentImageIndex((prev) => (prev + 1) % images.length);
  };

  const prevImage = () => {
    setCurrentImageIndex((prev) => (prev - 1 + images.length) % images.length);
  };

  if (isLoading) {
    return (
      <div className="min-h-screen pt-20 bg-background">
        <div className="max-w-7xl mx-auto px-4 lg:px-8 py-8">
          <Skeleton className="h-8 w-32 mb-6" />
          <div className="grid lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2 space-y-6">
              <Skeleton className="aspect-[16/10] rounded-xl" />
              <Skeleton className="h-48" />
            </div>
            <Skeleton className="h-96" />
          </div>
        </div>
      </div>
    );
  }

  if (!property) {
    return (
      <div
        className="min-h-screen pt-20 bg-background flex items-center justify-center"
        data-testid="property-not-found"
      >
        <div className="text-center">
          <Building2 className="w-16 h-16 mx-auto text-muted-foreground mb-4" />
          <h1 className="text-2xl font-bold mb-2">Propiedad no encontrada</h1>
          <p className="text-muted-foreground mb-6">
            La propiedad que buscas no existe o fue eliminada
          </p>
          <Link href="/propiedades">
            <Button>
              <ArrowLeft className="w-4 h-4 mr-2" />
              Volver a propiedades
            </Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div
      className="min-h-screen pt-20 bg-background"
      data-testid="page-property-detail"
    >
      <div className="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <Link href="/propiedades">
          <Button variant="ghost" className="mb-6" data-testid="button-back">
            <ArrowLeft className="w-4 h-4 mr-2" />
            Volver a propiedades
          </Button>
        </Link>

        <div className="grid lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 space-y-6">
            <div className="relative aspect-[16/10] rounded-xl overflow-hidden bg-muted">
              <img
                src={images[currentImageIndex]}
                alt={`${property.title} - Imagen ${currentImageIndex + 1}`}
                className="w-full h-full object-cover"
              />
              {images.length > 1 && (
                <>
                  <button
                    onClick={prevImage}
                    className="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors"
                    data-testid="button-prev-image"
                  >
                    <ChevronLeft className="w-6 h-6" />
                  </button>
                  <button
                    onClick={nextImage}
                    className="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70 transition-colors"
                    data-testid="button-next-image"
                  >
                    <ChevronRight className="w-6 h-6" />
                  </button>
                  <div className="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                    {currentImageIndex + 1} / {images.length}
                  </div>
                </>
              )}
            </div>

            {images.length > 1 && (
              <div className="flex gap-2 overflow-x-auto pb-2">
                {images.map((img, idx) => (
                  <button
                    key={idx}
                    onClick={() => setCurrentImageIndex(idx)}
                    className={`shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-colors ${
                      idx === currentImageIndex
                        ? "border-primary"
                        : "border-transparent"
                    }`}
                    data-testid={`button-thumbnail-${idx}`}
                  >
                    <img
                      src={img}
                      alt={`Miniatura ${idx + 1}`}
                      className="w-full h-full object-cover"
                    />
                  </button>
                ))}
              </div>
            )}

            <Card>
              <CardContent className="p-6">
                <div className="flex flex-wrap items-start justify-between gap-4 mb-6">
                  <div>
                    <div className="flex items-center gap-2 mb-2">
                      <Badge
                        variant={
                          property.operationType === "venta"
                            ? "default"
                            : "secondary"
                        }
                        className={
                          property.operationType === "venta"
                            ? "bg-green-600"
                            : "bg-amber-500"
                        }
                      >
                        {property.operationType === "venta"
                          ? "Venta"
                          : "Arriendo"}
                      </Badge>
                      <Badge variant="outline">
                        {getPropertyTypeLabel(property.propertyType)}
                      </Badge>
                      {property.isFeatured && (
                        <Badge className="bg-primary">Destacada</Badge>
                      )}
                    </div>
                    <h1 className="text-2xl lg:text-3xl font-bold text-foreground">
                      {property.title}
                    </h1>
                  </div>
                  <div className="text-right">
                    <div className="text-3xl font-bold text-primary">
                      {formatPrice(property.price, property.currency)}
                    </div>
                    {property.operationType === "arriendo" && (
                      <span className="text-muted-foreground text-sm">
                        /mes
                      </span>
                    )}
                  </div>
                </div>

                <div className="flex items-center gap-2 text-muted-foreground mb-6">
                  <MapPin className="w-5 h-5 text-primary" />
                  <span>
                    {property.address}, {property.comuna}, {property.region}
                  </span>
                </div>

                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-muted/30 rounded-lg mb-6">
                  <div className="text-center">
                    <Maximize className="w-6 h-6 mx-auto text-primary mb-2" />
                    <div className="text-lg font-semibold">
                      {property.squareMeters} m²
                    </div>
                    <div className="text-sm text-muted-foreground">
                      Superficie
                    </div>
                  </div>
                  {property.bedrooms && property.bedrooms > 0 && (
                    <div className="text-center">
                      <Bed className="w-6 h-6 mx-auto text-primary mb-2" />
                      <div className="text-lg font-semibold">
                        {property.bedrooms}
                      </div>
                      <div className="text-sm text-muted-foreground">
                        Dormitorios
                      </div>
                    </div>
                  )}
                  {property.bathrooms && property.bathrooms > 0 && (
                    <div className="text-center">
                      <Bath className="w-6 h-6 mx-auto text-primary mb-2" />
                      <div className="text-lg font-semibold">
                        {property.bathrooms}
                      </div>
                      <div className="text-sm text-muted-foreground">Baños</div>
                    </div>
                  )}
                  {property.parkingSpots && property.parkingSpots > 0 && (
                    <div className="text-center">
                      <Car className="w-6 h-6 mx-auto text-primary mb-2" />
                      <div className="text-lg font-semibold">
                        {property.parkingSpots}
                      </div>
                      <div className="text-sm text-muted-foreground">
                        Estacionamientos
                      </div>
                    </div>
                  )}
                </div>

                <div>
                  <h2 className="text-xl font-semibold mb-4">Descripción</h2>
                  <p className="text-muted-foreground whitespace-pre-line leading-relaxed">
                    {property.description}
                  </p>
                </div>
              </CardContent>
            </Card>
          </div>

          <div className="space-y-6">
            <Card className="sticky top-24">
              <CardContent className="p-6">
                <h3 className="font-semibold text-lg mb-4">
                  ¿Interesado en esta propiedad?
                </h3>
                <p className="text-muted-foreground text-sm mb-6">
                  Contáctanos y te ayudaremos a coordinar una visita
                </p>

                <div className="space-y-3">
                  <a href="tel:+56912345678" className="block">
                    <Button
                      className="w-full"
                      size="lg"
                      data-testid="button-call"
                    >
                      <Phone className="w-4 h-4 mr-2" />
                      Llamar ahora
                    </Button>
                  </a>

                  <a
                    href={`https://wa.me/56912345678?text=Hola, me interesa la propiedad: ${property.title}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="block"
                  >
                    <Button
                      variant="outline"
                      className="w-full bg-green-600 text-white border-green-600 hover:bg-green-700"
                      size="lg"
                      data-testid="button-whatsapp"
                    >
                      <SiWhatsapp className="w-4 h-4 mr-2" />
                      WhatsApp
                    </Button>
                  </a>

                  <a href="mailto:contacto@urbangroup.cl" className="block">
                    <Button
                      variant="outline"
                      className="w-full"
                      size="lg"
                      data-testid="button-email"
                    >
                      <Mail className="w-4 h-4 mr-2" />
                      Enviar correo
                    </Button>
                  </a>
                </div>

                <div className="flex gap-2 mt-6 pt-6 border-t">
                  <Button
                    variant="ghost"
                    className="flex-1"
                    data-testid="button-share"
                  >
                    <Share2 className="w-4 h-4 mr-2" />
                    Compartir
                  </Button>
                  <Button
                    variant="ghost"
                    className="flex-1"
                    data-testid="button-favorite"
                  >
                    <Heart className="w-4 h-4 mr-2" />
                    Guardar
                  </Button>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardContent className="p-6">
                <h3 className="font-semibold mb-4">Código de propiedad</h3>
                <code className="text-sm text-muted-foreground bg-muted px-2 py-1 rounded">
                  {property.id.slice(0, 8).toUpperCase()}
                </code>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
}
