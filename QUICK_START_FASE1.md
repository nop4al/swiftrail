# 🚀 FASE 1: QUICK START GUIDE

**Fase 1 Setup adalah foundation untuk frontend-to-backend integration.**

---

## 📌 What's been set up?

- ✅ Global Axios HTTP client
- ✅ 20+ API wrapper functions
- ✅ Storage management system
- ✅ Helper functions (formatting, validation, error handling)
- ✅ App initialization with global error handling

---

## 🎯 Before Fase 2 Starts

### 1. Verify Everything is Working

**Step 1**: Make sure both servers are running
```bash
# Terminal 1 - Backend (Laravel)
php artisan serve

# Terminal 2 - Frontend (Vue)
npm run dev

# Browser should open: http://localhost:5173
```

**Step 2**: Check browser console (F12 → Console)
```javascript
// Should work without errors
console.log(this.$axios)  // Axios instance
console.log(window.app)   // Vue app
```

**Step 3**: Test API health check
```bash
# In terminal
curl http://localhost:8000/api/v1/health

# Or in browser console
fetch('http://localhost:8000/api/v1/health')
  .then(r => r.json())
  .then(d => console.log(d))
```

### 2. Review Documentation

**Important files to read**:
1. [FASE_1_SETUP_REPORT.md](FASE_1_SETUP_REPORT.md) - What was done
2. [BACKEND_VERIFICATION.md](BACKEND_VERIFICATION.md) - Backend config
3. [FASE_1_VERIFICATION.md](FASE_1_VERIFICATION.md) - Verification checklist

### 3. Understand the Flow

```
User clicks "Cari Kereta"
    ↓
TrainSchedule.vue calls getSchedules() from utils/api.js
    ↓
getSchedules() uses this.$axios to call backend
    ↓
Backend returns schedule data
    ↓
Data is saved to sessionStorage via saveSelectedSchedule()
    ↓
User navigates to SelectSeat.vue
    ↓
SelectSeat retrieves data via getSelectedSchedule()
    ↓
... and so on until checkout
```

---

## 📝 Key Concepts

### 1. **Axios Setup** (Automatic)
- All HTTP requests go through global axios instance
- Auth token is **automatically injected** from localStorage
- Errors are **automatically handled** with user-friendly messages

```javascript
// You can use it in any component:
const response = await this.$axios.get('/endpoint')
```

### 2. **Storage System** (Persistent)
- SessionStorage: Booking data, search history (cleared on browser close)
- LocalStorage: Auth token, user data (persistent)

```javascript
// Save data
saveSelectedSchedule(schedule)

// Retrieve data
const schedule = getSelectedSchedule()
```

### 3. **API Functions** (Ready to Use)
- 20+ functions covering all backend endpoints
- All already handling errors
- All returning promises that can be awaited

```javascript
import { getSchedules, createBooking } from '@/utils/api'

const schedules = await getSchedules(from, to, date)
const booking = await createBooking(data)
```

### 4. **Helper Functions** (Available)
- Format data: `formatPrice(15000)` → `"Rp15.000"`
- Validate input: `isValidEmail(email)` → `true/false`
- Handle errors: `showError(error)` → shows message

```javascript
import { formatPrice, isValidEmail } from '@/utils/helpers'

const price = formatPrice(15000)
const valid = isValidEmail(email)
```

---

## 🔧 Quick Reference

### Using API Functions

```javascript
// In any component:

// 1. Import what you need
import { getSchedules, createBooking } from '@/utils/api'
import { formatPrice } from '@/utils/helpers'

export default {
  methods: {
    async searchTrains() {
      try {
        // 2. Call API function
        const response = await getSchedules(
          this.fromStation,
          this.toStation,
          this.date
        )
        
        // 3. Use the data
        this.schedules = response.data
        
        // 4. Format for display
        this.schedules.forEach(s => {
          s.displayPrice = formatPrice(s.price)
        })
      } catch (error) {
        // Error is automatically shown!
        // error message is in Indonesian
        console.error(error)
      }
    }
  }
}
```

### Using Storage

```javascript
// In TrainSchedule.vue (after search)
import { saveSelectedSchedule } from '@/utils/storage'

handleSelectTrain(schedule) {
  // Save schedule to session
  saveSelectedSchedule(schedule)
  
  // Navigate to next step
  this.$router.push({
    name: 'SelectSeat',
    params: { scheduleId: schedule.id }
  })
}

// In SelectSeat.vue (on mount)
import { getSelectedSchedule } from '@/utils/storage'

mounted() {
  const schedule = getSelectedSchedule()
  if (!schedule) {
    // User didn't come from TrainSchedule
    this.$router.back()
  }
}
```

### Formatting Data

