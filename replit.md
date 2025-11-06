# E-Learning Platform

## Overview
A full-stack e-learning platform with PHP/Lumen backend and Angular frontend featuring:
- Course catalog and enrollment
- Razorpay payment integration
- Webhook handling for automated user provisioning
- JWT-based authentication
- PostgreSQL database
- Email notifications

## Project Structure

### Backend (PHP/Lumen)
- **Location**: `backend/`
- **Framework**: Lumen 10 (PHP 8.4)
- **Database**: PostgreSQL (via Replit's built-in database)
- **Port**: 5000
- **API Base**: `/api/v1`

#### Key Components:
- **Controllers**: `backend/app/Http/Controllers/Api/V1/`
  - CatalogController: Course listing and details
  - PreorderController: Order creation with Razorpay
  - WebhookController: Razorpay webhook handling
  - OrdersController: Order status checking
  - CertificatesController: Certificate verification
  - MeController: User dashboard
  - LessonsController: Lesson content delivery

- **Middleware**: `backend/app/Http/Middleware/`
  - JwtMiddleware: JWT token authentication

- **Database Tables**:
  - users: User accounts
  - courses: Course catalog
  - preorders: Payment orders
  - payments: Payment records
  - enrollments: Course enrollments
  - entitlements: Ebook entitlements
  - certificates: Course certificates

### Frontend (Angular 17)
- **Location**: `frontend/`
- **Framework**: Angular 17 with Bootstrap 5
- **Styling**: Custom CSS with dark theme
- **Port**: 4200 (development)

#### Key Components:
- **Pages**:
  - Home: Course catalog listing
  - Course Detail: Individual course view with purchase flow

- **Services**:
  - ApiService: HTTP client for backend API calls

- **Features**:
  - Responsive course cards
  - Razorpay checkout integration
  - Form validation
  - Loading states and error handling

## API Endpoints

### Public Endpoints
- `GET /api/v1/courses` - List all courses
- `GET /api/v1/courses/{id}` - Get course details
- `POST /api/v1/preorders` - Create payment order
- `GET /api/v1/orders/{orderId}/status` - Check order status
- `POST /api/v1/payments/webhook/razorpay` - Razorpay webhook
- `GET /api/v1/certificates/{certNumber}` - Verify certificate

### Protected Endpoints (JWT Required)
- `GET /api/v1/me/dashboard` - User dashboard
- `GET /api/v1/lessons/{id}/content` - Lesson content
- `POST /api/v1/progress` - Update progress

## Environment Variables

### Backend (.env)
```
DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}

RAZORPAY_KEY_ID=your_key_id
RAZORPAY_KEY_SECRET=your_key_secret
RAZORPAY_WEBHOOK_SECRET=your_webhook_secret

WHATSAPP_TOKEN=your_meta_token
WHATSAPP_PHONE_ID=your_phone_id
WHATSAPP_TEMPLATE_WELCOME=welcome_template

MAIL_MAILER=log
```

### Frontend (environment.ts)
```typescript
apiBase: 'http://localhost:5000/api/v1'
razorpayKeyId: 'your_key_id'
```

## Setup Instructions

### Backend
1. Dependencies are auto-installed via Composer
2. Database migrations run automatically
3. Demo courses seeded into database
4. Backend runs on port 5000

### Frontend
1. Install: `cd frontend && npm install`
2. Serve: `npm start`
3. Access: http://localhost:4200

## Payment Flow

1. User selects course and clicks "Enroll Now"
2. User fills enrollment form (name, email, mobile)
3. Frontend creates preorder via API
4. Razorpay checkout modal opens
5. User completes payment
6. Razorpay sends webhook to backend
7. Backend verifies webhook signature
8. Backend creates/updates user account
9. Backend grants course enrollment
10. Backend sends welcome email with credentials
11. User receives access confirmation

## Database Schema

### Users Table
- Stores student accounts
- Auto-created on first purchase
- Includes name, email, phone, whatsapp, password_hash

### Courses Table
- Course catalog
- Includes title, summary, price, currency, cover_url

### Preorders Table
- Tracks payment intents
- Links to Razorpay order_id
- Stores customer info and order details

### Enrollments Table
- Links users to courses
- Tracks enrollment status and progress
- Created automatically after successful payment

## Recent Changes
- 2025-11-06: Initial project setup with Lumen backend and Angular frontend
- 2025-11-06: Configured PostgreSQL database with complete schema
- 2025-11-06: Implemented Razorpay payment integration
- 2025-11-06: Created webhook handler for automated provisioning
- 2025-11-06: Built Angular frontend with course catalog and checkout flow
- 2025-11-06: Seeded demo course data

## Next Steps
1. Configure Razorpay API keys in backend/.env
2. Update frontend environment with actual backend URL
3. Test complete payment flow
4. Implement WhatsApp notifications
5. Add PDF certificate generation
6. Build lesson delivery system
7. Create admin dashboard
