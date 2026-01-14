# Priority Agribusiness API Documentation

This API allows external applications (like Priority Bank) to access agricultural business data for loan processing, financial analysis, and business intelligence.

## Authentication

The API uses Laravel Sanctum for authentication. To access protected endpoints, you need to obtain an API token.

### Getting an API Token

Send a POST request to `/api/public/v1/auth/token` with the following JSON payload:

```json
{
    "email": "farmer@example.com",
    "password": "password",
    "device_name": "Priority Bank API Client"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "token": "1|abc123def456...",
        "token_type": "Bearer",
        "user": {
            "id": 1,
            "name": "John Farmer",
            "email": "farmer@example.com"
        }
    }
}
```

Include the token in the Authorization header for all subsequent requests:
```
Authorization: Bearer 1|abc123def456...
```

## API Endpoints

### User/Farmer Information

#### Get Profile
`GET /api/v1/farmer/profile`

Returns authenticated user's profile information.

#### Get Business Info
`GET /api/v1/farmer/business-info`

Returns comprehensive business information including farms, houses, and fields.

### Farm Information

#### List Farms
`GET /api/v1/farms`

Returns all farms belonging to the authenticated user.

#### Get Farm Details
`GET /api/v1/farms/{farm}`

Returns detailed information about a specific farm including houses and fields.

### Financial Data

#### Financial Summary
`GET /api/v1/financial/summary?year=2024`

Returns yearly financial summary including sales, expenses, and profit/loss.

#### Sales Data
`GET /api/v1/financial/sales?start_date=2024-01-01&end_date=2024-12-31`

Returns sales data for egg and bird sales within a date range.

#### Expenses Data
`GET /api/v1/financial/expenses?start_date=2024-01-01&end_date=2024-12-31`

Returns expense data categorized by type within a date range.

#### Profit/Loss Analysis
`GET /api/v1/financial/profit-loss?year=2024`

Returns monthly profit/loss analysis for the specified year.

#### Egg Production Data
`GET /api/v1/production/eggs?start_date=2024-01-01&end_date=2024-12-31`

Returns egg production data within a date range.

#### Crop Production Data
`GET /api/v1/production/crops?start_date=2024-01-01&end_date=2024-12-31`

Returns crop production and harvest data within a date range.

## Response Format

All API responses follow this structure:

```json
{
    "success": true|false,
    "data": {...} | null,
    "message": "Error message" // only present on errors
}
```

## Error Handling

- `401 Unauthorized`: Invalid or missing API token
- `403 Forbidden`: Attempting to access resources that don't belong to the authenticated user
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation errors
- `500 Internal Server Error`: Server errors

## Rate Limiting

API requests are rate limited. Please implement appropriate retry logic with exponential backoff.

## Data Security

- All API communication should use HTTPS
- Store API tokens securely
- Rotate tokens regularly
- Never expose tokens in client-side code

## Support

For technical support or questions about the API, please contact the development team.
