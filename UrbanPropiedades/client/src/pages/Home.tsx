import { useQuery } from "@tanstack/react-query";
import { Link } from "wouter";
import { Button } from "@/components/ui/button";
import { PropertyCard, PropertyCardSkeleton } from "@/components/PropertyCard";
import { SearchForm } from "@/components/SearchForm";
import {
  Building2,
  Home as HomeIcon,
  Building,
  Warehouse,
  MapPin,
  ArrowRight,
  Users,
  Award,
  Clock,
  CheckCircle,
} from "lucide-react";
import type { Property } from "@shared/schema";

export default function Home() {
  const { data: featuredProperties, isLoading } = useQuery<Property[]>({
    queryKey: ["/api/properties/featured"],
  });

  const propertyTypeLinks = [
    { icon: HomeIcon, label: "Casas", href: "/propiedades?tipo=casa" },
    { icon: Building, label: "Departamentos", href: "/propiedades?tipo=departamento" },
    { icon: Building2, label: "Oficinas", href: "/propiedades?tipo=oficina" },
    { icon: Warehouse, label: "Bodegas", href: "/propiedades?tipo=bodega" },
  ];

  const stats = [
    { icon: Building2, value: "500+", label: "Propiedades" },
    { icon: Users, value: "15+", label: "Años de experiencia" },
    { icon: Award, value: "1000+", label: "Clientes satisfechos" },
    { icon: CheckCircle, value: "98%", label: "Éxito en operaciones" },
  ];

  return (
    <div className="min-h-screen" data-testid="page-home">
      <section className="relative h-[600px] lg:h-[700px] flex items-center justify-center">
        <div
          className="absolute inset-0 bg-cover bg-center"
          style={{
            backgroundImage:
              "url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&h=1080&fit=crop')",
          }}
        >
          <div className="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70" />
        </div>

        <div className="relative z-10 w-full max-w-7xl mx-auto px-4 lg:px-8 text-center">
          <h1 className="text-4xl lg:text-6xl font-bold text-white mb-4 leading-tight">
            Encuentra tu propiedad ideal
            <br />
            <span className="text-primary">en Chile</span>
          </h1>
          <p className="text-lg lg:text-xl text-white/80 mb-8 max-w-2xl mx-auto">
            Más de 15 años de experiencia transformando el corretaje de
            propiedades en un servicio profesional. Casas, departamentos,
            oficinas y más.
          </p>

          <SearchForm variant="hero" />
        </div>
      </section>

      <section className="py-8 bg-card border-b">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
            {propertyTypeLinks.map((item) => {
              const Icon = item.icon;
              return (
                <Link key={item.label} href={item.href}>
                  <div
                    className="flex items-center gap-3 p-4 rounded-lg hover-elevate cursor-pointer bg-background border"
                    data-testid={`link-type-${item.label.toLowerCase()}`}
                  >
                    <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                      <Icon className="w-6 h-6 text-primary" />
                    </div>
                    <span className="font-medium">{item.label}</span>
                  </div>
                </Link>
              );
            })}
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20 bg-background">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-10">
            <div>
              <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-2">
                Propiedades Destacadas
              </h2>
              <p className="text-muted-foreground">
                Descubre las mejores oportunidades inmobiliarias seleccionadas
                para ti
              </p>
            </div>
            <Link href="/propiedades">
              <Button variant="outline" data-testid="button-view-all">
                Ver todas las propiedades
                <ArrowRight className="w-4 h-4 ml-2" />
              </Button>
            </Link>
          </div>

          {isLoading ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {[...Array(8)].map((_, i) => (
                <PropertyCardSkeleton key={i} />
              ))}
            </div>
          ) : featuredProperties && featuredProperties.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {featuredProperties.map((property) => (
                <PropertyCard key={property.id} property={property} />
              ))}
            </div>
          ) : (
            <div className="text-center py-12 bg-muted/30 rounded-xl">
              <Building2 className="w-16 h-16 mx-auto text-muted-foreground mb-4" />
              <h3 className="text-xl font-semibold mb-2">
                No hay propiedades destacadas
              </h3>
              <p className="text-muted-foreground mb-4">
                Pronto agregaremos propiedades destacadas para ti
              </p>
              <Link href="/propiedades">
                <Button data-testid="button-explore-all">
                  Explorar todas las propiedades
                </Button>
              </Link>
            </div>
          )}
        </div>
      </section>

      <section className="py-16 lg:py-20 bg-primary text-primary-foreground">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
            {stats.map((stat) => {
              const Icon = stat.icon;
              return (
                <div key={stat.label} className="text-center">
                  <div className="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center mx-auto mb-4">
                    <Icon className="w-8 h-8" />
                  </div>
                  <div className="text-3xl lg:text-4xl font-bold mb-2">
                    {stat.value}
                  </div>
                  <div className="text-primary-foreground/80">{stat.label}</div>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20 bg-background">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-6">
                ¿Quiénes Somos?
              </h2>
              <p className="text-muted-foreground mb-6 leading-relaxed">
                <strong className="text-foreground">Urban Group</strong> es un
                equipo multidisciplinario formado por Arquitectos, Abogados y
                una extensa Red de Corredores de Propiedades con años de
                experiencia en el mercado.
              </p>
              <p className="text-muted-foreground mb-6 leading-relaxed">
                Con más de <strong className="text-foreground">15 años</strong>{" "}
                en el mercado, hemos transformado el corretaje de propiedades en
                un servicio profesional, logrando el éxito en cada compraventa
                inmobiliaria.
              </p>
              <p className="text-muted-foreground mb-8 leading-relaxed">
                Nuestra Principal Prioridad es la Satisfacción de cada Vendedor
                y cada Comprador, durante TODO el Proceso de Compraventa.
              </p>
              <Link href="/nosotros">
                <Button size="lg" data-testid="button-about-more">
                  Conocer más sobre nosotros
                  <ArrowRight className="w-4 h-4 ml-2" />
                </Button>
              </Link>
            </div>
            <div className="relative">
              <img
                src="https://images.unsplash.com/photo-1560520031-3a4dc4e9de0c?w=800&h=600&fit=crop"
                alt="Equipo UrbanGroup"
                className="rounded-xl shadow-lg w-full"
              />
              <div className="absolute -bottom-6 -left-6 bg-primary text-primary-foreground p-6 rounded-xl shadow-lg">
                <div className="text-4xl font-bold">15+</div>
                <div className="text-sm">Años de experiencia</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20 bg-muted/30">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl lg:text-4xl font-bold text-foreground mb-4">
              Nuestros Servicios
            </h2>
            <p className="text-muted-foreground max-w-2xl mx-auto">
              Ofrecemos soluciones integrales para todas tus necesidades
              inmobiliarias
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8">
            <div className="bg-card p-8 rounded-xl border">
              <div className="w-14 h-14 rounded-lg bg-primary/10 flex items-center justify-center mb-6">
                <MapPin className="w-7 h-7 text-primary" />
              </div>
              <h3 className="text-xl font-semibold mb-3">Búsqueda de Terrenos</h3>
              <p className="text-muted-foreground">
                Buscamos Terrenos en todo Chile que permitan la Edificación de
                Viviendas, Oficinas, Hoteles, Locales y más.
              </p>
            </div>

            <div className="bg-card p-8 rounded-xl border">
              <div className="w-14 h-14 rounded-lg bg-primary/10 flex items-center justify-center mb-6">
                <Building2 className="w-7 h-7 text-primary" />
              </div>
              <h3 className="text-xl font-semibold mb-3">Activos Inmobiliarios</h3>
              <p className="text-muted-foreground">
                Buscamos Activos Inmobiliarios en Venta o Propiedades con Renta
                en Venta para inversores.
              </p>
            </div>

            <div className="bg-card p-8 rounded-xl border">
              <div className="w-14 h-14 rounded-lg bg-primary/10 flex items-center justify-center mb-6">
                <Clock className="w-7 h-7 text-primary" />
              </div>
              <h3 className="text-xl font-semibold mb-3">Gestión de Ventas</h3>
              <p className="text-muted-foreground">
                Gestionamos la Venta y Arriendo de todo tipo de propiedades:
                Casas, Deptos, Oficinas, Bodegas y más.
              </p>
            </div>
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20 bg-slate-900 text-white">
        <div className="max-w-4xl mx-auto px-4 lg:px-8 text-center">
          <h2 className="text-3xl lg:text-4xl font-bold mb-6">
            ¿Tienes una propiedad para vender o arrendar?
          </h2>
          <p className="text-slate-300 mb-8 text-lg">
            Únete a nuestra red de socios y publica tus propiedades en el portal
            inmobiliario líder de Chile
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/login">
              <Button
                size="lg"
                className="bg-white text-slate-900 hover:bg-slate-100"
                data-testid="button-partner-login"
              >
                Ingresar como Socio
              </Button>
            </Link>
            <Link href="/nosotros">
              <Button
                size="lg"
                variant="outline"
                className="border-white text-white hover:bg-white/10"
                data-testid="button-contact-us"
              >
                Contáctanos
              </Button>
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
}
