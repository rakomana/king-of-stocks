# King Of Stonk

Generate summary of the latest stocks, and send it via email, plus more exciting features

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Installation

1. Clone the repository:

    ```sh
    git clone https://github.com/rakomana/king-of-stocks.git
    cd king-of-stocks
    ```

2. Install composer dependencies:

    ```sh
composer run-script post-root-package-install

composer run-script post-create-project-cmd
    ```

3. JWT
    php artisan jwt:secret
    ```

4. Run database migrations:

    ```sh
    php artisan migrate
    php artisan db:seed
## Seeding with fake data
    ```

5. Serve the application:

    ```sh
    php artisan serve
    ```

## Usage

1. Access the application in your web browser at `http://localhost:8000`.

2. Generate token by login to get stock prices for filtering
see the api documentation below to generate the token

https://api.postman.com/collections/10812189-5840a1df-4641-474d-a597-6a100b749dc9?access_key=PMAT-01H7SQSRDQ7TVVA7CWSMNAB72Q

3. Run cron jobs
Add the following line to server's crontab configuration to schedule the task:
0 22 * * * php /var/www/html/mylaravelapp/artisan email:send
0 22 * * * php /var/www/html/mylaravelapp/artisan clean:up
55 23 * * * php /var/www/html/mylaravelapp/artisan controller:store
