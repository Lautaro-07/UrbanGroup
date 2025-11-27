import { useQuery } from "@tanstack/react-query";
import { useLocation } from "wouter";
import { useEffect } from "react";
import type { User } from "@shared/schema";
import { getQueryFn } from "@/lib/queryClient";

export function useAuth() {
  const { data: user, isLoading, error } = useQuery<User | null>({
    queryKey: ["/api/auth/me"],
    queryFn: getQueryFn({ on401: "returnNull" }),
    staleTime: 60000,
    retry: false,
  });

  return {
    user,
    isLoading,
    isAuthenticated: !!user,
    isAdmin: user?.role === "admin",
    isPartner: user?.role === "partner",
    error,
  };
}

export function useRequireAuth(requiredRole?: "admin" | "partner") {
  const [, setLocation] = useLocation();
  const { user, isLoading, isAuthenticated } = useAuth();

  useEffect(() => {
    if (!isLoading && !isAuthenticated) {
      setLocation("/login");
    }
    
    if (!isLoading && isAuthenticated && requiredRole && user?.role !== requiredRole) {
      if (requiredRole === "admin" && user?.role !== "admin") {
        setLocation("/panel");
      }
    }
  }, [isLoading, isAuthenticated, user, requiredRole, setLocation]);

  return { user, isLoading };
}
