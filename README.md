A RESTful API for managing video (Vimeo) and photo (Flickr) bookmarks with oEmbed metadata extraction.

# Overview #

This API allows users to manage bookmarks for videos and photos, automatically retrieving metadata using the oEmbed protocol via the oscarotero/embed library.

Built with Symfony, API Platform, and Doctrine.

# Features #

- ✔️ Create and delete video and photo bookmarks
- ✔️ Automatic metadata extraction via oEmbed
- ✔️ Supports JSON-LD format (API Platform)
- ✔️ Uses Doctrine ORM with PostgreSQL
- ✔️ Fully tested with PHPUnit
- ✔️ Includes a Makefile for simplified commands

---------------------------

🔧 Installation & Setup

1️⃣ Build the project (without cache)
```docker compose build --no-cache```

2️⃣ Start the containers
```docker-compose up -d```

3️⃣ Install dependencies
```make composer-install```

4️⃣ Run database migrations
```make migration-migrate```

---------------------------

🌍 Accessing the API (HTTPS Notice)

When accessing the API on localhost, you'll need to accept the self-signed SSL certificate.

If you want to access the API over HTTPS, retrieve the certificate from the Caddy volume:

```Path: caddy/pki/authorities/local/root```

Save the certificate in your browser to avoid security warnings.

🚀 You're ready to use the API!
