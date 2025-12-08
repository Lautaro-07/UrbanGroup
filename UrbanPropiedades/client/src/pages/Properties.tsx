import { useState, useEffect } from "react";
import { useQuery } from "@tanstack/react-query";
import { useSearch, useLocation } from "wouter";
import { Button } from "@/components/ui/button";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { PropertyCard, PropertyCardSkeleton } from "@/components/PropertyCard";
import {
  Building2,
  Search,
  X,
  SlidersHorizontal,
  Grid3X3,
  List,
} from "lucide-react";
import {
  propertyTypes,
  operationTypes,
  chileanRegions,
  comunasByRegion,
} from "@shared/schema";
import type { Property } from "@shared/schema";
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet";

export default function Properties() {
  const searchString = useSearch();
  const [, setLocation] = useLocation();
  const searchParams = new URLSearchParams(searchString);

  const [filters, setFilters] = useState({
    tipo: searchParams.get("tipo") || "",
    operacion: searchParams.get("operacion") || "",
    region: searchParams.get("region") || "",
    comuna: searchParams.get("comuna") || "",
    minPrice: searchParams.get("minPrice") || "",
    maxPrice: searchParams.get("maxPrice") || "",
    minM2: searchParams.get("minM2") || "",
    maxM2: searchParams.get("maxM2") || "",
  });

  const [viewMode, setViewMode] = useState<"grid" | "list">("grid");

  const buildQueryString = () => {
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([key, value]) => {
      if (value && value !== "all") {
        params.set(key, value);
      }
    });
    return params.toString();
  };

  const { data: properties, isLoading } = useQuery<Property[]>({
    queryKey: ["/api/properties", buildQueryString()],
  });

  const availableComunas = filters.region
    ? comunasByRegion[filters.region] || []
    : [];

  const updateFilter = (key: string, value: string) => {
    const newFilters = { ...filters, [key]: value };
    if (key === "region") {
      newFilters.comuna = "";
    }
    setFilters(newFilters);
  };

  const applyFilters = () => {
    const params = buildQueryString();
    setLocation(`/propiedades${params ? `?${params}` : ""}`);
  };

  const clearFilters = () => {
    setFilters({
      tipo: "",
      operacion: "",
      region: "",
      comuna: "",
      minPrice: "",
      maxPrice: "",
      minM2: "",
      maxM2: "",
    });
    setLocation("/propiedades");
  };

  const activeFiltersCount = Object.values(filters).filter(
    (v) => v && v !== "all"
  ).length;

  useEffect(() => {
    setFilters({
      tipo: searchParams.get("tipo") || "",
      operacion: searchParams.get("operacion") || "",
      region: searchParams.get("region") || "",
      comuna: searchParams.get("comuna") || "",
      minPrice: searchParams.get("minPrice") || "",
      maxPrice: searchParams.get("maxPrice") || "",
      minM2: searchParams.get("minM2") || "",
      maxM2: searchParams.get("maxM2") || "",
    });
  }, [searchString]);

  const FilterContent = () => (
    <div className="space-y-6">
      <div className="space-y-3">
        <label className="text-sm font-medium">Tipo de Propiedad</label>
        <Select
          value={filters.tipo}
          onValueChange={(v) => updateFilter("tipo", v)}
        >
          <SelectTrigger data-testid="filter-property-type">
            <SelectValue placeholder="Todos los tipos" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Todos los tipos</SelectItem>
            {propertyTypes.map((type) => (
              <SelectItem key={type.value} value={type.value}>
                {type.label}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      <div className="space-y-3">
        <label className="text-sm font-medium">Operación</label>
        <Select
          value={filters.operacion}
          onValueChange={(v) => updateFilter("operacion", v)}
        >
          <SelectTrigger data-testid="filter-operation">
            <SelectValue placeholder="Todas" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Todas</SelectItem>
            {operationTypes.map((op) => (
              <SelectItem key={op.value} value={op.value}>
                {op.label}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      <div className="space-y-3">
        <label className="text-sm font-medium">Región</label>
        <Select
          value={filters.region}
          onValueChange={(v) => updateFilter("region", v)}
        >
          <SelectTrigger data-testid="filter-region">
            <SelectValue placeholder="Todas las regiones" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Todas las regiones</SelectItem>
            {chileanRegions.map((reg) => (
              <SelectItem key={reg} value={reg}>
                {reg}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      <div className="space-y-3">
        <label className="text-sm font-medium">Comuna</label>
        <Select
          value={filters.comuna}
          onValueChange={(v) => updateFilter("comuna", v)}
          disabled={!filters.region}
        >
          <SelectTrigger data-testid="filter-comuna">
            <SelectValue
              placeholder={
                filters.region ? "Todas las comunas" : "Seleccione región"
              }
            />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Todas las comunas</SelectItem>
            {availableComunas.map((com) => (
              <SelectItem key={com} value={com}>
                {com}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      <div className="space-y-3">
        <label className="text-sm font-medium">Rango de Precio (UF)</label>
        <div className="grid grid-cols-2 gap-2">
          <Input
            type="number"
            placeholder="Mínimo"
            value={filters.minPrice}
            onChange={(e) => updateFilter("minPrice", e.target.value)}
            data-testid="filter-min-price"
          />
          <Input
            type="number"
            placeholder="Máximo"
            value={filters.maxPrice}
            onChange={(e) => updateFilter("maxPrice", e.target.value)}
            data-testid="filter-max-price"
          />
        </div>
      </div>

      <div className="space-y-3">
        <label className="text-sm font-medium">Superficie (m²)</label>
        <div className="grid grid-cols-2 gap-2">
          <Input
            type="number"
            placeholder="Mínimo"
            value={filters.minM2}
            onChange={(e) => updateFilter("minM2", e.target.value)}
            data-testid="filter-min-m2"
          />
          <Input
            type="number"
            placeholder="Máximo"
            value={filters.maxM2}
            onChange={(e) => updateFilter("maxM2", e.target.value)}
            data-testid="filter-max-m2"
          />
        </div>
      </div>

      <div className="flex gap-2 pt-4">
        <Button
          onClick={applyFilters}
          className="flex-1"
          data-testid="button-apply-filters"
        >
          <Search className="w-4 h-4 mr-2" />
          Aplicar Filtros
        </Button>
        {activeFiltersCount > 0 && (
          <Button
            variant="outline"
            onClick={clearFilters}
            data-testid="button-clear-filters"
          >
            <X className="w-4 h-4" />
          </Button>
        )}
      </div>
    </div>
  );

  return (
    <div className="min-h-screen pt-20 bg-background" data-testid="page-properties">
      <div className="bg-primary/5 border-b py-8">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <h1 className="text-3xl font-bold text-foreground mb-2">
            Propiedades
          </h1>
          <p className="text-muted-foreground">
            Encuentra la propiedad ideal para ti
          </p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 lg:px-8 py-8">
        <div className="flex flex-col lg:flex-row gap-8">
          <aside className="hidden lg:block w-72 shrink-0">
            <div className="sticky top-24 bg-card border rounded-lg p-6">
              <h3 className="font-semibold mb-6 flex items-center gap-2">
                <SlidersHorizontal className="w-5 h-5" />
                Filtros
                {activeFiltersCount > 0 && (
                  <Badge variant="secondary" className="ml-auto">
                    {activeFiltersCount}
                  </Badge>
                )}
              </h3>
              <FilterContent />
            </div>
          </aside>

          <main className="flex-1">
            <div className="flex flex-wrap items-center justify-between gap-4 mb-6">
              <div className="flex items-center gap-2">
                <Sheet>
                  <SheetTrigger asChild>
                    <Button
                      variant="outline"
                      className="lg:hidden"
                      data-testid="button-mobile-filters"
                    >
                      <SlidersHorizontal className="w-4 h-4 mr-2" />
                      Filtros
                      {activeFiltersCount > 0 && (
                        <Badge variant="secondary" className="ml-2">
                          {activeFiltersCount}
                        </Badge>
                      )}
                    </Button>
                  </SheetTrigger>
                  <SheetContent side="left" className="w-80">
                    <SheetHeader>
                      <SheetTitle className="flex items-center gap-2">
                        <SlidersHorizontal className="w-5 h-5" />
                        Filtros
                      </SheetTitle>
                    </SheetHeader>
                    <div className="mt-6">
                      <FilterContent />
                    </div>
                  </SheetContent>
                </Sheet>

                <span className="text-sm text-muted-foreground">
                  {isLoading
                    ? "Cargando..."
                    : `${properties?.length || 0} propiedades encontradas`}
                </span>
              </div>

              <div className="flex items-center gap-2">
                <Button
                  variant={viewMode === "grid" ? "secondary" : "ghost"}
                  size="icon"
                  onClick={() => setViewMode("grid")}
                  data-testid="button-view-grid"
                >
                  <Grid3X3 className="w-4 h-4" />
                </Button>
                <Button
                  variant={viewMode === "list" ? "secondary" : "ghost"}
                  size="icon"
                  onClick={() => setViewMode("list")}
                  data-testid="button-view-list"
                >
                  <List className="w-4 h-4" />
                </Button>
              </div>
            </div>

            {activeFiltersCount > 0 && (
              <div className="flex flex-wrap gap-2 mb-6">
                {filters.tipo && filters.tipo !== "all" && (
                  <Badge variant="secondary" className="gap-1">
                    {propertyTypes.find((t) => t.value === filters.tipo)?.label}
                    <X
                      className="w-3 h-3 cursor-pointer"
                      onClick={() => updateFilter("tipo", "")}
                    />
                  </Badge>
                )}
                {filters.operacion && filters.operacion !== "all" && (
                  <Badge variant="secondary" className="gap-1">
                    {
                      operationTypes.find((o) => o.value === filters.operacion)
                        ?.label
                    }
                    <X
                      className="w-3 h-3 cursor-pointer"
                      onClick={() => updateFilter("operacion", "")}
                    />
                  </Badge>
                )}
                {filters.region && filters.region !== "all" && (
                  <Badge variant="secondary" className="gap-1">
                    {filters.region}
                    <X
                      className="w-3 h-3 cursor-pointer"
                      onClick={() => updateFilter("region", "")}
                    />
                  </Badge>
                )}
                {filters.comuna && filters.comuna !== "all" && (
                  <Badge variant="secondary" className="gap-1">
                    {filters.comuna}
                    <X
                      className="w-3 h-3 cursor-pointer"
                      onClick={() => updateFilter("comuna", "")}
                    />
                  </Badge>
                )}
              </div>
            )}

            {isLoading ? (
              <div
                className={
                  viewMode === "grid"
                    ? "grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
                    : "space-y-4"
                }
              >
                {[...Array(6)].map((_, i) => (
                  <PropertyCardSkeleton key={i} />
                ))}
              </div>
            ) : properties && properties.length > 0 ? (
              <div
                className={
                  viewMode === "grid"
                    ? "grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"
                    : "space-y-4"
                }
              >
                {properties.map((property) => (
                  <PropertyCard key={property.id} property={property} />
                ))}
              </div>
            ) : (
              <div className="text-center py-16 bg-muted/30 rounded-xl">
                <Building2 className="w-16 h-16 mx-auto text-muted-foreground mb-4" />
                <h3 className="text-xl font-semibold mb-2">
                  No se encontraron propiedades
                </h3>
                <p className="text-muted-foreground mb-6">
                  Intenta ajustar los filtros de búsqueda
                </p>
                <Button onClick={clearFilters} data-testid="button-reset-search">
                  Limpiar filtros
                </Button>
              </div>
            )}
          </main>
        </div>
      </div>
    </div>
  );
}
