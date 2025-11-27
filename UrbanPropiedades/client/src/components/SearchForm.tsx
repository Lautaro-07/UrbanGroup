import { useState } from "react";
import { useLocation } from "wouter";
import { Button } from "@/components/ui/button";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Search } from "lucide-react";
import {
  propertyTypes,
  operationTypes,
  chileanRegions,
  comunasByRegion,
} from "@shared/schema";

interface SearchFormProps {
  variant?: "hero" | "inline";
  initialFilters?: {
    tipo?: string;
    operacion?: string;
    region?: string;
    comuna?: string;
  };
}

export function SearchForm({ variant = "hero", initialFilters }: SearchFormProps) {
  const [, setLocation] = useLocation();
  const [propertyType, setPropertyType] = useState(initialFilters?.tipo || "");
  const [operationType, setOperationType] = useState(
    initialFilters?.operacion || ""
  );
  const [region, setRegion] = useState(initialFilters?.region || "");
  const [comuna, setComuna] = useState(initialFilters?.comuna || "");

  const availableComunas = region ? comunasByRegion[region] || [] : [];

  const handleSearch = () => {
    const params = new URLSearchParams();
    if (propertyType) params.set("tipo", propertyType);
    if (operationType) params.set("operacion", operationType);
    if (region) params.set("region", region);
    if (comuna) params.set("comuna", comuna);

    setLocation(`/propiedades?${params.toString()}`);
  };

  const handleRegionChange = (value: string) => {
    setRegion(value);
    setComuna("");
  };

  if (variant === "hero") {
    return (
      <div
        className="bg-white/95 backdrop-blur-sm rounded-xl shadow-2xl p-6 lg:p-8 max-w-4xl mx-auto"
        data-testid="search-form-hero"
      >
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div className="space-y-2">
            <label className="text-sm font-medium text-slate-700">
              Tipo de Propiedad
            </label>
            <Select value={propertyType} onValueChange={setPropertyType}>
              <SelectTrigger
                className="w-full bg-white"
                data-testid="select-property-type"
              >
                <SelectValue placeholder="Seleccionar tipo" />
              </SelectTrigger>
              <SelectContent>
                {propertyTypes.map((type) => (
                  <SelectItem key={type.value} value={type.value}>
                    {type.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <label className="text-sm font-medium text-slate-700">
              Operación
            </label>
            <Select value={operationType} onValueChange={setOperationType}>
              <SelectTrigger
                className="w-full bg-white"
                data-testid="select-operation-type"
              >
                <SelectValue placeholder="Venta o Arriendo" />
              </SelectTrigger>
              <SelectContent>
                {operationTypes.map((op) => (
                  <SelectItem key={op.value} value={op.value}>
                    {op.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <label className="text-sm font-medium text-slate-700">Región</label>
            <Select value={region} onValueChange={handleRegionChange}>
              <SelectTrigger
                className="w-full bg-white"
                data-testid="select-region"
              >
                <SelectValue placeholder="Seleccionar región" />
              </SelectTrigger>
              <SelectContent>
                {chileanRegions.map((reg) => (
                  <SelectItem key={reg} value={reg}>
                    {reg}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <div className="space-y-2">
            <label className="text-sm font-medium text-slate-700">Comuna</label>
            <Select
              value={comuna}
              onValueChange={setComuna}
              disabled={!region}
            >
              <SelectTrigger
                className="w-full bg-white"
                data-testid="select-comuna"
              >
                <SelectValue
                  placeholder={region ? "Seleccionar comuna" : "Primero seleccione región"}
                />
              </SelectTrigger>
              <SelectContent>
                {availableComunas.map((com) => (
                  <SelectItem key={com} value={com}>
                    {com}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </div>

        <div className="mt-6 flex justify-center">
          <Button
            size="lg"
            className="px-12"
            onClick={handleSearch}
            data-testid="button-search"
          >
            <Search className="w-5 h-5 mr-2" />
            Buscar Propiedades
          </Button>
        </div>
      </div>
    );
  }

  return (
    <div
      className="bg-card border rounded-lg p-4"
      data-testid="search-form-inline"
    >
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <Select value={propertyType} onValueChange={setPropertyType}>
          <SelectTrigger data-testid="select-property-type-inline">
            <SelectValue placeholder="Tipo de propiedad" />
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

        <Select value={operationType} onValueChange={setOperationType}>
          <SelectTrigger data-testid="select-operation-type-inline">
            <SelectValue placeholder="Operación" />
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

        <Select value={region} onValueChange={handleRegionChange}>
          <SelectTrigger data-testid="select-region-inline">
            <SelectValue placeholder="Región" />
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

        <Select value={comuna} onValueChange={setComuna} disabled={!region}>
          <SelectTrigger data-testid="select-comuna-inline">
            <SelectValue placeholder="Comuna" />
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

        <Button onClick={handleSearch} data-testid="button-search-inline">
          <Search className="w-4 h-4 mr-2" />
          Buscar
        </Button>
      </div>
    </div>
  );
}
