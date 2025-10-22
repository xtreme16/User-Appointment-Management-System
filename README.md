# User Appointment Management System

## 🛠️ Setup Instructions

### 1. Clone the Repository
```bash
git clone https://github.com/xtreme16/User-Appointment-Management-System.git
cd User-Appointment-Management-System
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run dev
```

### 3. Copy and Configure Environment File
```bash
cp .env.example .env
```

Edit .env sesuai dengan konfigurasi:
```bash
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=appoint_sys
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=cookie
SESSION_LIFETIME=60
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=127.0.0.1
SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000,localhost:8000
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations and Seeders
```bash
php artisan migrate --seed
```

### 6. Start the Development Server
```bash
php artisan serve
```

## 💡 Features

- User authentication (register/login)
- Create and view appointments
- Invite other users to meetings
- Timezone-aware scheduling
- Seeder for dummy users and appointments

## 📸 Documentation & Demo Video

📹 Watch the Demo on Google Drive
Link:
https://drive.google.com/drive/folders/1sY0V62vF-IjPx-HH2uz74eZMkEDF7otj?usp=sharing