# Notes App

Web application built with Symfony. It allows users to create, view, and delete notes.

## How to run

1. Clone this repository.

1. Open the `Notes-App` directory.

1. Install PHP dependencies:
    ```bash
    composer install
    ```

1. Install JavaScript dependencies:
    ```bash
    npm install
    ```

1. Set up the database:
    ```bash
    php bin/console doctrine:migrations:migrate
    php bin/console doctrine:database:create
    ```

1. Run the Symfony server:
    ```bash
    symfony serve
    ```

1. Access the application:
    Open your browser and navigate to `http://localhost:8000`.

