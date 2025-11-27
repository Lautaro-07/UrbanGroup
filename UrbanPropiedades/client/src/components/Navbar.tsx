import { useState } from "react";
import { Link, useLocation } from "wouter";
import { Button } from "@/components/ui/button";
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet";
import { Menu, X, Home, Building2, Info, LogIn, User } from "lucide-react";

export function Navbar() {
  const [location] = useLocation();
  const [isOpen, setIsOpen] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false);

  useState(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  });

  const navLinks = [
    { href: "/", label: "Inicio", icon: Home },
    { href: "/propiedades", label: "Propiedades", icon: Building2 },
    { href: "/nosotros", label: "Nosotros", icon: Info },
  ];

  const isActive = (href: string) => {
    if (href === "/") return location === "/";
    return location.startsWith(href);
  };

  return (
    <header
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled || location !== "/"
          ? "bg-background/95 backdrop-blur-md border-b shadow-sm"
          : "bg-transparent"
      }`}
      data-testid="navbar"
    >
      <div className="max-w-7xl mx-auto px-4 lg:px-8">
        <div className="flex items-center justify-between h-20">
          <Link href="/" data-testid="link-home-logo">
            <div className="flex items-center gap-2 cursor-pointer">
              <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                <Building2 className="w-6 h-6 text-primary-foreground" />
              </div>
              <span
                className={`text-xl font-bold ${
                  isScrolled || location !== "/"
                    ? "text-foreground"
                    : "text-white"
                }`}
              >
                UrbanGroup
              </span>
            </div>
          </Link>

          <nav className="hidden md:flex items-center gap-1">
            {navLinks.map((link) => (
              <Link key={link.href} href={link.href}>
                <Button
                  variant="ghost"
                  className={`px-4 ${
                    isActive(link.href)
                      ? "bg-primary/10 text-primary"
                      : isScrolled || location !== "/"
                      ? "text-foreground hover:text-primary"
                      : "text-white/90 hover:text-white hover:bg-white/10"
                  }`}
                  data-testid={`link-nav-${link.label.toLowerCase()}`}
                >
                  {link.label}
                </Button>
              </Link>
            ))}
          </nav>

          <div className="hidden md:flex items-center gap-2">
            <Link href="/login">
              <Button
                variant={isScrolled || location !== "/" ? "outline" : "ghost"}
                className={
                  isScrolled || location !== "/"
                    ? ""
                    : "text-white border-white/30 hover:bg-white/10"
                }
                data-testid="button-login"
              >
                <LogIn className="w-4 h-4 mr-2" />
                Ingresar
              </Button>
            </Link>
          </div>

          <Sheet open={isOpen} onOpenChange={setIsOpen}>
            <SheetTrigger asChild className="md:hidden">
              <Button
                variant="ghost"
                size="icon"
                className={
                  isScrolled || location !== "/"
                    ? ""
                    : "text-white hover:bg-white/10"
                }
                data-testid="button-mobile-menu"
              >
                <Menu className="w-6 h-6" />
              </Button>
            </SheetTrigger>
            <SheetContent side="right" className="w-72">
              <div className="flex flex-col gap-6 mt-8">
                <div className="flex items-center gap-2 mb-4">
                  <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                    <Building2 className="w-6 h-6 text-primary-foreground" />
                  </div>
                  <span className="text-xl font-bold">UrbanGroup</span>
                </div>

                <nav className="flex flex-col gap-2">
                  {navLinks.map((link) => {
                    const Icon = link.icon;
                    return (
                      <Link key={link.href} href={link.href}>
                        <Button
                          variant={isActive(link.href) ? "secondary" : "ghost"}
                          className="w-full justify-start gap-3"
                          onClick={() => setIsOpen(false)}
                          data-testid={`link-mobile-${link.label.toLowerCase()}`}
                        >
                          <Icon className="w-5 h-5" />
                          {link.label}
                        </Button>
                      </Link>
                    );
                  })}
                </nav>

                <div className="border-t pt-4">
                  <Link href="/login">
                    <Button
                      className="w-full"
                      onClick={() => setIsOpen(false)}
                      data-testid="button-mobile-login"
                    >
                      <User className="w-4 h-4 mr-2" />
                      Ingresar como Socio
                    </Button>
                  </Link>
                </div>
              </div>
            </SheetContent>
          </Sheet>
        </div>
      </div>
    </header>
  );
}
