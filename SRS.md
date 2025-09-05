## Software Requirement Specification (SRS)

### Appointment Management System API
Version: 1.0  
Date: September 2025  
Author: Development Team

---

## 1. Introduction

### 1.1 Purpose
Tài liệu mô tả chi tiết yêu cầu chức năng và phi chức năng của hệ thống API quản lý lịch hẹn (Appointment Management System) xây dựng bằng Laravel, phục vụ React frontend.

### 1.2 Scope
Hệ thống cung cấp API để quản lý: appointments, contacts, staff, services, appointment types, settings; hỗ trợ tìm kiếm, lọc, soft delete, tối ưu phản hồi cho frontend.

### 1.3 Definitions & Abbreviations
- API: Application Programming Interface
- CRUD: Create, Read, Update, Delete
- REST: Representational State Transfer
- CORS: Cross-Origin Resource Sharing
- ORM: Object-Relational Mapping
- SRS: Software Requirement Specification

---

## 2. Overall Description

### 2.1 Product Perspective

Client (React) ⇄ HTTP/JSON ⇄ Laravel API ⇄ MySQL Database

### 2.2 Product Functions
1) Quản lý lịch hẹn (appointments)
2) Quản lý khách hàng (contacts)
3) Quản lý nhân viên (staff)
4) Quản lý dịch vụ (services)
5) Quản lý loại lịch hẹn (appointment types) + Soft delete/restore
6) Quản lý cài đặt (settings) + Bulk update
7) Tìm kiếm và lọc
8) Endpoint tổng hợp dữ liệu (combined data)

---

## 3. Specific Requirements

### 3.1 Functional Requirements

#### 3.1.1 Appointment Management

- FR-001 Create Appointment  
Endpoint: POST `/api/appointments`  
Input: `title, type_id, contact_id, staff_id, start|start_time, end|end_time, service_ids[]`  
Output: 201 + Appointment object (JSON)

```php
// app/Http/Controllers/Api/AppointmentController.php (trích yếu)
public function store(Request $request): JsonResponse
{
    $startTime = $request->start ?? $request->start_time;
    $endTime = $request->end ?? $request->end_time;

    $appointment = Appointment::create([
        'id' => Str::random(10),
        'title' => $request->title,
        'type_id' => $request->type_id,
        'contact_id' => $request->contact_id,
        'staff_id' => $request->staff_id,
        'start_time' => $startTime,
        'end_time' => $endTime,
    ]);

    if ($request->has('service_ids')) {
        $appointment->services()->attach($request->service_ids);
    }

    $appointment->load(['services']);
    return response()->json(new AppointmentResource($appointment), 201);
}
```

- FR-002 Read Appointments (list + filters)  
Endpoint: GET `/api/appointments`  
Filters: `start_date, end_date, staff_id, contact_id, type_id, search`

```php
public function index(Request $request): JsonResponse
{
    $query = Appointment::with(['services']);
    if ($request->has('start_date') && $request->has('end_date')) {
        $startTime = strtotime($request->start_date) * 1000;
        $endTime = strtotime($request->end_date) * 1000;
        $query->whereBetween('start_time', [$startTime, $endTime]);
    }
    if ($request->has('staff_id')) $query->where('staff_id', $request->staff_id);
    if ($request->has('contact_id')) $query->where('contact_id', $request->contact_id);
    if ($request->has('type_id')) $query->where('type_id', $request->type_id);
    if ($request->has('search')) $query->where('title', 'like', '%'.$request->search.'%');
    $appointments = $query->orderBy('start_time')->get();
    return response()->json(AppointmentResource::collection($appointments));
}
```

- FR-003 Update Appointment  
Endpoint: PUT `/api/appointments/{id}`  
Body: trường cần cập nhật + `service_ids[]` (sync)

```php
public function update(Request $request, string $id): JsonResponse
{
    $appointment = Appointment::findOrFail($id);
    $appointment->update($request->only(['title','type_id','contact_id','staff_id','start_time','end_time']));
    if ($request->has('service_ids')) $appointment->services()->sync($request->service_ids);
    $appointment->load(['services']);
    return response()->json(new AppointmentResource($appointment));
}
```

- FR-004 Delete Appointment  
Endpoint: DELETE `/api/appointments/{id}` → 204 No Content

