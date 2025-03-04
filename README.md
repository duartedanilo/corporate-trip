
# Corporate travel

This project aims to manage corporate travel orders

## Main features
- List travel orders
- Add new travel order
- Update travel order status
- Register new user
- Login and logout

## Stack

**Back-end:** Laravel 12

**Database:** MySQL


## Run locally

Before clone this project, you will need git and Docker.

First, you need to clone this repository to your machine.
```bash
  git clone git@github.com:duartedanilo/corporate-trip.git
```

Go to the directory
```bash
  cd corporate-trip
```

If you have composer installed in your machine, you can run the command
```bash
  composer install
```

If you don't have composer installed in your machine, run the following command
```bash
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install
```

Set the environment variables
```bash
cp .env.example .env
```

Maybe you will need to change some environment variable!

Run the following command to install the Docker containers
```bash
./vendor/bin/sail up -d
```

After that you can run artisan, composer, npm though sail command.

Install node packages
```bash
./vendor/bin/sail npm install
```

Build it
```bash
./vendor/bin/sail npm run build
```

If you go to [localhost](http://localhost) you should see the application running.

## Running Tests

To run tests, run the following command

```bash
  ./vendor/bin/sail artisan test
```

## API Documentation
This project is using Swagger. Access it on [http://localhost/api/documentation](http://localhost/api/documentation).

## User notification
The e-mails on local environment are sent to [Mailpit](http://localhost:8025/). 
