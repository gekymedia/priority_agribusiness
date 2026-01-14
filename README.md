# Priority Agribusiness Management System

This project is a Laravel 12 based application designed to help Priority Solutions Agency – Directorate of Agriculture manage both poultry and crop farming operations.  It includes modules for managing farms, houses/pens, bird batches, crop fields, plantings, tasks and simple financial tracking.

## Requirements

* PHP 8.2 or higher
* Composer
* Node/NPM (for front‑end asset compilation if you wish to extend the UI)
* A MySQL (or MariaDB) database

## Installation

1. **Clone the repository** and install dependencies:

   ```bash
   git clone <repo-url> priority-agribusiness
   cd priority-agribusiness
   composer install
   ```

2. **Copy the environment file** and generate an application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure your database** settings in `.env` (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).  The default timezone is set to `Africa/Accra`.

4. **Run the database migrations** to create the required tables:

   ```bash
   php artisan migrate
   ```

5. **Run the development server**:

   ```bash
   php artisan serve
   ```

6. Open your browser at `http://localhost:8000`.  You should see the login screen.  Create a new account via the Register link.  The first user created becomes the administrator.

## Basic Usage

After logging in you will see a simple dashboard summarising your farms, bird batches, crop plantings and pending tasks.  Use the navigation bar to manage:

* **Farms** – create poultry or crop farms (or mixed).  Farms form the top‑level container for houses and fields.
* **Houses/Pens** – add pens for keeping bird batches.  Each house belongs to a farm.
* **Bird Batches** – record groups of birds.  Track their purpose (broiler/layer/breeder), arrival date, quantity, cost, etc.
* **Fields** – define crop plots on your farms.
* **Plantings** – log crop plantings on your fields.  Record crop name, planting date, expected harvest date, quantity planted and current status.
* **Tasks** – create reminders for vaccinations, medications, harvest dates or any other important milestones.  Tasks can be marked as done when completed.

The UI uses Blade templates with Bootstrap 5 to provide a simple, clean experience.  Feel free to extend the controllers, models and views to add more functionality such as recording daily bird batch metrics, medication/vaccination schedules, egg and bird sales, crop inputs and harvests.

## Default Login

This application does not ship with pre‑configured user credentials.  Use the **Register** link on the login page to create your first user.  You can add a `role` to a user via the database (e.g. `admin`, `manager`, `worker`, `viewer`) to implement role‑based permissions.

## Timezone

The application is configured to use the `Africa/Accra` timezone.  This ensures that all dates and times are stored and displayed correctly for Ghana.

## Contributing

Pull requests are welcome!  Feel free to submit issues and improvements.