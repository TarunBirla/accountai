# accountai

**AccountAI Practice OS** – UK Accountancy Lead & Practice Management System.

Built with Laravel 11, Blade, MySQL, and modern UK practice workflow management.

## Features
- **Public Lead Intake**: Multi-select business & personal services, anti-spam honeypot, rate limiting.
- **Dedicated Social Funnel**: High-converting `/social` landing page.
- **Client Portal**: Track onboarding milestones, requested services, and security profile.
- **Admin Dashboard**: Full CRM, lead lifecycle tracking, statutory reminders, services catalogue, and CSV export.
- **One-Hit Installer**: Automated database creation, migrations, seeders, and cache optimization at `/setup/run-all?key=ACCOUNT_AI_SECURE_INSTALL_KEY_2026`.

## Setup & Deployment
1. Configure database credentials in `.env`.
2. Visit `/setup/run-all?key=ACCOUNT_AI_SECURE_INSTALL_KEY_2026` in your browser.
3. Sign in to admin panel at `/login`:
   - **Email:** `admin@accountai.com`
   - **Password:** `admin@123`
