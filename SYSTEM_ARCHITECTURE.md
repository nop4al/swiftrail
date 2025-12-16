# Swiftrail System Architecture

## Complete System Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                          CLIENT LAYER (Vue.js)                      │
├─────────────────────────────────────────────────────────────────────┤
│                                                                       │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐  │
│  │  TrainSchedule   │  │  SelectSeat      │  │ OrderConfirm     │  │
│  │  Search trains   │  │  Pick seats      │  │ Review order     │  │
│  │  from/to/date    │  │  View layout     │  │ View pricing     │  │
│  └────────┬─────────┘  └────────┬─────────┘  └────────┬─────────┘  │
│           │                     │                     │            │
│           └──────────┬──────────┴──────────┬──────────┘            │
│                      │                     │                       │
│                  ┌───▼─────┬───────────┐   │                       │
│                  │          │           │   │                       │
│          ┌──────────────────────────┐  │   │                       │
│          │  PaymentMethods          │  │   │                       │
│          │  Select payment option   │  │   │                       │
│          └───────────────┬──────────┘  │   │                       │
│                          │             │   │                       │
│                      ┌───▼─────────────▼───▼──────┐                │
│                      │  Checkout / Final Confirm  │                │
│                      │  Process payment           │                │
│                      └────────────┬────────────────┘                │
│                                   │                                 │
└───────────────────────────────────┼─────────────────────────────────┘
                                    │
                    ┌───────────────▼────────────────┐
                    │    HTTP / JSON API LAYER       │
                    │  Base URL: /api/v1             │
                    └───────────────┬────────────────┘
                                    │
┌───────────────────────────────────┼─────────────────────────────────┐
│                   API SERVER LAYER (Laravel)                        │
├───────────────────────────────────┼─────────────────────────────────┤
│                                   │                                  │
│           ┌───────────────────────▼──────────────────┐              │
│           │       Router (routes/api.php)           │              │
│           │  Groups:                                │              │
│           │  - /schedules                           │              │
│           │  - /bookings                            │              │
│           │  - /payments                            │              │
│           └────────────────┬─────────────────────────┘              │
│                            │                                        │
│   ┌────────────────────────┼────────────────────────┐              │
│   │                        │                        │              │
│   ▼                        ▼                        ▼              │
│ ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐ │
│ │TrainSchedule    │  │Booking           │  │Payment           │ │
│ │Controller        │  │Controller        │  │Controller        │ │
│ │                  │  │                  │  │                  │ │
│ │• getSchedules    │  │• getAvailSeats   │  │• getPaymentMeth  │ │
│ │• getScheduleDetail│ │• createBooking   │  │• processPayment  │ │
│ │                  │  │• getBooking      │  │• getOrderConfirm │ │
│ │                  │  │• cancelBooking   │  │                  │ │
│ └────────┬─────────┘  └────────┬─────────┘  └────────┬─────────┘ │
│          │                     │                     │            │
│          └────────┬────────────┴──────────┬──────────┘            │
│                   │                       │                       │
│           ┌───────▼────────────────────────▼───────┐              │
│           │     Models (Business Logic)            │              │
│           │                                        │              │
│           │  • Schedule (with Train, Route)       │              │
│           │  • Booking (with Schedule, User)      │              │
│           │  • Payment (with Booking)             │              │
│           │  • User (with Bookings, Loyalty)      │              │
│           │  • Station (with Routes)              │              │
│           │  • Train (with Schedules)             │              │
│           │  • Route (with Stations, Trains)      │              │
│           │  • UserLoyalty, Refund, etc.          │              │
│           │                                        │              │
│           └────────────────┬──────────────────────┘              │
│                            │                                      │
└────────────────────────────┼──────────────────────────────────────┘
                             │
┌────────────────────────────▼──────────────────────────────────────┐
│                      DATABASE LAYER (SQLite)                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                    │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐             │
│  │  schedules  │  │  bookings   │  │  payments   │             │
│  │             │  │             │  │             │             │
│  │ - id        │  │ - id        │  │ - id        │             │
│  │ - train_id  │  │ - schedule  │  │ - booking   │             │
│  │ - route_id  │  │ - user_id   │  │ - amount    │             │
│  │ - date      │  │ - seats     │  │ - method    │             │
│  │ - departure │  │ - status    │  │ - status    │             │
│  │ - arrival   │  │ - total     │  │ - date      │             │
│  │ - available │  │ - created   │  │             │             │
│  └─────────────┘  └─────────────┘  └─────────────┘             │
│                                                                    │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐             │
│  │  trains     │  │  routes     │  │  stations   │             │
│  │             │  │             │  │             │             │
│  │ - id        │  │ - id        │  │ - id        │             │
│  │ - name      │  │ - from_id   │  │ - code      │             │
│  │ - type      │  │ - to_id     │  │ - city      │             │
│  │ - capacity  │  │ - distance  │  │ - timezone  │             │
│  │ - classes   │  │ - duration  │  │             │             │
│  └─────────────┘  └─────────────┘  └─────────────┘             │
│                                                                    │
│  ┌─────────────┐  ┌──────────────────┐  ┌──────────────────┐  │
│  │  users      │  │  user_loyalty    │  │  refunds         │  │
│  │             │  │                  │  │                  │  │
│  │ - id        │  │ - id             │  │ - id             │  │
│  │ - email     │  │ - user_id        │  │ - booking_id     │  │
│  │ - password  │  │ - points         │  │ - amount         │  │
│  │ - name      │  │ - tier_id        │  │ - reason         │  │
│  │             │  │ - created        │  │ - status         │  │
│  └─────────────┘  └──────────────────┘  └──────────────────┘  │
│                                                                    │
└────────────────────────────────────────────────────────────────────┘
```

---

## Request Flow

### Complete Booking Flow

```
┌─────────────┐
│   START     │
└──────┬──────┘
       │
       ▼
