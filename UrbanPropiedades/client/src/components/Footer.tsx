import { Link } from "wouter";
import { Building2, Mail, Phone, MapPin } from "lucide-react";
import { SiFacebook, SiInstagram, SiLinkedin, SiWhatsapp } from "react-icons/si";

export function Footer() {
  return (
    <footer className="bg-slate-900 text-slate-200" data-testid="footer">
      <div className="max-w-7xl mx-auto px-4 lg:px-8 py-12 lg:py-16">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
          <div className="space-y-4">
            <div className="flex items-center gap-2">
              <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                <Building2 className="w-6 h-6 text-primary-foreground" />
              </div>
              <span className="text-xl font-bold text-white">UrbanGroup</span>
            </div>
            <p className="text-slate-400 text-sm leading-relaxed">
              UrbanGroup es un equipo multidisciplinario con más de 15 años de
              experiencia en el mercado inmobiliario chileno. Transformamos el
              corretaje en un servicio profesional.
            </p>
            <div className="flex items-center gap-3 pt-2">
              <a
                href="https://facebook.com"
                target="_blank"
                rel="noopener noreferrer"
                className="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors"
                data-testid="link-facebook"
              >
                <SiFacebook className="w-4 h-4" />
              </a>
              <a
                href="https://instagram.com"
                target="_blank"
                rel="noopener noreferrer"
                className="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors"
                data-testid="link-instagram"
              >
                <SiInstagram className="w-4 h-4" />
              </a>
              <a
                href="https://linkedin.com"
                target="_blank"
                rel="noopener noreferrer"
                className="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors"
                data-testid="link-linkedin"
              >
                <SiLinkedin className="w-4 h-4" />
              </a>
              <a
                href="https://wa.me/56912345678"
                target="_blank"
                rel="noopener noreferrer"
                className="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors"
                data-testid="link-whatsapp"
              >
                <SiWhatsapp className="w-4 h-4" />
              </a>
            </div>
          </div>

          <div>
            <h3 className="text-white font-semibold mb-4">Enlaces Rápidos</h3>
            <ul className="space-y-3">
              <li>
                <Link
                  href="/"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-home"
                >
                  Inicio
                </Link>
              </li>
              <li>
                <Link
                  href="/propiedades"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-properties"
                >
                  Propiedades
                </Link>
              </li>
              <li>
                <Link
                  href="/propiedades?operacion=venta"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-sale"
                >
                  Propiedades en Venta
                </Link>
              </li>
              <li>
                <Link
                  href="/propiedades?operacion=arriendo"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-rent"
                >
                  Propiedades en Arriendo
                </Link>
              </li>
              <li>
                <Link
                  href="/nosotros"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-about"
                >
                  Sobre Nosotros
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="text-white font-semibold mb-4">Tipos de Propiedades</h3>
            <ul className="space-y-3">
              <li>
                <Link
                  href="/propiedades?tipo=casa"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-houses"
                >
                  Casas
                </Link>
              </li>
              <li>
                <Link
                  href="/propiedades?tipo=departamento"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-apartments"
                >
                  Departamentos
                </Link>
              </li>
              <li>
                <Link
                  href="/propiedades?tipo=oficina"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-offices"
                >
                  Oficinas
                </Link>
              </li>
              <li>
                <Link
                  href="/propiedades?tipo=local"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-commercial"
                >
                  Locales Comerciales
                </Link>
              </li>
              <li>
                <Link
                  href="/propiedades?tipo=bodega"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-warehouses"
                >
                  Bodegas
                </Link>
              </li>
              <li>
                <Link
                  href="/propiedades?tipo=terreno"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-footer-land"
                >
                  Terrenos
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="text-white font-semibold mb-4">Contacto</h3>
            <ul className="space-y-4">
              <li className="flex items-start gap-3">
                <MapPin className="w-5 h-5 text-primary mt-0.5 shrink-0" />
                <span className="text-slate-400 text-sm">
                  Av. Apoquindo 4700, Of. 1802
                  <br />
                  Las Condes, Santiago, Chile
                </span>
              </li>
              <li className="flex items-center gap-3">
                <Phone className="w-5 h-5 text-primary shrink-0" />
                <a
                  href="tel:+56912345678"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-phone"
                >
                  +56 9 1234 5678
                </a>
              </li>
              <li className="flex items-center gap-3">
                <Mail className="w-5 h-5 text-primary shrink-0" />
                <a
                  href="mailto:contacto@urbangroup.cl"
                  className="text-slate-400 hover:text-white transition-colors text-sm"
                  data-testid="link-email"
                >
                  contacto@urbangroup.cl
                </a>
              </li>
            </ul>
          </div>
        </div>

        <div className="border-t border-slate-800 mt-10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
          <p className="text-slate-500 text-sm">
            © {new Date().getFullYear()} UrbanGroup SpA - Todos los derechos
            reservados.
          </p>
          <div className="flex items-center gap-6 text-sm">
            <Link
              href="/terminos"
              className="text-slate-500 hover:text-white transition-colors"
              data-testid="link-terms"
            >
              Términos y Condiciones
            </Link>
            <Link
              href="/privacidad"
              className="text-slate-500 hover:text-white transition-colors"
              data-testid="link-privacy"
            >
              Política de Privacidad
            </Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
