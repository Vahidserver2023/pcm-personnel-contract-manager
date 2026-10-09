# PCM Architecture

## Overview
This project is built as a production-ready WordPress-based HR system with three layers:

1. WordPress plugin for business logic and custom tables.
2. Dedicated theme for public-facing pages.
3. React + TypeScript SPA for admin operations.

## Security principles
- WordPress nonces for admin requests
- Capability checks and role separation
- Prepared SQL queries
- Output escaping and input sanitization
- Sensitive data masking and secure file rules

## Data model
Custom tables are created through `dbDelta()` and managed by a migration layer.

## Next phases
- Migrations and database seeding
- REST endpoints with validation
- Payroll engine and regulations versioning
- Audit logging
- Docker and deployment config
- End-to-end testing
