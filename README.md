# Project BookingOnline Installation Guide for Laravel

## System Requirements
- PHP >= 8.1
- Composer
- MySQL or PostgreSQL
- Node.js & NPM/Yarn

## Step 1: Clone the Repository
First, clone the repository from GitHub:

```bash
git clone https://github.com/TanNguyen01/BookingOnline.git
cd BookingOnline
```

## Step 2: Install Composer Packages
Next, install the required packages via Composer:

```bash
composer install
```

## Step 3: Install Node.js Packages
Install the necessary JavaScript packages:

```bash
npm install
# or if you use Yarn
yarn install
```

## Step 4: Configure Environment
Copy the .env.example file to .env:

```bash
cp .env.example .env
```

Edit the .env file to configure the necessary settings (database, mail, etc.).

## Step 5: Generate Application Key
Generate the application key:
```bash
php artisan key:generate
```

## Step 6: Set Up Database
Run the migrate and seed commands to create and initialize the database:

```bash
php artisan migrate

php artisan db:seed
```

## Step 7: Build Front-end Assets
Build the CSS and JavaScript files:

```bash
npm run dev
# or if you use Yarn
yarn dev
```

## Step 8: Run the Application
Start the Laravel development server:

```bash
php artisan serve
```

The application will run at http://localhost:8000.

## Step 9: Useful Commands
- Run tests: `php artisan test`
- Clear cache: `php artisan cache:clear`
- Optimize: `php artisan optimize`
## Notes
- Ensure you have correctly configured the settings in the .env file, especially the database configuration.
- Regularly check and update the Composer and npm/yarn packages to ensure you are using the latest versions.

Good luck with your installation and enjoy working with the project!
