# Qwix

Qwix is a small backend application meant to simulate transfers between user accounts. Qwix is built on top of the Laravel Lumen Framework.

From the Lumen docs:

> Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Table of Contents

| 🔧 [Requirements](#requirements) <br /> 🔌 [Setup](#setup) <br /> 🚀 [API Documentation](#api-documentation) |
| :-- |

## Requirements

* [Docker](https://www.docker.com/get-started);
* [MySQL Database](https://dev.mysql.com/downloads/) (either local or remote);
    * User with CRUD credentials to supply the application with.

## Setup

Clone this repository into your local machine with your terminal application:

```bash
git clone https://github.com/qwix-app/qwix.git
```
### Environment variables

Inside the repository it is important that you set the proper environment variables pointing to your MySQL database.

Feel free to copy and paste the contents from the `.env.example` file.

#### **[FILE]** `.env`

In the following section, fill in your connection data:

```bash
DB_CONNECTION=mysql
DB_HOST=<YOUR_HOST>
DB_PORT=3306
DB_DATABASE=<YOUR_DATABASE>
DB_USERNAME=<YOUR_USERNAME>
DB_PASSWORD=<YOUR_PASSWORD>
```

Further below, include the following variables required to integrate to the external validation services:

```bash
MOCKY_BASE_URI=https://run.mocky.io/v3/
MOCKY_AUTH_URI=8fafdd68-a090-496f-8c9a-3442cf30dae6
MOCKY_NOTIFICATION_URI=b19f7b9f-9cbf-4fc6-ad22-dc30601aec04
```

### Running the container

You will probably want to generate the database structure.

In order to do that, you will have to take a few extra steps just for your first run.

#### First run

By building the `Dockerfile.dev` image and running a container, you gain access to the [Artisan Console](https://laravel.com/docs/8.x/artisan).

You can harness the power of the Artisan CLI and run migrations and generate the database structure automagically.

Build the image like so:

```bash
docker build -t qwix -f Dockerfile.dev .
```

Then run the container, while binding your working directory to a volume:

```bash
# You may need to replace `pwd` with your repo's *absolute path*
# if you're using Windows and/or Git Bash!
docker run -it --tty --rm -p 8000:8000 --name qwix -v `pwd`:/app qwix bash
```

By now you should have gained access to the container's inner terminal, so you can run:

```bash
# The seed flag is optional, but quite convenient
php artisan migrate --seed
```

By passing the `--seed` flag, the `users` table will have some rows generated so you can play around with them.

After the migration is done, you're ready to [query the API with Postman](#api-documentation).

To quit the container, just type `exit` and hit `Enter`.

#### All right, now I just want to serve the API, thanks

Now that your database is all set up, you can build the Docker image with the following command:

```bash
docker build -t qwix
```

Once built, you can run a container based on the image with:

```bash
# Please mind the `pwd` again if you're in a Windows environment!
docker run -p 8000:8000 -v `pwd`:/app qwix 
```

The application will be served in port `8000` and can be accessed through the URL: `http://localhost:8000`.

## API Documentation

Qwix's API Documentation is available in its [`Postman Collection docs ↗️`](https://documenter.getpostman.com/view/5002377/TW77fNTJ).
