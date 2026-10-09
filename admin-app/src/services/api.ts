import axios, { AxiosInstance } from 'axios';

const api: AxiosInstance = axios.create({
  baseURL: '/wp-json',
  timeout: 30000,
});

api.interceptors.request.use((config) => {
  const nonce = (window as any).pcmNonce;
  if (nonce) {
    config.headers['X-WP-Nonce'] = nonce;
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      window.location.href = '/wp-login.php';
    }
    return Promise.reject(error);
  }
);

export default api;
