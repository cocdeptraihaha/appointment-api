# Appointment API

Laravel API for Appointment Management System

## Features

- ✅ CRUD operations for appointments, contacts, staff, services
- ✅ Appointment types with soft delete
- ✅ Many-to-many relationships
- ✅ CORS enabled for React frontend
- ✅ API Resources for consistent responses
- ✅ Search and filtering capabilities

## API Endpoints

### Appointments
- `GET /api/appointments` - List appointments
- `POST /api/appointments` - Create appointment
- `GET /api/appointments/{id}` - Get appointment
- `PUT /api/appointments/{id}` - Update appointment
- `DELETE /api/appointments/{id}` - Delete appointment

### Appointment Types
- `GET /api/appointment-types` - List appointment types
- `POST /api/appointment-types` - Create appointment type
- `PUT /api/appointment-types/{id}` - Update appointment type
- `DELETE /api/appointment-types/{id}` - Soft delete appointment type

### Contacts
- `GET /api/contacts` - List contacts
- `POST /api/contacts` - Create contact
- `GET /api/contacts/{id}` - Get contact
- `PUT /api/contacts/{id}` - Update contact
- `DELETE /api/contacts/{id}` - Delete contact

### Staff
- `GET /api/staff` - List staff
- `POST /api/staff` - Create staff
- `GET /api/staff/{id}` - Get staff
- `PUT /api/staff/{id}` - Update staff
- `DELETE /api/staff/{id}` - Delete staff

### Services
- `GET /api/services` - List services
- `POST /api/services` - Create service
- `GET /api/services/{id}` - Get service
- `PUT /api/services/{id}` - Update service
- `DELETE /api/services/{id}` - Delete service

### Settings
- `GET /api/settings` - Get all settings
- `PUT /api/settings` - Bulk update settings

### Combined Data
- `GET /api/data` - Get all data in one request

## Deployment on Render

1. Connect your GitHub repository to Render
2. Create a new Web Service
3. Use the following settings:
   - **Build Command**: `chmod +x build.sh && ./build.sh`
   - **Start Command**: `chmod +x start.sh && ./start.sh`
   - **Environment**: PHP
   - **Plan**: Free

4. Add environment variables:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_KEY` (will be generated)

5. Create a PostgreSQL database and connect it to your service

## Local Development

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Start development server
php artisan serve
```

## Environment Variables

```env
APP_NAME="Appointment API"
APP_ENV=production
APP_KEY=base64:your-app-key
APP_DEBUG=false
APP_URL=https://your-app.onrender.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password
```

## CORS Configuration

The API is configured to accept requests from any origin. For production, you may want to restrict this to your frontend domain.

## License

MIT