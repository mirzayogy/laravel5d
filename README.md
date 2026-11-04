
# ProtoLog

**Engineering Experiment Journal for Robotics, Embedded Systems, and IoT.**

ProtoLog is a Laravel-based application prototype designed to help engineers and experimenters document their development projects, manage component inventories, record experiments, and organize measurement results.

The system provides a structured workspace for documenting engineering activities, from project planning and component usage to experiment execution and result reporting.

## Project Overview

ProtoLog focuses on organizing engineering experiments through connected database records.

Its main capabilities are:

- **Project Management** — organize engineering projects by domain, objective, and status.
- **Component Inventory** — record reusable electronic components, modules, and hardware specifications.
- **Experiment Management** — document experiment objectives, hypotheses, procedures, and notes.
- **Component Tracking** — record which components are used in each experiment, including their roles and connection details.
- **Experiment Runs** — record individual experiment executions, firmware versions, and environmental conditions.
- **Measurement Recording** — define measurement parameters and store sample results with their units and timestamps.
- **Experiment Reports** — organize experiment summaries, conclusions, limitations, and recommendations.

## Technology Stack

- **Backend:** Laravel
- **Programming Language:** PHP
- **Database:** MySQL
- **ORM:** Laravel Eloquent
- **Frontend Tooling:** Vite and npm

## Installation and Setup

### 1. Clone the repository

```bash
git clone <repository-url>
cd <repository-folder>
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure environment

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

For macOS or Linux:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Configure the database

Create a MySQL database, then update the database configuration in `.env`.

Example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=protolog
DB_USERNAME=root
DB_PASSWORD=
```

Adjust the database credentials according to your local environment.

### 5. Run migrations and seeders

```bash
php artisan migrate --seed
```

This command creates the database tables and executes the registered seeders:

- `PeriodeSeeder` — existing application seed data.
- `ProtoLogSeeder` — sample data for ProtoLog.

To execute the ProtoLog seeder separately:

```bash
php artisan db:seed --class=ProtoLogSeeder
```

### 6. Run the application

Start the Vite development server:

```bash
npm run dev
```

In another terminal, start Laravel:

```bash
php artisan serve
```

Open the local address displayed by Laravel, usually:

`http://127.0.0.1:8000`

## Database Structure

ProtoLog introduces eight domain tables:

| Table | Description |
|---|---|
| `projects` | Stores project information and ownership. |
| `components` | Stores the user's component inventory. |
| `experiments` | Stores experiments belonging to projects. |
| `experiment_components` | Records component usage within experiments. |
| `experiment_runs` | Stores individual experiment executions. |
| `measurement_parameters` | Defines the parameters measured in experiments. |
| `measurement_records` | Stores measurement samples and results. |
| `experiment_reports` | Stores experiment summaries and conclusions. |

Existing Laravel tables and the `periode` table are retained.

### Entity Relationships

The database uses the following Eloquent relationships:

- **User → Project:** One-to-Many.
- **User → Component:** One-to-Many.
- **Project → Experiment:** One-to-Many.
- **Project → ExperimentRun:** Has-Many-Through.
- **Experiment ↔ Component:** Many-to-Many through `experiment_components`.
- **Experiment → ExperimentRun:** One-to-Many.
- **Experiment → MeasurementParameter:** One-to-Many.
- **Experiment → ExperimentReport:** One-to-One.
- **ExperimentRun → MeasurementRecord:** One-to-Many.
- **MeasurementParameter → MeasurementRecord:** One-to-Many.
- **Component → MeasurementParameter:** One-to-Many through the optional `source_component_id` reference.

The complete entity relationship diagram is available at:

[`docs/database/erd.md`](docs/database/erd.md)

## Database Integrity

The database implements:

- Foreign key constraints to maintain referential integrity.
- Cascading deletion for dependent records where appropriate.
- Restricted deletion for components referenced by experiment usage or measurement parameters.
- Unique constraints for experiment-component associations.
- Unique experiment run numbers within each experiment.
- Unique measurement samples for each run and parameter combination.
- A unique experiment reference for each report.

Some cross-table business rules, such as verifying that a component belongs to the same user as its project's owner, require additional application-layer validation.

## Sample Data

`ProtoLogSeeder` provides repeatable sample data for database development and relationship verification.

The sample dataset includes:

- 1 demo user.
- 1 engineering project.
- 2 inventory components.
- 1 experiment.
- 2 experiment-component associations.
- 1 experiment run.
- 2 measurement parameters.
- 2 measurement records.
- 1 experiment report.

The sample measurement values are demonstration data, not results from an actual physical experiment.

The demo user is created with:

- Email: `demo@protolog.local`
- Development password: `password`

Do not use these demo credentials in a production environment.

## Project Documentation

| Document | Location |
|---|---|
| Entity Relationship Diagram | `docs/database/erd.md` |
| Assignment Progress | `docs/progress/P01-database-design.md` |
| Database Migrations | `database/migrations/` |
| Eloquent Models | `app/Models/` |
| Database Seeders | `database/seeders/` |

## Current Development Scope

This phase focuses on database design, migrations, Eloquent models, relationships, and sample data.

Actual hardware integration, automated sensor data collection, and application-level experiment management features are outside the scope of this database foundation phase.

---

**ProtoLog** — A structured journal for engineering experiments.
