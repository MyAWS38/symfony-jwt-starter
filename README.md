Symfony JWT Starter
==================

A PHP/Symfony REST Backend with JWT auth.

## Authentication

Send basic authentication requests to: `/api/tokens`

This will return the JSON web token.

Once you have the token, pass an `Authorization` header with each request.

`Authorization: Bearer [token string]` (without the brackets)

You can test token is valid by sending a request to: `api/tokens/check`

## Development Setup

You must have PHP and Composer installed and access to a MySQL database to setup and run this application.

Many of the setup and administration commands are wrapped in Make targets. It might be helpful to
review the `Makefile`.

1) Create a MySQL database

2) Update `app/config/parameters.yml` with the relevant parameters.

3) Initialize the project with fixtures data.

```
# Delete old db, create new one, and install fixtures.
make init
```

4) Initialize OpenSSL Certs for JWT

```
# Generate openssl certs for jwt. 
# Use the passphrase defined in parameters.yml when prompted.
make init-jwt
```

## Serve the project

Run the following command to serve the project locally for development.

```
make serve
```

This should run the server at: `http://localhost:8000`.

There is a traditional login and UI for user administration.

For development, the data fixtures are setup to add 2 users:

admin: `admin@example.com` / `secret123`

user: `user@example.com` / `secret123`

You will find the fixtures defined in `src/AppBundle/ORM/LoadFixtures.php`.