#### 3.1.2 Appointment Type Management

- FR-005 Create/Read/Update/Delete (Soft Delete)  
Endpoints: REST + `DELETE` hoặc `PUT` với `deleted_at` để soft delete

```php
public function update(Request $request, string $id): JsonResponse
{
    $appointmentType = AppointmentType::withTrashed()->findOrFail($id);
    if ($request->has('deleted_at') && $request->deleted_at) {
        $appointmentType->delete();
    } else {
        $appointmentType->update($request->only(['label','color']));
    }
    return response()->json(new AppointmentTypeResource($appointmentType));
}

public function destroy(string $id): JsonResponse
{
    $appointmentType = AppointmentType::findOrFail($id);
    if ($appointmentType->appointments()->count() > 0) {
        return response()->json(['success'=>false,'message'=>'Cannot delete appointment type with existing appointments'], 422);
    }
    $appointmentType->delete();
    return response()->json([], 204);
}
```

- FR-006 Restore  
Endpoint: POST `/api/appointment_types/{id}/restore`

#### 3.1.3 Settings Management

- FR-007 Bulk Update Settings  
Endpoint: PUT `/api/settings`  
Body: `{ visibleContacts: string[] }`

```php
public function bulkUpdate(Request $request): JsonResponse
{
    $updated = [];
    if ($request->has('visibleContacts')) {
        $row = Setting::where('setting_key','visibleContacts')->first();
        $payload = json_encode($request->visibleContacts);
        if ($row) { $row->update(['setting_value'=>$payload]); }
        else { Setting::create(['setting_key'=>'visibleContacts','setting_value'=>$payload]); }
        $updated['visibleContacts'] = $request->visibleContacts;
    }
    return response()->json($updated);
}
```

- FR-008 Get Settings (Key-Value)  
Endpoint: GET `/api/settings`  
Trả về object, tự parse JSON strings.

#### 3.1.4 Combined Data Endpoint

- FR-009 Get All Data  
Endpoint: GET `/api/data`  
Trả về: appointments, appointment_types, contacts, staff, services, settings.

### 3.2 Non-Functional Requirements

- Performance: < 500ms với queries đã index; eager loading để tránh N+1.  
- Security: CORS mở cho frontend; mã trạng thái HTTP chính xác; không phơi bày dữ liệu nhạy cảm.  
- Compatibility: JSON response nhất quán; mapping field phù hợp React (`start`/`end`).

---

## 4. Architecture & Components

### 4.1 Routes (`routes/api.php`)
- `Route::apiResource()` cho CRUD chuẩn.
- Routes bổ sung: underscore cho React, restore, settings bulk update, combined `/api/data`.

### 4.2 Controllers (tác dụng các hàm chính)

- AppointmentController
  - `index(Request)`: Lấy danh sách; áp dụng filter từ query string; eager loading `services`; trả về collection resource.
  - `store(Request)`: Map `start|end` → `start_time|end_time`; tạo ID (Str::random); attach services; trả 201.
  - `show($id)`: Tải 1 bản ghi + `services`.
  - `update(Request,$id)`: Cập nhật trường cho phép; `sync` services nếu có.
  - `destroy($id)`: Xóa bản ghi, trả 204.

- AppointmentTypeController
  - `index(Request)`: Lọc `include_deleted`, search theo `label`, `withCount('appointments')`.
  - `store(Request)`: Tạo mới với ID ngẫu nhiên.
  - `update(Request,$id)`: Hỗ trợ soft delete qua PUT (nếu có `deleted_at`).
  - `destroy($id)`: Ràng buộc nghiệp vụ: không xóa nếu còn appointments.
  - `restore($id)`: Khôi phục bản ghi soft-deleted.

- ContactController / ServiceController / StaffController
  - `index(Request)`: Search theo `name`, kèm `withCount`/`with` cần thiết.
  - `store/show/update/destroy`: CRUD cơ bản; với Staff/Service xử lý quan hệ many-to-many.

- SettingController
  - `index(Request)`: Trả về object key-value; tự `json_decode` khi cần.
  - `store/update/destroy`: CRUD cơ bản.
  - `getByKey/updateByKey`: Lấy/cập nhật theo `setting_key`.
  - `bulkUpdate(Request)`: Cập nhật nhiều settings (ví dụ `visibleContacts`).

