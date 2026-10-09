import { useEffect, useState } from 'react';
import api from '@/services/api';

function Contracts() {
  const [contracts, setContracts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchContracts = async () => {
      try {
        const response = await api.get('/pcm/v1/contracts');
        setContracts(response.data.data || []);
      } catch (error) {
        console.error('Failed to fetch contracts:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchContracts();
  }, []);

  if (loading) {
    return <div className="p-6 text-center">بارگذاری...</div>;
  }

  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold text-gray-900 mb-6">قرارداد‌ها</h1>
      <div className="bg-white rounded-lg shadow-sm p-6">
        <p className="text-gray-500">تعداد قرارداد: {contracts.length}</p>
      </div>
    </div>
  );
}

export default Contracts;