┌──────────────────────────────────────┐
│ User searches trains                 │
│ TrainSchedule.vue → Input date/route │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Frontend API Call:                   │
│ GET /api/v1/schedules?               │
│   from=GMR&to=SGU&date=2025-01-15   │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Backend Processing:                  │
│ TrainScheduleController@getSchedules │
│ • Query schedules with filters       │
│ • Calculate available seats          │
│ • Format response                    │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Database Query:                      │
│ SELECT * FROM schedules              │
│ WHERE date = ... AND route_id = ...  │
│ INNER JOIN trains, routes, stations  │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Return JSON Response:                │
│ {                                    │
│   "success": true,                   │
│   "data": [                          │
│     {                                │
│       "id": 1,                       │
│       "train_name": "Argo Bromo",   │
│       "departure": "08:00",          │
│       "arrival": "16:30",            │
│       "available_seats": 45          │
│     }                                │
│   ]                                  │
│ }                                    │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Frontend Renders:                    │
│ SelectSeat.vue                       │
│ Shows train details and seat layout  │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Get available seats:                 │
│ GET /api/v1/bookings/1/seats         │
│                                      │
│ Backend:                             │
│ BookingController@getAvailableSeats  │
│ • Query all bookings for schedule    │
│ • Generate seat layout               │
│ • Separate available/booked          │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ User selects seats                   │
│ SelectSeat.vue → Click A01, A02      │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create booking:                      │
│ POST /api/v1/bookings                │
│ {                                    │
│   "schedule_id": 1,                  │
│   "selected_seats": ["A01", "A02"],  │
│   "passenger_count": 2,              │
│   "price_per_seat": 150000,          │
│   "platform_fee": 25000              │
│ }                                    │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Backend Processing:                  │
│ BookingController@createBooking      │
│ • Validate input                     │
│ • Check seat availability            │
│ • Create booking record              │
│ • Generate reference number          │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Database Insert:                     │
│ INSERT INTO bookings VALUES (        │
│   schedule_id=1,                     │
│   user_id=1,                         │
│   seats=['A01','A02'],               │
│   status='pending',                  │
│   total=350000                       │
│ )                                    │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Return booking ID:                   │
│ {                                    │
│   "success": true,                   │
│   "data": {                          │
│     "id": 10,                        │
│     "reference": "SWR20250115001",   │
│     "total_amount": 350000           │
│   }                                  │
│ }                                    │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Show order confirmation:             │
│ OrderConfirmation.vue                │
│ GET /api/v1/payments/confirmation/10 │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Backend returns order details:       │
│ PaymentController@getOrderConfirm    │
│ • Query booking with all relations   │
│ • Format pricing breakdown           │
│ • Include passenger details          │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Frontend shows order summary:        │
│ • Route: Jakarta → Surabaya          │
│ • Date: 2025-01-15                   │
│ • Seats: A01, A02                    │
│ • Passengers: 2 x Dewasa             │
│ • Price: 300,000 + 25,000 = 325,000 │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Get payment methods:                 │
│ GET /api/v1/payments/methods         │
│                                      │
│ Returns:                             │
│ - Credit Card                        │
│ - Debit Card                         │
│ - Bank Transfer                      │
│ - E-Wallet                           │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ User selects payment method:         │
│ Checkout.vue → Select Credit Card    │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Process payment:                     │
│ POST /api/v1/payments/process        │
│ {                                    │
│   "booking_id": 10,                  │
│   "payment_method": "credit_card",   │
│   "amount": 325000,                  │
│   "card_token": "4111..."            │
│ }                                    │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Backend processes payment:           │
│ PaymentController@processPayment     │
│ • Validate amount                    │
│ • Call payment gateway               │
│ • Create payment record              │
│ • Generate ticket number             │
│ • Update booking status              │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Database updates:                    │
│ UPDATE bookings                      │
│   SET status='confirmed'             │
│ INSERT INTO payments                 │
│   (booking_id, amount, method, ...)  │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Return confirmation:                 │
│ {                                    │
│   "success": true,                   │
│   "data": {                          │
│     "ticket_number": "TKT2025...",   │
│     "transaction_id": "TXN2025...",  │
│     "status": "confirmed",           │
│     "booking_ref": "SWR2025..."      │
│   }                                  │
│ }                                    │
└──────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Frontend shows receipt:              │
│ Receipt.vue                          │
│ • Ticket number                      │
│ • Transaction ID                     │
│ • Booking reference                  │
│ • QR code                            │
│ • Download/Share options             │
└──────────────────────────────────────┘
       │
       ▼
