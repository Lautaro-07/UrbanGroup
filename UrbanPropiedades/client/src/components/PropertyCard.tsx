import { Link } from "wouter";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Bed, Bath, Maximize, MapPin, Car } from "lucide-react";
import type { Property } from "@shared/schema";

interface PropertyCardProps {
  property: Property;
}

export function PropertyCard({ property }: PropertyCardProps) {
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

  const mainImage =
    property.images && property.images.length > 0
      ? property.images[0]
      : "https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop";

  return (
    <Link href={`/propiedad/${property.id}`}>
      <Card
        className="group overflow-hidden cursor-pointer hover-elevate border border-border/50"
        data-testid={`card-property-${property.id}`}
      >
        <div className="relative aspect-[4/3] overflow-hidden">
          <img
            src={mainImage}
            alt={property.title}
            className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
            loading="lazy"
          />
          <div className="absolute top-3 left-3 flex flex-wrap gap-2">
            <Badge
              variant="secondary"
              className="bg-primary text-primary-foreground font-semibold shadow-md"
            >
              {formatPrice(property.price, property.currency)}
            </Badge>
          </div>
          <div className="absolute top-3 right-3">
            <Badge
              variant={property.operationType === "venta" ? "default" : "secondary"}
              className={
                property.operationType === "venta"
                  ? "bg-green-600 text-white"
                  : "bg-amber-500 text-white"
              }
            >
              {property.operationType === "venta" ? "Venta" : "Arriendo"}
            </Badge>
          </div>
          {property.images && property.images.length > 1 && (
            <div className="absolute bottom-3 right-3 bg-black/60 text-white text-xs px-2 py-1 rounded-md">
              {property.images.length} fotos
            </div>
          )}
        </div>
        <CardContent className="p-4 space-y-3">
          <div>
            <h3 className="font-semibold text-foreground line-clamp-1 group-hover:text-primary transition-colors">
              {property.title}
            </h3>
            <p className="text-sm text-muted-foreground">
              {getPropertyTypeLabel(property.propertyType)} en{" "}
              {property.operationType === "venta" ? "Venta" : "Arriendo"}
            </p>
          </div>

          <div className="flex items-center gap-1.5 text-muted-foreground">
            <MapPin className="w-4 h-4 text-primary shrink-0" />
            <span className="text-sm line-clamp-1">
              {property.comuna}, {property.region}
            </span>
          </div>

          <div className="flex items-center gap-4 pt-2 border-t text-sm text-muted-foreground">
            <div className="flex items-center gap-1.5">
              <Maximize className="w-4 h-4" />
              <span>{property.squareMeters} m²</span>
            </div>
            {property.bedrooms && property.bedrooms > 0 && (
              <div className="flex items-center gap-1.5">
                <Bed className="w-4 h-4" />
                <span>{property.bedrooms}</span>
              </div>
            )}
            {property.bathrooms && property.bathrooms > 0 && (
              <div className="flex items-center gap-1.5">
                <Bath className="w-4 h-4" />
                <span>{property.bathrooms}</span>
              </div>
            )}
            {property.parkingSpots && property.parkingSpots > 0 && (
              <div className="flex items-center gap-1.5">
                <Car className="w-4 h-4" />
                <span>{property.parkingSpots}</span>
              </div>
            )}
          </div>
        </CardContent>
      </Card>
    </Link>
  );
}

export function PropertyCardSkeleton() {
  return (
    <Card className="overflow-hidden">
      <div className="aspect-[4/3] bg-muted animate-pulse" />
      <CardContent className="p-4 space-y-3">
        <div className="space-y-2">
          <div className="h-5 bg-muted rounded animate-pulse w-3/4" />
          <div className="h-4 bg-muted rounded animate-pulse w-1/2" />
        </div>
        <div className="h-4 bg-muted rounded animate-pulse w-2/3" />
        <div className="flex items-center gap-4 pt-2 border-t">
          <div className="h-4 bg-muted rounded animate-pulse w-16" />
          <div className="h-4 bg-muted rounded animate-pulse w-12" />
          <div className="h-4 bg-muted rounded animate-pulse w-12" />
        </div>
      </CardContent>
    </Card>
  );
}
