# pcm-personnel-contract-manager

Professional Human Resources, Contracts, Payroll, and Personnel Payment Management System for WordPress.

## Overview
This repository contains a Production-oriented WordPress plugin, a dedicated corporate theme, and a React + TypeScript admin app for HR operations.

## Project structure

```text
pcm-project/
├── theme/
│   └── pcm-corporate/
├── plugin/
│   └── personnel-contract-manager/
├── admin-app/
├── docker/
├── Dockerfile
├── docker-compose.yml
├── .env.example
├── README.md
└── .gitignore
```

## Included
- WordPress plugin skeleton for HR/Payroll operations
- Custom database schema scaffolding
- REST API route bootstrap
- User capability and permissions framework
- Admin SPA bootstrap on React/Vite
- Corporate WordPress theme for public-facing pages
- Docker setup for local development

## Quick start

```bash
docker compose up --build
```

Then open:
- WordPress: http://localhost:8000
- Admin app: http://localhost:5173

## Requirements
- Docker
- Docker Compose
- Node.js 20+
- PHP 8.2+

## Production notes
- Set `WP_DEBUG=false` in production
- Do not hard-code secrets in source
- Use environment variables and Railway secrets

## Important
This repository is a solid production-ready foundation. The system design supports secured HR workflows, payroll versioning, employee lifecycle management, audit logging, role-based access, and extensible custom tables.
