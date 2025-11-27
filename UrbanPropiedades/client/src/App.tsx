import { Switch, Route } from "wouter";
import { queryClient } from "./lib/queryClient";
import { QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "@/components/ui/toaster";
import { TooltipProvider } from "@/components/ui/tooltip";
import { Navbar } from "@/components/Navbar";
import { Footer } from "@/components/Footer";
import Home from "@/pages/Home";
import Properties from "@/pages/Properties";
import PropertyDetail from "@/pages/PropertyDetail";
import About from "@/pages/About";
import Login from "@/pages/Login";
import AdminDashboard from "@/pages/AdminDashboard";
import PartnerDashboard from "@/pages/PartnerDashboard";
import NotFound from "@/pages/not-found";

function Router() {
  return (
    <Switch>
      <Route path="/" component={Home} />
      <Route path="/propiedades" component={Properties} />
      <Route path="/propiedad/:id" component={PropertyDetail} />
      <Route path="/nosotros" component={About} />
      <Route path="/login" component={Login} />
      <Route path="/admin" component={AdminDashboard} />
      <Route path="/panel" component={PartnerDashboard} />
      <Route component={NotFound} />
    </Switch>
  );
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <TooltipProvider>
        <div className="flex flex-col min-h-screen">
          <Navbar />
          <main className="flex-1">
            <Router />
          </main>
          <Footer />
        </div>
        <Toaster />
      </TooltipProvider>
    </QueryClientProvider>
  );
}

export default App;
