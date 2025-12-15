# Firebase Authentication Setup Guide

## Langkah-langkah Setup Firebase Authentication untuk SwiftRail

### 1. Buat Firebase Project

1. Buka [Firebase Console](https://console.firebase.google.com/)
2. Klik "Add project" atau "Create project"
3. Isi nama project: `SwiftRail` (atau nama lain sesuai preferensi)
4. Pilih lokasi (recommended: Singapura atau Indonesia jika tersedia)
5. Klik "Create project"

### 2. Setup Firebase Authentication

1. Di Firebase Console, buka project kamu
2. Di sidebar, klik "Authentication"
3. Klik "Get Started"
4. Pilih "Email/Password" sebagai sign-in method
5. Toggle "Enable" untuk Email/Password
6. Klik "Save"

### 3. Daftar Web App di Firebase

1. Di Firebase Console, klik ⚙️ (Settings) di atas "Project Overview"
2. Pilih tab "Your apps"
3. Klik icon Web (`</>`), jika belum ada web app
4. Isi App nickname: `SwiftRail Web`
5. Klik "Register app"
6. Copy Firebase configuration yang muncul

### 4. Konfigurasi Environment Variable

1. Buka file `.env` di root project
2. Tambahkan Firebase config dari step 3:

```dotenv
# Firebase Configuration
VITE_FIREBASE_API_KEY=your_api_key_here
VITE_FIREBASE_AUTH_DOMAIN=your_auth_domain_here
VITE_FIREBASE_PROJECT_ID=your_project_id_here
VITE_FIREBASE_STORAGE_BUCKET=your_storage_bucket_here
VITE_FIREBASE_MESSAGING_SENDER_ID=your_messaging_sender_id_here
VITE_FIREBASE_APP_ID=your_app_id_here
```

**Contoh:**
```dotenv
VITE_FIREBASE_API_KEY=AIzaSyDw_-eFZ4zQ_4v1B2c3D4e5F6g7H8i9J0k
VITE_FIREBASE_AUTH_DOMAIN=swiftrail-abc123.firebaseapp.com
VITE_FIREBASE_PROJECT_ID=swiftrail-abc123
VITE_FIREBASE_STORAGE_BUCKET=swiftrail-abc123.appspot.com
VITE_FIREBASE_MESSAGING_SENDER_ID=123456789012
VITE_FIREBASE_APP_ID=1:123456789012:web:abc123def456ghi789jkl
```

### 5. Update Database Schema

Tambahkan kolom `firebase_uid` ke tabel users:

```bash
php artisan make:migration add_firebase_uid_to_users_table
```

Edit migration file:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('firebase_uid')->nullable()->unique()->after('id');
});
```

Jalankan migration:

```bash
php artisan migrate
```

### 6. Konfigurasi Login/Register Components

Login dan Register components sudah otomatis menggunakan Firebase. Flow-nya:

1. User input email & password di Vue component
2. Firebase authenticate user
3. Jika berhasil, sync ke Laravel backend via `/api/v1/auth/sync-firebase`
4. Backend create/update user di database & generate API token
5. Store token di localStorage untuk API requests

### 7. Testing

**Test Register:**
```bash
1. Buka http://localhost:5173
2. Klik "Register"
3. Isi email, nama, password
4. Klik submit
5. User akan dibuat di Firebase & Laravel database
```

**Test Login:**
```bash
1. Buka http://localhost:5173
2. Klik "Login"
3. Isi email & password (dari Firebase)
4. Klik submit
5. User akan login & token disimpan di localStorage
```

### 8. Environment Variables Explanation

| Variable | Description |
|----------|-------------|
| `VITE_FIREBASE_API_KEY` | Public API key untuk Firebase |
| `VITE_FIREBASE_AUTH_DOMAIN` | Firebase auth domain (untuk sign-in page) |
| `VITE_FIREBASE_PROJECT_ID` | Unique project ID |
| `VITE_FIREBASE_STORAGE_BUCKET` | Cloud Storage bucket (jika digunakan nanti) |
| `VITE_FIREBASE_MESSAGING_SENDER_ID` | Firebase Cloud Messaging sender ID |
| `VITE_FIREBASE_APP_ID` | Firebase app ID |

### 9. Fitur-fitur yang Tersedia

✅ **Register dengan Firebase**
- User dibuat di Firebase Authentication
- User disync ke Laravel database
- Validasi email

✅ **Login dengan Firebase**
- Login pakai Firebase credentials
- Auto-sync profile ke Laravel
- Generate API token untuk backend requests

✅ **Auto Token Management**
- Firebase ID token otomatis di-refresh
- Laravel API token disimpan di localStorage
- All API requests menggunakan token

✅ **Profile Sync**
- Display name diambil dari Firebase
- Avatar/photo URL disupport
- Profile bisa di-update di Firebase

### 10. Troubleshooting

**Error: "Invalid API key"**
- Pastikan VITE_FIREBASE_API_KEY di `.env` benar
- Di Firebase Console, check API restrictions di Keys

**Error: "Domain not authorized"**
- Di Firebase Console, buka Authentication settings
- Authorized domains, tambahkan localhost:5173 (dev) & domain production

**User tidak ter-sync ke database**
- Check Laravel logs: `storage/logs/laravel.log`
- Pastikan `/api/v1/auth/sync-firebase` endpoint accessible
- Verify Firebase ID token passed correctly

### 11. Next Steps

Setelah Firebase Auth setup berhasil, bisa tambah:
- **Firebase Cloud Messaging** untuk push notifications
- **Firebase Realtime Database** untuk live seat availability
- **Firebase Storage** untuk user profile photos
- **Firebase Analytics** untuk tracking user behavior

---

**Dokumentasi Firebase:** https://firebase.google.com/docs/auth
**Laravel Sanctum:** https://laravel.com/docs/sanctum
