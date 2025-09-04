# 🗓️ TKA Scheduling Feature

## 📋 **Feature Overview**

Fitur penjadwalan TKA memungkinkan Super Admin untuk membuat, mengelola, dan mengirim jadwal pelaksanaan Tes Kemampuan Akademik (TKA) ke semua stakeholder (Teacher dan Student dashboards).

## 🎯 **User Requirements**

> "Super admin ini menjadwalkan pelaksanaan TKA, kemudian jadwal tersebut di kirim ke Dashboard teacher dan juga siswa bahwa pelaksaan TKA akan di berlangsung pada ......"

## 🏗️ **Architecture**

### **Backend (Laravel)**
- **Model**: `TkaSchedule`
- **Controller**: `TkaScheduleController`
- **Database**: `tka_schedules` table
- **API Routes**: `/api/tka-schedules/*`

### **Frontend Integration**
- **Super Admin Dashboard**: Manage schedules
- **Teacher Dashboard**: View upcoming schedules
- **Student Dashboard**: View upcoming schedules

## 🗄️ **Database Schema**

### **Table: `tka_schedules`**

```sql
CREATE TABLE tka_schedules (
    id BIGINT IDENTITY PRIMARY KEY,
    title NVARCHAR(255) NOT NULL,           -- Judul jadwal TKA
    description TEXT NULL,                  -- Deskripsi jadwal
    start_date DATETIME NOT NULL,           -- Tanggal dan waktu mulai
    end_date DATETIME NOT NULL,             -- Tanggal dan waktu selesai
    status NVARCHAR(20) DEFAULT 'scheduled', -- scheduled, ongoing, completed, cancelled
    type NVARCHAR(20) DEFAULT 'regular',    -- regular, makeup, special
    instructions TEXT NULL,                 -- Instruksi khusus
    target_schools JSON NULL,               -- Sekolah yang ditargetkan (null = semua)
    is_active BIT DEFAULT 1,                -- Status aktif
    created_by NVARCHAR(255) NULL,          -- Admin yang membuat
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);
```

### **Indexes**
- `start_date, end_date` - For date range queries
- `status, is_active` - For status filtering
- `type` - For type filtering

## 🔧 **API Endpoints**

### **Public Routes (Teacher & Student Dashboards)**

#### **GET /api/tka-schedules**
Get all active TKA schedules with optional filters.

