import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { useLocation } from "wouter";
import { z } from "zod";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { useToast } from "@/hooks/use-toast";
import { Building2, LogIn, Loader2, Eye, EyeOff } from "lucide-react";
import { apiRequest } from "@/lib/queryClient";

const loginSchema = z.object({
  username: z.string().min(1, "Ingrese su usuario"),
  password: z.string().min(1, "Ingrese su contraseña"),
});

type LoginFormData = z.infer<typeof loginSchema>;

export default function Login() {
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const [showPassword, setShowPassword] = useState(false);

  const form = useForm<LoginFormData>({
    resolver: zodResolver(loginSchema),
    defaultValues: {
      username: "",
      password: "",
    },
  });

  const loginMutation = useMutation({
    mutationFn: async (data: LoginFormData) => {
      const response = await apiRequest("POST", "/api/auth/login", data);
      return response;
    },
    onSuccess: (data: any) => {
      toast({
        title: "Bienvenido",
        description: `Has iniciado sesión exitosamente`,
      });
      if (data.user.role === "admin") {
        setLocation("/admin");
      } else {
        setLocation("/panel");
      }
    },
    onError: (error: any) => {
      toast({
        variant: "destructive",
        title: "Error",
        description: error.message || "Usuario o contraseña incorrectos",
      });
    },
  });

  const onSubmit = (data: LoginFormData) => {
    loginMutation.mutate(data);
  };

  return (
    <div
      className="min-h-screen pt-20 bg-background flex items-center justify-center px-4"
      data-testid="page-login"
    >
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <div className="w-16 h-16 bg-primary rounded-xl flex items-center justify-center mx-auto mb-4">
            <Building2 className="w-8 h-8 text-primary-foreground" />
          </div>
          <h1 className="text-2xl font-bold">Acceso para Socios</h1>
          <p className="text-muted-foreground mt-2">
            Ingresa a tu cuenta para gestionar tus propiedades
          </p>
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="text-center">Iniciar Sesión</CardTitle>
          </CardHeader>
          <CardContent>
            <Form {...form}>
              <form
                onSubmit={form.handleSubmit(onSubmit)}
                className="space-y-4"
              >
                <FormField
                  control={form.control}
                  name="username"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>Usuario</FormLabel>
                      <FormControl>
                        <Input
                          placeholder="Tu nombre de usuario"
                          autoComplete="username"
                          data-testid="input-username"
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
                      <FormLabel>Contraseña</FormLabel>
                      <FormControl>
                        <div className="relative">
                          <Input
                            type={showPassword ? "text" : "password"}
                            placeholder="Tu contraseña"
                            autoComplete="current-password"
                            data-testid="input-password"
                            {...field}
                          />
                          <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            className="absolute right-0 top-0 h-full px-3"
                            onClick={() => setShowPassword(!showPassword)}
                            data-testid="button-toggle-password"
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

                <Button
                  type="submit"
                  className="w-full"
                  size="lg"
                  disabled={loginMutation.isPending}
                  data-testid="button-submit-login"
                >
                  {loginMutation.isPending ? (
                    <>
                      <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                      Ingresando...
                    </>
                  ) : (
                    <>
                      <LogIn className="w-4 h-4 mr-2" />
                      Ingresar
                    </>
                  )}
                </Button>
              </form>
            </Form>

            <div className="mt-6 pt-6 border-t text-center">
              <p className="text-sm text-muted-foreground">
                ¿No tienes cuenta?{" "}
                <a
                  href="mailto:contacto@urbangroup.cl"
                  className="text-primary hover:underline"
                >
                  Contacta al administrador
                </a>
              </p>
            </div>
          </CardContent>
        </Card>

        <div className="mt-6 p-4 bg-muted/50 rounded-lg">
          <p className="text-sm text-muted-foreground text-center">
            <strong>Demo:</strong> admin / admin123 (Administrador)
            <br />
            socio1 / socio123 (Socio)
          </p>
        </div>
      </div>
    </div>
  );
}
