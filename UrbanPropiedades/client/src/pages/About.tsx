import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import {
  Building2,
  Users,
  Award,
  Target,
  Eye,
  CheckCircle,
  Phone,
  Mail,
  MapPin,
  Clock,
} from "lucide-react";
import { SiWhatsapp, SiFacebook, SiInstagram, SiLinkedin } from "react-icons/si";

export default function About() {
  const team = [
    {
      name: "Carlos Rodríguez",
      role: "Director General",
      image:
        "https://images.unsplash.com/photo-1560250097-0b93528c311a?w=300&h=300&fit=crop",
    },
    {
      name: "María González",
      role: "Gerente Comercial",
      image:
        "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=300&h=300&fit=crop",
    },
    {
      name: "Andrés Silva",
      role: "Arquitecto Jefe",
      image:
        "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=300&h=300&fit=crop",
    },
  ];

  const values = [
    {
      icon: CheckCircle,
      title: "Profesionalismo",
      description:
        "Nos enfocamos en el resultado final de cada operación con un seguimiento exhaustivo.",
    },
    {
      icon: Users,
      title: "Trabajo en Equipo",
      description:
        "Un equipo multidisciplinario de arquitectos, abogados y corredores expertos.",
    },
    {
      icon: Award,
      title: "Excelencia",
      description:
        "Más de 15 años transformando el corretaje en un servicio de alta calidad.",
    },
    {
      icon: Target,
      title: "Resultados",
      description:
        "98% de éxito en nuestras operaciones inmobiliarias a nivel nacional.",
    },
  ];

  return (
    <div className="min-h-screen pt-20 bg-background" data-testid="page-about">
      <section className="relative py-20 lg:py-28 overflow-hidden">
        <div
          className="absolute inset-0 bg-cover bg-center"
          style={{
            backgroundImage:
              "url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&h=800&fit=crop')",
          }}
        >
          <div className="absolute inset-0 bg-gradient-to-r from-slate-900/95 to-slate-900/70" />
        </div>

        <div className="relative max-w-7xl mx-auto px-4 lg:px-8">
          <div className="max-w-2xl">
            <h1 className="text-4xl lg:text-5xl font-bold text-white mb-6">
              Sobre Nosotros
            </h1>
            <p className="text-xl text-white/80 leading-relaxed">
              Somos un equipo multidisciplinario con más de 15 años de
              experiencia transformando el corretaje de propiedades en un
              servicio profesional de excelencia.
            </p>
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl lg:text-4xl font-bold mb-6">
                ¿Quiénes Somos?
              </h2>
              <div className="space-y-4 text-muted-foreground leading-relaxed">
                <p>
                  <strong className="text-foreground">Urban Group</strong> es un
                  equipo multidisciplinario formado por Arquitectos, Abogados y
                  una extensa Red de Corredores de Propiedades con años de
                  experiencia en el mercado.
                </p>
                <p>
                  Con más de{" "}
                  <strong className="text-foreground">15 años</strong> en el
                  mercado, hemos transformado el corretaje de propiedades en un
                  servicio profesional, logrando el éxito en cada compraventa
                  inmobiliaria.
                </p>
                <p>
                  En <strong className="text-foreground">Urban Group</strong>{" "}
                  nos enfocamos en el resultado final de cada operación.
                  Mediante un exhaustivo seguimiento del proceso de compraventa,
                  atendemos los detalles de forma Pro Activa, de manera de
                  anticipar las dificultades y posibles demoras, Gestionando las
                  Soluciones Óptimas, para el cumplimiento de cada etapa, dentro
                  de los plazos acordados.
                </p>
                <p className="font-medium text-foreground">
                  Nuestra Principal Prioridad es la Satisfacción de cada
                  Vendedor y cada Comprador, durante TODO el Proceso de
                  Compraventa.
                </p>
              </div>
            </div>
            <div className="relative">
              <img
                src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?w=600&h=500&fit=crop"
                alt="Oficina UrbanGroup"
                className="rounded-xl shadow-lg w-full"
              />
              <div className="absolute -bottom-6 -right-6 bg-primary text-primary-foreground p-6 rounded-xl shadow-lg hidden lg:block">
                <div className="text-4xl font-bold">15+</div>
                <div className="text-sm">Años de experiencia</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20 bg-muted/30">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12">
            <Card className="overflow-hidden">
              <CardContent className="p-8">
                <div className="flex items-center gap-4 mb-6">
                  <div className="w-14 h-14 rounded-lg bg-primary/10 flex items-center justify-center">
                    <Target className="w-7 h-7 text-primary" />
                  </div>
                  <h3 className="text-2xl font-bold">Misión</h3>
                </div>
                <p className="text-muted-foreground leading-relaxed">
                  Brindar un servicio inmobiliario integral y profesional,
                  enfocado en satisfacer las necesidades de nuestros clientes
                  mediante la gestión eficiente de compraventa y arriendo de
                  propiedades, garantizando transparencia, confianza y
                  resultados exitosos en cada operación.
                </p>
              </CardContent>
            </Card>

            <Card className="overflow-hidden">
              <CardContent className="p-8">
                <div className="flex items-center gap-4 mb-6">
                  <div className="w-14 h-14 rounded-lg bg-primary/10 flex items-center justify-center">
                    <Eye className="w-7 h-7 text-primary" />
                  </div>
                  <h3 className="text-2xl font-bold">Visión</h3>
                </div>
                <p className="text-muted-foreground leading-relaxed">
                  Ser reconocidos como el portal inmobiliario líder en Chile,
                  destacando por nuestra excelencia en el servicio, innovación
                  tecnológica y compromiso con la satisfacción total de
                  vendedores y compradores en el mercado inmobiliario nacional.
                </p>
              </CardContent>
            </Card>
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl lg:text-4xl font-bold mb-4">
              Nuestros Valores
            </h2>
            <p className="text-muted-foreground max-w-2xl mx-auto">
              Los principios que guían cada una de nuestras acciones
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {values.map((value) => {
              const Icon = value.icon;
              return (
                <Card key={value.title} className="text-center">
                  <CardContent className="p-6">
                    <div className="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4">
                      <Icon className="w-7 h-7 text-primary" />
                    </div>
                    <h3 className="font-semibold text-lg mb-2">{value.title}</h3>
                    <p className="text-sm text-muted-foreground">
                      {value.description}
                    </p>
                  </CardContent>
                </Card>
              );
            })}
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20 bg-primary text-primary-foreground">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            <div>
              <div className="text-4xl lg:text-5xl font-bold mb-2">500+</div>
              <div className="text-primary-foreground/80">
                Propiedades gestionadas
              </div>
            </div>
            <div>
              <div className="text-4xl lg:text-5xl font-bold mb-2">15+</div>
              <div className="text-primary-foreground/80">
                Años de experiencia
              </div>
            </div>
            <div>
              <div className="text-4xl lg:text-5xl font-bold mb-2">1000+</div>
              <div className="text-primary-foreground/80">
                Clientes satisfechos
              </div>
            </div>
            <div>
              <div className="text-4xl lg:text-5xl font-bold mb-2">98%</div>
              <div className="text-primary-foreground/80">
                Operaciones exitosas
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl lg:text-4xl font-bold mb-4">
              Nuestro Equipo
            </h2>
            <p className="text-muted-foreground max-w-2xl mx-auto">
              Profesionales comprometidos con tu éxito inmobiliario
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8">
            {team.map((member) => (
              <Card key={member.name} className="overflow-hidden">
                <div className="aspect-square overflow-hidden">
                  <img
                    src={member.image}
                    alt={member.name}
                    className="w-full h-full object-cover"
                  />
                </div>
                <CardContent className="p-6 text-center">
                  <h3 className="font-semibold text-lg">{member.name}</h3>
                  <p className="text-muted-foreground">{member.role}</p>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16 lg:py-20 bg-muted/30">
        <div className="max-w-7xl mx-auto px-4 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12">
            <div>
              <h2 className="text-3xl lg:text-4xl font-bold mb-6">Contáctanos</h2>
              <p className="text-muted-foreground mb-8">
                Estamos aquí para ayudarte con todas tus necesidades
                inmobiliarias. No dudes en contactarnos.
              </p>

              <div className="space-y-6">
                <div className="flex items-start gap-4">
                  <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <MapPin className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h3 className="font-semibold mb-1">Dirección</h3>
                    <p className="text-muted-foreground">
                      Av. Apoquindo 4700, Of. 1802
                      <br />
                      Las Condes, Santiago, Chile
                    </p>
                  </div>
                </div>

                <div className="flex items-start gap-4">
                  <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <Phone className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h3 className="font-semibold mb-1">Teléfono</h3>
                    <a
                      href="tel:+56912345678"
                      className="text-muted-foreground hover:text-primary"
                    >
                      +56 9 1234 5678
                    </a>
                  </div>
                </div>

                <div className="flex items-start gap-4">
                  <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <Mail className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h3 className="font-semibold mb-1">Email</h3>
                    <a
                      href="mailto:contacto@urbangroup.cl"
                      className="text-muted-foreground hover:text-primary"
                    >
                      contacto@urbangroup.cl
                    </a>
                  </div>
                </div>

                <div className="flex items-start gap-4">
                  <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <Clock className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h3 className="font-semibold mb-1">Horario de Atención</h3>
                    <p className="text-muted-foreground">
                      Lunes a Viernes: 9:00 - 18:00
                      <br />
                      Sábado: 10:00 - 14:00
                    </p>
                  </div>
                </div>
              </div>

              <div className="mt-8">
                <h3 className="font-semibold mb-4">Síguenos</h3>
                <div className="flex gap-3">
                  <a
                    href="https://facebook.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center hover:bg-primary hover:text-primary-foreground transition-colors"
                    data-testid="link-about-facebook"
                  >
                    <SiFacebook className="w-5 h-5" />
                  </a>
                  <a
                    href="https://instagram.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center hover:bg-primary hover:text-primary-foreground transition-colors"
                    data-testid="link-about-instagram"
                  >
                    <SiInstagram className="w-5 h-5" />
                  </a>
                  <a
                    href="https://linkedin.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center hover:bg-primary hover:text-primary-foreground transition-colors"
                    data-testid="link-about-linkedin"
                  >
                    <SiLinkedin className="w-5 h-5" />
                  </a>
                  <a
                    href="https://wa.me/56912345678"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center hover:bg-primary hover:text-primary-foreground transition-colors"
                    data-testid="link-about-whatsapp"
                  >
                    <SiWhatsapp className="w-5 h-5" />
                  </a>
                </div>
              </div>
            </div>

            <div className="rounded-xl overflow-hidden h-[400px] lg:h-auto">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3330.4!2d-70.57!3d-33.41!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzPCsDI0JzM2LjAiUyA3MMKwMzQnMTIuMCJX!5e0!3m2!1ses!2scl!4v1234567890"
                width="100%"
                height="100%"
                style={{ border: 0, minHeight: "400px" }}
                allowFullScreen
                loading="lazy"
                referrerPolicy="no-referrer-when-downgrade"
                title="Ubicación UrbanGroup"
              />
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