**Query Parameters:**
- `status` - Filter by status (scheduled, ongoing, completed, cancelled)
- `school_id` - Filter by specific school
- `type` - Filter by type (regular, makeup, special)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "TKA Gelombang 1 - Tahun 2024",
      "description": "Tes Kemampuan Akademik gelombang pertama...",
      "start_date": "2024-09-11T08:00:00.000000Z",
      "end_date": "2024-09-11T12:00:00.000000Z",
      "status": "scheduled",
      "type": "regular",
      "instructions": "Siswa diharapkan datang 30 menit sebelum...",
      "target_schools": null,
      "is_active": true,
      "created_by": "Super Admin",
      "formatted_start_date": "11/09/2024 08:00",
      "formatted_end_date": "11/09/2024 12:00",
      "duration": "4 hours",
      "status_badge": "bg-blue-100 text-blue-800",
      "type_badge": "bg-blue-100 text-blue-800"
    }
  ],
  "total": 5
}
```

#### **GET /api/tka-schedules/upcoming**
Get upcoming TKA schedules only.

**Query Parameters:**
- `school_id` - Filter by specific school

**Response:**
```json
{
  "success": true,
  "data": [...],
  "total": 3
}
```

### **Admin Routes (Super Admin Dashboard)**

#### **POST /api/tka-schedules**
Create new TKA schedule.

**Request Body:**
```json
{
  "title": "TKA Gelombang 1 - Tahun 2024",
  "description": "Tes Kemampuan Akademik gelombang pertama...",
  "start_date": "2024-09-11 08:00:00",
  "end_date": "2024-09-11 12:00:00",
  "type": "regular",
  "instructions": "Siswa diharapkan datang 30 menit sebelum...",
  "target_schools": [1, 2, 3]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Jadwal TKA berhasil dibuat",
  "data": { ... }
}
```

#### **PUT /api/tka-schedules/{id}**
Update existing TKA schedule.

#### **DELETE /api/tka-schedules/{id}**
Soft delete TKA schedule (set is_active = false).

#### **POST /api/tka-schedules/{id}/cancel**
Cancel TKA schedule (set status = cancelled).

## 🎨 **Model Features**

### **TkaSchedule Model**

#### **Fillable Fields**
```php
protected $fillable = [
    'title', 'description', 'start_date', 'end_date',
    'status', 'type', 'instructions', 'target_schools',
    'is_active', 'created_by'
];
```

#### **Casts**
```php
protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'target_schools' => 'array',
    'is_active' => 'boolean'
];
```

#### **Query Scopes**
- `active()` - Only active schedules
- `upcoming()` - Only future schedules
- `ongoing()` - Currently running schedules
- `completed()` - Past schedules
- `forSchool($schoolId)` - Schedules for specific school

#### **Accessors**
- `formatted_start_date` - Human readable start date
- `formatted_end_date` - Human readable end date
- `duration` - Duration in human readable format
- `status_badge` - CSS classes for status badge
- `type_badge` - CSS classes for type badge

#### **Methods**
- `isUpcoming()` - Check if schedule is in future
- `isOngoing()` - Check if schedule is currently running
- `isCompleted()` - Check if schedule is completed
- `isCancelled()` - Check if schedule is cancelled
- `canBeEdited()` - Check if schedule can be edited
- `canBeCancelled()` - Check if schedule can be cancelled

## 📊 **Sample Data**

### **TKA Schedules Seeded:**

1. **TKA Gelombang 1 - Tahun 2024**
   - **Date**: 7 days from now, 08:00 - 12:00
   - **Type**: Regular
   - **Target**: All schools

2. **TKA Gelombang 2 - Tahun 2024**
   - **Date**: 14 days from now, 08:00 - 12:00
   - **Type**: Regular
   - **Target**: All schools

3. **TKA Susulan - Siswa Berhalangan**
   - **Date**: 21 days from now, 13:00 - 17:00
   - **Type**: Makeup
   - **Target**: All schools

4. **TKA Khusus - Sekolah Swasta**
   - **Date**: 28 days from now, 09:00 - 13:00
   - **Type**: Special
   - **Target**: Schools with ID 1, 2, 3

5. **TKA Gelombang 3 - Tahun 2024**
   - **Date**: 35 days from now, 08:00 - 12:00
   - **Type**: Regular
   - **Target**: All schools

## 🔄 **Status Flow**

### **Schedule Statuses:**
1. **scheduled** - Default status when created
2. **ongoing** - Currently running (start_date <= now <= end_date)
3. **completed** - Finished (end_date < now)
4. **cancelled** - Manually cancelled by admin

### **Type Categories:**
1. **regular** - Standard TKA schedule
2. **makeup** - Makeup test for absent students
3. **special** - Special schedule for specific schools

## 🎯 **Target Schools Logic**

### **All Schools (target_schools = null)**
- Schedule applies to all schools
- Visible in all teacher and student dashboards

### **Specific Schools (target_schools = [1, 2, 3])**
- Schedule applies only to specified schools
- Only visible in dashboards of targeted schools

## 📱 **Frontend Integration Points**

### **Super Admin Dashboard**
- **Create Schedule**: Form to create new TKA schedule
- **Manage Schedules**: List, edit, cancel, delete schedules
- **Schedule Calendar**: Visual calendar view
- **Statistics**: Overview of schedule statistics

### **Teacher Dashboard**
- **Upcoming Schedules**: List of upcoming TKA schedules
- **Schedule Notifications**: Alerts about new schedules
- **Schedule Details**: Detailed view of each schedule

### **Student Dashboard**
- **Upcoming Schedules**: List of upcoming TKA schedules
- **Schedule Notifications**: Alerts about new schedules
- **Schedule Details**: Detailed view of each schedule

## 🚀 **Usage Examples**

### **Create New Schedule**
```javascript
const response = await fetch('/api/tka-schedules', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    title: 'TKA Gelombang 4 - Tahun 2024',
    description: 'Tes Kemampuan Akademik gelombang keempat',
    start_date: '2024-10-15 08:00:00',
    end_date: '2024-10-15 12:00:00',
    type: 'regular',
    instructions: 'Siswa diharapkan datang tepat waktu',
    target_schools: null
  })
});
```

### **Get Upcoming Schedules for School**
```javascript
const response = await fetch('/api/tka-schedules/upcoming?school_id=1');
const data = await response.json();
```

### **Cancel Schedule**
```javascript
const response = await fetch('/api/tka-schedules/1/cancel', {
  method: 'POST'
});
```

## 🔮 **Future Enhancements**

### **1. Automated Status Updates**
- Cron job to automatically update status from 'scheduled' to 'ongoing' to 'completed'

### **2. Email Notifications**
- Send email notifications to teachers and students about new schedules

### **3. SMS Notifications**
- Send SMS reminders before TKA starts

### **4. Calendar Integration**
- Export schedules to Google Calendar, Outlook, etc.

### **5. Advanced Filtering**
- Filter by date range, school type, schedule type
- Search functionality

### **6. Bulk Operations**
- Create multiple schedules at once
- Bulk cancel/delete operations

### **7. Schedule Templates**
- Save common schedule patterns as templates
- Quick create from templates

### **8. Conflict Detection**
- Detect overlapping schedules
- Warn about potential conflicts

---

**TKA Scheduling feature is now ready for integration with Super Admin, Teacher, and Student dashboards!** ✅
