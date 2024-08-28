# ✨ GeniusAI-Powered Content Generation and Analysis Platform 🚀

Welcome to GeniusAI, an advanced AI-powered Software as a Service (SaaS) platform designed to revolutionize business operations through cutting-edge automation and intelligent solutions. This guide provides comprehensive instructions on how to install and set up GeniusAI.

## Installation Guide 🛠️

### File Structure 📂

- **CSS Files:**
  - `/../public/assets/css`
- **Tailwind CSS Files:**
  - `/../resources/css`
- **JavaScript Files:**
  - `/../public/assets/js`
- **Frontend Assets:**
  - `/../public/assets/frontend`
- **Fonts, Images, Components:**
  - `/../public/assets/`

### Laravel Framework

GeniusAI uses the Laravel Framework for backend operations. All necessary files are pre-configured, so no additional installation or updates are required. Learn more about Laravel [here](https://laravel.com/).

### Server Requirements 🖥️

- **Web Server:** Apache server (recommended for .htaccess rewrite rules)
- **PHP:** Version 8.1 or higher
- **Database:** MariaDB 10.5 or higher / MySQL 5.7 or higher
- **PHP's max_execution_time:** It is recommended to increase this value.

**Required PHP Modules:**

- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- Filter PHP Extension
- Hash PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Session PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Zip PHP Extension

### Creating a Database 💾

1. Create a database with `utf8mb4_general_ci` character encoding and grant all permissions.
2. Save your database name, username, and password for later use.

For more detailed instructions, refer to the following guides: [Plesk](https://plesk.com), [CPanel](https://cpanel.net), [MacOS](https://apple.com/macos), [Windows](https://microsoft.com/windows).

### File Permissions 🔐

- **All Folder Permissions:** `755`
- **All File Permissions:** `644`
- **.env File Permissions:** `640`

### Setup Wizard ✨

1. Navigate to: `https://yourdomainname.com/install`.
2. The welcome screen of GeniusAI’s setup wizard will appear. Click **Next** to continue.
3. On the **Server Requirements** screen, ensure all requirements are checked before proceeding.
4. Enter your site’s information:
   - **App Name:** Enter your site/project name.
   - **App URL:** Enter your site/project URL (e.g., `https://yourdomain.com`).
   - **Advanced Options:** (Optional) Leave as default if unsure.
5. Click **Setup Database** and enter your database details:
   - **Database Connection:** Choose your connection type.
   - **Database Host:** Usually `127.0.0.1` or `localhost`.
   - **Database Port:** Default is `3306`.
   - **Database Name:** Enter your database name.
   - **Database Username:** Enter your database username.
   - **Database Password:** Enter your database password.
6. Click **Install** to complete the setup.
7. If successful, the **Installation Completed** screen will appear. Click **Activate GeniusAI** to access the dashboard.

**Default Login Credentials:**

- **Email:** `admin@admin.com`

# GeniusAI RESTful APIs 🌐

This README provides detailed instructions on how to set up, configure, and access the RESTful APIs for GeniusAI, including OAuth2 Passport authentication and API documentation generation.


## REST API Setup 🛠️

### Prerequisites

Before setting up the RESTful APIs, ensure you have the following installed and configured:

- **PHP**: Version 8.1 or higher
- **Laravel**: Ensure you have Laravel installed and configured
- **MySQL/MariaDB**: Database setup and ready for migrations

### Running Migrations 📋

To prepare your database for OAuth2 Passport integration, you'll need to run the necessary migrations. These migrations will create the tables required for Passport's functionality.

1. **Run Migrations**
   - Navigate to the root of your Laravel project.
   - Execute the following command:
     ```bash
     php artisan migrate
     ```
   - This command will set up the necessary tables in your database.

### OAuth2 Passport Setup 🔐

Laravel Passport provides a full OAuth2 server implementation for your Laravel application in a matter of minutes. To set up OAuth2 Passport with Laravel, follow these steps:

1. **Install Passport**
   - From the root of your Laravel project, run:
     ```bash
     composer require laravel/passport
     ```
   - Install Passport by running the following command:
     ```bash
     php artisan passport:install
     ```
   - This command generates the encryption keys needed to generate secure access tokens.

2. **Save Your Credentials**
   - After installation, you'll see `client_id` and `client_secret` values generated in the console.
   - Save these values as you'll need them to access the API endpoints.

3. **Configure Passport in Laravel**
   - Add the `Laravel\Passport\HasApiTokens` trait to your `User` model:
     ```php
     use Laravel\Passport\HasApiTokens;

     class User extends Authenticatable
     {
         use HasApiTokens, Notifiable;
     }
     ```
   - In the `AuthServiceProvider`, register the `Passport` routes within the `boot` method:
     ```php
     use Laravel\Passport\Passport;

     public function boot()
     {
         $this->registerPolicies();

         Passport::routes();
     }
     ```
   - Finally, in your `config/auth.php` file, set the `driver` option of the `api` authentication guard to `passport`:
     ```php
     'guards' => [
         'api' => [
             'driver' => 'passport',
             'provider' => 'users',
         ],
     ],
     ```

That's it! Your OAuth2 Passport setup is now complete. You can now use the `client_id` and `client_secret` to authenticate API requests.

## Generating API Documentation 📑

GeniusAI uses L5-Swagger to generate API documentation. This allows you to automatically document your API endpoints in an easily accessible format.

### Generate Documentation

To generate the Swagger documentation, follow these steps:

1. **Run the Swagger Generator**
   - Navigate to the root of your Laravel project.
   - Run the following command:
     ```bash
     php artisan l5-swagger:generate
     ```
   - This command will generate the Swagger documentation for your API endpoints.

### Accessing the Documentation

Once the documentation is generated, you can access it via your web browser:

1. **Access Documentation**
   - Open your browser and go to:
     ```
     http://your-app-url/api/documentation
     ```
   - Replace `your-app-url` with your actual application URL.

Your API documentation should now be accessible, providing detailed information about each available endpoint, including request and response formats.

## API Endpoint Structure 🧩

GeniusAI's API follows RESTful conventions, and the endpoints are organized into resources. Below are examples of the endpoint structures:

- **Authentication**
  - `POST /api/login`: Authenticate and receive a token
  - `POST /api/register`: Register a new user
- **Content Generation**
  - `POST /api/content/text`: Generate text content
  - `POST /api/content/image`: Generate image content
- **Content Analysis**
  - `POST /api/analysis/sentiment`: Perform sentiment analysis on text
  - `POST /api/analysis/plagiarism`: Check for plagiarism in text content

## Contribution Guidelines 🤝

We welcome contributions to improve and expand the API. To contribute:

1. **Fork the repository** on GitHub.
2. **Create a new branch** for your feature or fix.
3. **Commit your changes** with clear messages.
4. **Submit a pull request** for review.


## Contribution Guidelines 🤝

We welcome contributions to GeniusAI. To contribute, please follow these guidelines:

1. **Fork the repository** to your GitHub account.
2. **Create a new branch** with a descriptive name.
3. **Make your changes** and commit them with a clear message.
4. **Submit a pull request** to the `main` branch for review.

## License 📜

GeniusAI is open-source software licensed under the [MIT License](LICENSE).

## Support and Contact 📧

If you have any questions or need support, please reach out to us at [support@geniusai.com](mailto:officialjwise@gmail.com).

## Git Repository 🔗

Find the source code on GitHub: [GeniusAI Repository](https://github.com/officialjwise/mini_project.git)

Thank you for using GeniusAI! 🎉
