import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { useEffect } from 'react';
import Layout from './components/Layout';
import Dashboard from './pages/Dashboard';
import Employees from './pages/Employees';
import Contracts from './pages/Contracts';
import Payroll from './pages/Payroll';
import Reports from './pages/Reports';
import Payments from './pages/Payments';
import Audit from './pages/Audit';
import { useAuthStore } from './stores/authStore';

function App() {
  const { verifyAuth } = useAuthStore();

  useEffect(() => {
    verifyAuth();
  }, [verifyAuth]);

  return (
    <Router>
      <Routes>
        <Route path="/" element={<Layout />}>
          <Route index element={<Dashboard />} />
          <Route path="employees" element={<Employees />} />
          <Route path="contracts" element={<Contracts />} />
          <Route path="payroll" element={<Payroll />} />
          <Route path="reports" element={<Reports />} />
          <Route path="payments" element={<Payments />} />
          <Route path="audit" element={<Audit />} />
        </Route>
      </Routes>
    </Router>
  );
}

export default App;