┌─────────────┐
│     END     │
└─────────────┘
```

---

## Component Interaction Diagram

```
┌──────────────────────────────────────────────────────────────┐
│                       Router (main.js)                        │
│                                                                │
│  /trains → TrainSchedule.vue                                  │
│  /select-seat → SelectSeat.vue                               │
│  /order-confirmation → OrderConfirmation.vue                 │
│  /payment-methods → PaymentMethods.vue                       │
│  /checkout → Checkout.vue                                     │
│  /receipt → Receipt.vue                                       │
└──────────────────────────────────────────────────────────────┘
         │                    │                    │
         ▼                    ▼                    ▼
    ┌─────────────┐  ┌─────────────┐  ┌──────────────────┐
    │TrainSchedule│  │ SelectSeat  │  │OrderConfirmation │
    │             │  │             │  │                  │
    │ ┌─────────┐ │  │ ┌─────────┐ │  │ ┌──────────────┐│
    │ │GET /v1/ │ │  │ │GET /v1/ │ │  │ │ GET /v1/     ││
    │ │schedules│ │  │ │bookings/│ │  │ │ payments/    ││
    │ │         │ │  │ │seats    │ │  │ │ confirmation ││
    │ └─────────┘ │  │ │         │ │  │ │ /{bookingId} ││
    │             │  │ │POST /v1/│ │  │ └──────────────┘│
    │ on:select   │  │ │bookings │ │  │                  │
    │   ─→ pass   │  │ └─────────┘ │  │ on:confirm       │
    │   price     │  │             │  │   ─→ pass to     │
    │   & train   │  │ on:save     │  │   payment-methods│
    │   type      │  │   ─→ create │  │                  │
    │             │  │   booking   │  │ on:cancel        │
    │             │  │   ─→ redirect  │   ─→ back to     │
    │             │  │   to confirm   │   train search   │
    │             │  │             │  │                  │
    │             │  │ on:cancel   │  │                  │
    │             │  │   ─→ back to   │                  │
    │             │  │   train search │                  │
    │             │  │             │  │                  │
    └─────────────┘  └─────────────┘  └──────────────────┘
         │                    │                    │
         └────────────────────┴────────────────────┘
                        │
                        ▼
              ┌──────────────────────┐
              │  Shared Store/State  │
              │                      │
              │ • selectedTrain      │
              │ • selectedSeats      │
              │ • bookingId          │
              │ • totalPrice         │
              │ • passengerCount     │
              └──────────────────────┘
                        │
                        ▼
              ┌──────────────────────┐
              │  PaymentMethods      │
              │                      │
              │ GET /v1/             │
              │ payments/methods     │
              │                      │
              │ on:select            │
              │   ─→ to checkout     │
              └──────────────────────┘
                        │
                        ▼
              ┌──────────────────────┐
              │  Checkout.vue        │
              │                      │
              │ POST /v1/            │
              │ payments/process     │
              │                      │
              │ on:success           │
              │   ─→ to receipt      │
              │                      │
              │ on:error             │
              │   ─→ show error msg  │
              └──────────────────────┘
                        │
                        ▼
              ┌──────────────────────┐
              │  Receipt.vue         │
              │                      │
              │ Show ticket          │
              │ QR code              │
              │ Download option      │
              └──────────────────────┘
```

---

## Technology Stack

```
Frontend:
  • Vue 3 (SPA)
  • Vue Router (Navigation)
  • Vite (Build tool)
  • Axios (HTTP client)
  • Tailwind CSS (Styling)

Backend:
  • Laravel 12
  • PHP 8.5
  • RESTful API design
  • Model-View-Controller pattern

Database:
  • SQLite (Development)
  • MySQL (Production-ready)
  • 15 tables with relationships
  • Foreign key constraints
  • Indexes on critical fields

Development:
  • Laragon (Local environment)
  • Composer (PHP dependencies)
  • NPM (Node dependencies)
  • Git (Version control)

Tools:
  • Postman (API testing)
  • Thunder Client (VS Code)
  • DB Browser for SQLite
```

---

## Data Flow Summary

```
User Input
    ↓
Vue Component
    ↓
Axios HTTP Request
    ↓
Laravel Route
    ↓
API Controller
    ↓
Model Query / Business Logic
    ↓
Database Query
    ↓
Model Result
    ↓
Controller Response Formatting
    ↓
JSON Response
    ↓
Axios Response Handler
    ↓
Vue Component Update
    ↓
DOM Re-render
    ↓
User Sees Update
```