### 4.3 Models (Eloquent)
- `Appointment`: `$fillable`, casts, `belongsTo` Contact/Staff/AppointmentType, `belongsToMany` Service.
- `AppointmentType`: `SoftDeletes`, timestamps=true, `hasMany` Appointment.
- `Contact`, `Service`, `Staff`, `Setting`: `$fillable` phù hợp; quan hệ 1-n, n-n như thiết kế.

### 4.4 Resources (API Resources)
- Chuẩn hóa phản hồi JSON, ẩn/hiện trường theo `whenLoaded`, chuyển `start_time|end_time` → `start|end`, thêm `service_ids`.

### 4.5 Middleware (CORS)
- `CorsMiddleware`: Xử lý preflight OPTIONS; thêm CORS headers cho mọi phản hồi; cấu hình prepend vào nhóm `api` trong `bootstrap/app.php`.

---

## 5. Database Design

### 5.1 Entities & Relationships
- Appointments (N-N với Services, N-1 tới Contact/Staff/AppointmentType)
- AppointmentTypes (1-N tới Appointments, SoftDeletes)
- Contacts (1-N Appointments)
- Staff (1-N Appointments, N-N Services)
- Services (N-N Appointments, N-N Staff)
- Settings (key-value)

### 5.2 Indexes đề xuất
- `appointments(start_time)`, `appointments(end_time)`, `appointments(type_id)`, `appointments(contact_id)`, `appointments(staff_id)`
- `appointment_types(deleted_at)`

---

## 6. API Specifications

### 6.1 Base URLs
- Dev: `http://127.0.0.1:8000/api`
- Prod: `https://your-domain.com/api`

### 6.2 Endpoints Summary
- Appointments: RESTful CRUD
- Appointment Types: RESTful + `POST /appointment_types/{id}/restore`
- Contacts/Services/Staff: RESTful CRUD
- Settings: RESTful + `PUT /settings` (bulk) + key-based get/update
- Data: `GET /data` (combined)

### 6.3 Response Format (ví dụ)
```json
{
  "id": "RtKf8pxYPZ",
  "title": "Consultation Appointment",
  "type_id": "1",
  "contact_id": "1",
  "staff_id": "1",
  "service_ids": ["1","2"],
  "start": 1755147900000,
  "end": 1755149700000
}
```

### 6.4 Error Responses (ví dụ)
```json
{
  "error": "Failed to create appointment",
  "message": "Database connection error"
}
```

---

## 7. Security
- CORS: Cho phép `*`, methods: `GET, POST, PUT, DELETE, OPTIONS`, headers chuẩn.
- Authorization/Authentication: (chưa áp dụng trong phạm vi này; có thể thêm Sanctum/JWT).
- Business constraints: Không xóa entity khi còn ràng buộc (ví dụ: type có appointments).

---

## 8. Performance
- Eager loading quan hệ để tránh N+1.
- Indexes trên các cột lọc/sort thường dùng.
- Combined endpoint `/api/data` giảm số lượng request đầu trang.

---

## 9. Testing
- Feature tests cho CRUD chính, kiểm tra mã trạng thái và cấu trúc JSON.
- Integration flow: Create → Read → Update → Delete cho mỗi resource.

---

## 10. Glossary
- Eloquent ORM: Lớp ánh xạ đối tượng-quan hệ của Laravel.
- Resource: Lớp chuyển đổi Model → JSON response.
- Middleware: Lớp xử lý trước/sau khi vào Controller.
- Soft Delete: Đánh dấu `deleted_at` thay vì xóa cứng.
- Eager Loading: Tải quan hệ trước bằng `with()`.
- CORS: Cơ chế chia sẻ tài nguyên chéo miền.

---

## 11. Appendices

### 11.1 Key Implementation Notes
- ID chuỗi độ dài 10 (`Str::random(10)`) được dùng cho primary keys.
- Tương thích React: dùng `start/end` ở response; map `start|end` khi nhận request.
- Routes duplicate dạng underscore (`appointment_types`) cho compatibility.

### 11.2 Future Work
- Thêm auth (Sanctum/JWT), rate limiting, audit logs.
- Thêm pagination chuẩn ở các list endpoints.
- Viết tài liệu OpenAPI/Swagger.


