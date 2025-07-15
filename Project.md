# 🧠 Task Manager App (Vue + Laravel + Docker)

A full-stack task management system built with:

- Laravel (Backend API)
- Vue 3 + Vite (Frontend)
- PostgreSQL (Database)
- Maildev (Local mail testing)
- Docker (Dev environment)

## 🛠 Requirements

- Docker & Docker Compose installed

## 🏁 Setup Instructions

### 1. Clone or unzip the project

## 🔐 Seeded Admin Credentials

- **Email:** `admin@example.com`  
- **Password:** `admin12345`
  
## 🌐 Access the App

- **Frontend:** [http://localhost:5173](http://localhost:5173)  
- **Backend API:** [http://localhost:8000/api](http://localhost:8000/api)  
- **Maildev UI:** [http://localhost:1080](http://localhost:1080)

## ✅ Preconfigured Features

- 🔐 **Authentication** using JWT
- 📋 **Task Management** with Role-based Access (Admin/User)
- 📧 **Queued Email Sending** using Maildev
- 🌱 **Database Seeding** with default admin credentials

## 🚀 Start All Services


# 🧠 Task Manager App (Vue + Laravel + Docker)

A full-stack task management system built with:

- Laravel (Backend API)
- Vue 3 + Vite (Frontend)
- PostgreSQL (Database)
- Maildev (Local mail testing)
- Docker (Dev environment)

## 🛠 Requirements

- Docker & Docker Compose installed
- Php
- node 

## 🏁 Setup Instructions

### 1. unzip the project

### 2. Install Dependencies

- **Frontend (Vue + Vite)**  
  Navigate to the frontend directory and install dependencies:

  ```bash
  cd task-manager
  npm install

  Without docker just run 
  npm run dev
  ```

- **Backend (Laravel)**  
  Navigate to the backend directory and install dependencies:

  ```bash
  cd task-manager-api
  composer install

  Without docker just run php artisan serve
  ```

Navigate to the root directory

  ```bash
  cd task-manager
  docker-compose up --build

  If not using docker skip this step
  ```

### 🔐 Seeded Admin Credentials

- **Email:** `admin@example.com`  
- **Password:** `admin12345`
  
## 🌐 Access the App

- **Frontend:** [http://localhost:5173](http://localhost:5173)  
- **Backend API:** [http://localhost:8000/api](http://localhost:8000/api)  
- **Maildev UI:** [http://localhost:1080](http://localhost:1080)

## ✅ Preconfigured Features

- 🔐 **Authentication** using JWT
- 📋 **Task Management** with Role-based Access (Admin/User)
- 📧 **Queued Email Sending** using Maildev
- 🌱 **Database Seeding** with default admin credentials

## 🚀 Start All Services

Navigate to the root directory

  ```bash
  cd task-manager
  docker-compose up --build
```

### 🚀 If you dont use docker

  ```bash
  upload the dump file and update the .env file to point to the db name you are using changing the host username and password ensure ports dont conflict
  then start the backend using php artisan serve