```javascript
import { formatDate, formatTime, formatPrice, formatDuration } from '@/utils/helpers'

// Date
formatDate('2024-01-15')           // "15 Jan 2024"
formatDate('2024-01-15', 'full')   // "Monday, January 15, 2024"

// Time
formatTime('14:30:00')             // "14:30"
formatTime('14:30:00', '12h')      // "2:30 PM"

// Price
formatPrice(150000)                // "Rp150.000"
formatPrice(150000.50, true)       // "Rp150.000,50"

// Duration
formatDuration(3.5)                // "3h 30m"
```

### Validating Input

```javascript
import { 
  isValidEmail, 
  isValidPhoneNumber, 
  isValidNIK,
  validateRequired 
} from '@/utils/helpers'

// Single validation
if (!isValidEmail(this.email)) {
  this.emailError = 'Email tidak valid'
}

if (!isValidPhoneNumber(this.phone)) {
  this.phoneError = 'Nomor HP tidak valid'
}

// Multiple required fields
const { isValid, errors } = validateRequired({
  name: this.name,
  email: this.email,
  phone: this.phone
})

if (!isValid) {
  // errors = { name: "name harus diisi", ... }
  this.formErrors = errors
}
```

---

## 🐛 Common Issues & Solutions

### Issue 1: "Cannot read property '$axios'"
**Problem**: Axios not set up  
**Solution**:
```javascript
// Check app.js has this line:
app.config.globalProperties.$axios = axios

// Or use direct import instead:
import axios from '@/config/axios'
```

### Issue 2: CORS Error in Console
**Problem**: Backend CORS not configured  
**Solution**: Check `config/cors.php` in Laravel
```php
'allowed_origins' => [
    'http://localhost:5173',
],
```

### Issue 3: 404 API Errors
**Problem**: API endpoint doesn't exist  
**Solution**: Check Laravel routes in `routes/api.php`
```bash
php artisan route:list | grep api
```

### Issue 4: 401 Unauthorized
**Problem**: Auth token not set  
**Solution**: For testing, set fake token:
```javascript
// In browser console:
localStorage.setItem('auth_token', 'test-token')

// Then reload page
location.reload()
```

### Issue 5: Session Data Not Persisting
**Problem**: Using localStorage for session data  
**Solution**: Use sessionStorage (cleared on browser close):
```javascript
sessionStorage.setItem('booking', JSON.stringify(data))
```

---

## 📚 File Locations

### Main Files Created/Modified:
```
resources/js/
├── app.js                    ← Updated (17 lines)
├── config/
│   └── axios.js             ← Created (73 lines)
└── utils/
    ├── api.js               ← Enhanced (350+ lines)
    ├── storage.js           ← Created (200+ lines)
    └── helpers.js           ← Enhanced (400+ lines)
```

### Documentation Files:
```
Project Root/
├── FASE_1_SETUP_REPORT.md    ← Technical details
├── FASE_1_SUMMARY.md         ← Architecture overview
├── FASE_1_VERIFICATION.md    ← Verification checklist
├── BACKEND_VERIFICATION.md   ← Backend setup guide
└── This file (QUICK_START_FASE1.md)
```

---

## ✅ Ready for Fase 2?

**Checklist**:
- [ ] Frontend dev server running
- [ ] Backend running
- [ ] No errors in browser console
- [ ] API health check works
- [ ] Understand how axios, storage, and helpers work
- [ ] Read the documentation files

---

## 🚀 Next: Fase 2 - Component Integration

### What will happen:
1. Update `TrainSchedule.vue` to use real API
2. Update `SelectSeat.vue` to fetch available seats
3. Add loading states and error UI
4. Test the complete flow

### Estimated time: 1.5-2 hours

### To Start Fase 2:
1. Read [FRONTEND_AUDIT_REPORT.md](FRONTEND_AUDIT_REPORT.md) for component details
2. Wait for next phase instructions
3. Make sure backend is running with all migrations

---

## 💡 Pro Tips

### 1. Use Browser DevTools Network Tab
- Check all API requests and responses
- Verify request headers include `Authorization` token
- Check response status codes

### 2. Use Vue DevTools Extension
- Inspect component state
- See router navigation
- Check props and emits

### 3. Check Logs
**Backend**:
```bash
tail -f storage/logs/laravel.log
```

**Frontend**:
```bash
# Open browser console (F12)
# Check for any errors or warnings
```

### 4. Test API Directly
```bash
# Test with curl
curl -X GET http://localhost:8000/api/v1/health

# Or in Postman/Insomnia
GET http://localhost:8000/api/v1/health
```

---

## 📞 Getting Help

### If something doesn't work:

1. **Check browser console** (F12) for errors
2. **Check network tab** to see API requests
3. **Read error messages** - they're in Indonesian
4. **Check documentation** in the project root
5. **Review the code** - comments explain everything

---

**You're all set for Fase 2! 🎉**

**When backend is ready, we'll integrate the actual components.**
