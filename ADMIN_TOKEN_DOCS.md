# Admin Token System Documentation

## Overview

The Admin Token System provides a secure way for administrators to generate API tokens for system integration with third-party applications. These tokens are designed to be used for server-to-server communication and can have extended lifetimes compared to regular user tokens.

## Features

-   Admin-only token generation
-   Custom token expiration dates
-   Token abilities/permissions system
-   Token verification endpoint
-   Token management via API or command line

## Creating Admin Users and Tokens

### Seeding an Admin User

To create an initial admin user with an API token, you can run the AdminUserSeeder:

```bash
php artisan db:seed --class=AdminUserSeeder
```

This will create an admin user with the following credentials:

-   Email: admin@hensleycook.com
-   Password: Admin@123456
-   An API token valid for one year

### Generating Admin Tokens via Command Line

You can generate admin tokens for existing users using the command line:

```bash
# Basic usage
php artisan admin:token --email=admin@hensleycook.com

# Specify token name and expiration
php artisan admin:token --email=admin@hensleycook.com --name="Integration with System X" --expiry=90
```

If you don't specify the email, the command will prompt you to enter it.

## API Endpoints for Token Management

### Generating Admin Tokens

```
POST /api/admin/tokens
Authorization: Bearer {admin-token}
Content-Type: application/json

{
  "name": "Integration with System X",
  "expires_in_days": 365,
  "abilities": ["read", "write"]
}
```

### Listing Admin Tokens

```
GET /api/admin/tokens
Authorization: Bearer {admin-token}
```

### Revoking Admin Tokens

```
DELETE /api/admin/tokens/{tokenId}
Authorization: Bearer {admin-token}
```

### Verifying Token Validity

This endpoint can be used by external systems to validate a token:

```
POST /api/verify-token
Authorization: Bearer {token-to-verify}
```

## Using Tokens in API Requests

To use a token for API authentication, include it in the Authorization header:

```
Authorization: Bearer {token}
```

## Token Security

-   Tokens are stored as hashed values in the database
-   Tokens have expiration dates
-   Token usage is logged
-   Tokens can be revoked at any time
-   Each token has a unique ID and name for tracking purposes

## Best Practices

1. Use descriptive names for tokens to identify their purpose
2. Set appropriate expiration dates based on security requirements
3. Revoke unused tokens
4. Keep tokens secure and never expose them in client-side code
5. Use HTTPS for all API communications
