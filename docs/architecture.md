:root {
  font-family: 'Tahoma', 'Vazirmatn', sans-serif;
  color: #0f172a;
  background: #f3f6fb;
  line-height: 1.5;
  font-weight: 500;
  direction: rtl;
}

* {
  box-sizing: border-box;
}

html, body, #root {
  margin: 0;
  min-height: 100%;
  min-width: 100%;
}

body {
  min-height: 100vh;
  background: linear-gradient(135deg, #edf5ff 0%, #f9fafb 100%);
}

button {
  font: inherit;
}

.pcm-app {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 280px;
  background: #0f172a;
  color: white;
  padding: 24px 20px;
}

.brand {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 24px;
}

.nav-item {
  display: block;
  width: 100%;
  background: transparent;
  color: white;
  border: none;
  text-align: right;
  padding: 12px 14px;
  border-radius: 10px;
  cursor: pointer;
  margin: 6px 0;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.08);
}

.content {
  flex: 1;
  padding: 32px;
}

.topbar {
  margin-bottom: 24px;
}

.topbar h1 {
  margin: 0;
  font-size: 2rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.stat-card {
  background: rgba(255, 255, 255, 0.75);
  border: 1px solid #dfe7f4;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
}

.stat-card span {
  display: block;
  color: #475467;
  margin-bottom: 12px;
}

.stat-card strong {
  font-size: 1.6rem;
}

@media (max-width: 900px) {
  .pcm-app {
    flex-direction: column;
  }

  .sidebar {
    width: 100%;
  }
}
