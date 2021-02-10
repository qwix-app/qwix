# Qwix

Qwix is a small backend application meant to simulate transfers between user accounts. Qwix is built on top of the Laravel Lumen Framework.

From the Lumen docs:

> Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Requirements

* Docker;
* MySQL Database (either local or remote).

## Setup

Clone this repository into your local machine with your terminal application:

```bash
clone https://github.com/qwix-app/qwix.git
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

Further below, include the following variables, needed for the external validation services:

```bash
MOCKY_BASE_URI=https://run.mocky.io/v3/
MOCKY_AUTH_URI=8fafdd68-a090-496f-8c9a-3442cf30dae6
MOCKY_NOTIFICATION_URI=b19f7b9f-9cbf-4fc6-ad22-dc30601aec04
```

### Running the container

You can build the Docker image with the following command:

```bash
docker build -t qwix
```

Once built, you can run a container based on the image with:

```bash
# You may need to replace `pwd` with your repo's *absolute path*
# if you're using Windows and/or Git Bash!
docker run -p 8000:8000 -v `pwd`:/app qwix 
```

The application will be served in port `8000` and can be accessed through the URL: `http://localhost:8000`.
