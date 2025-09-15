# Software Requirements Specification (SRS)

## Hệ thống Quản lý Lịch hẹn (Appointment Management System)

### 1. Tổng quan hệ thống

#### 1.1 Mục đích

Hệ thống quản lý lịch hẹn là một API RESTful được xây dựng bằng Laravel, cung cấp các chức năng quản lý lịch hẹn, khách hàng, nhân viên, dịch vụ và loại lịch hẹn.

#### 1.2 Phạm vi

-   Quản lý lịch hẹn với đầy đủ CRUD operations
-   Quản lý thông tin khách hàng (contacts)
-   Quản lý thông tin nhân viên (staff)
-   Quản lý dịch vụ (services)
-   Quản lý loại lịch hẹn (appointment types)
-   Quản lý cài đặt hệ thống (settings)

#### 1.3 Kiến trúc hệ thống

-   **Backend**: Laravel 11.x với PHP 8.2+
-   **Database**: MySQL/PostgreSQL
-   **API**: RESTful API với JSON responses
-   **Authentication**: Laravel Sanctum (tùy chọn)

### 2. Cấu trúc dữ liệu

#### 2.1 Bảng Appointments

-   `id` (string, 10): Khóa chính
-   `title` (string, 200): Tiêu đề lịch hẹn
-   `type_id` (string, 10): ID loại lịch hẹn (foreign key)
-   `contact_id` (string, 10): ID khách hàng (foreign key)
-   `staff_id` (string, 10): ID nhân viên (foreign key)
-   `start_time` (bigint): Thời gian bắt đầu (timestamp)
-   `end_time` (bigint): Thời gian kết thúc (timestamp)

#### 2.2 Bảng Appointment Types

-   `id` (string, 10): Khóa chính
-   `label` (string, 100): Tên loại lịch hẹn
-   `color` (string, 7): Màu sắc (hex code)
-   `deleted_at` (timestamp): Soft delete
-   `created_at`, `updated_at` (timestamp): Timestamps

#### 2.3 Bảng Contacts

-   `id` (string, 10): Khóa chính
-   `avatar` (text): URL ảnh đại diện
-   `first_name` (string, 100): Tên
-   `last_name` (string, 100): Họ
-   `email` (string, 200): Email
-   `phone_number` (string, 50): Số điện thoại

#### 2.4 Bảng Staff

-   `id` (string, 10): Khóa chính
-   `avatar` (text): URL ảnh đại diện
-   `name` (string, 100): Tên nhân viên
-   `visible` (boolean): Hiển thị trong lịch

#### 2.5 Bảng Services

-   `id` (string, 10): Khóa chính
-   `name` (string, 200): Tên dịch vụ

#### 2.6 Bảng quan hệ

-   `appointment_services`: Quan hệ many-to-many giữa appointments và services
-   `staff_services`: Quan hệ many-to-many giữa staff và services

### 3. API Endpoints

#### 3.1 Appointments Management

##### GET /api/appointments

**Mô tả**: Lấy danh sách lịch hẹn với các bộ lọc
**Query Parameters**:

-   `start_date`: Lọc từ ngày (format: Y-m-d)
-   `end_date`: Lọc đến ngày (format: Y-m-d)
-   `staff_id`: Lọc theo nhân viên
-   `contact_id`: Lọc theo khách hàng
-   `type_id`: Lọc theo loại lịch hẹn
-   `search`: Tìm kiếm theo tiêu đề

**Response**: Array of AppointmentResource

##### POST /api/appointments

**Mô tả**: Tạo lịch hẹn mới
**Request Body**:

```json
{
    "title": "string (required, max:200)",
    "type_id": "string (optional, exists:appointment_types,id)",
    "contact_id": "string (optional, exists:contacts,id)",
    "staff_id": "string (optional, exists:staff,id)",
    "start": "integer (optional, timestamp)",
    "end": "integer (optional, timestamp)",
    "start_time": "integer (optional, timestamp)",
    "end_time": "integer (optional, timestamp)",
    "service_ids": "array (optional, array of service IDs)"
}
```

**Response**: AppointmentResource (201 Created)

##### GET /api/appointments/{id}

**Mô tả**: Lấy thông tin chi tiết lịch hẹn
**Response**: AppointmentResource

##### PUT /api/appointments/{id}

**Mô tả**: Cập nhật lịch hẹn
**Request Body**: Tương tự POST nhưng tất cả fields đều optional
**Response**: AppointmentResource

##### DELETE /api/appointments/{id}

**Mô tả**: Xóa lịch hẹn
**Response**: 204 No Content

#### 3.2 Contacts Management

##### GET /api/contacts

**Mô tả**: Lấy danh sách khách hàng (tối đa 5 kết quả)
**Query Parameters**:

-   `search`: Tìm kiếm theo tên, email, số điện thoại

**Response**: Array of ContactResource

##### GET /api/contacts/paginated

**Mô tả**: Lấy danh sách khách hàng có phân trang (25 kết quả/trang)
**Query Parameters**:

-   `search`: Tìm kiếm theo tên, email, số điện thoại

**Response**: Paginated ContactResource collection

##### POST /api/contacts

**Mô tả**: Tạo khách hàng mới
**Request Body**:

```json
{
    "first_name": "string (required, max:100)",
    "last_name": "string (optional, max:100)",
    "email": "string (optional, max:200, email format)",
    "phone_number": "string (required, regex: /^\\+?[1-9][0-9]{7,14}$/)",
    "avatar": "string (optional)"
}
```

**Response**: ContactResource (201 Created)

##### GET /api/contacts/{id}

**Mô tả**: Lấy thông tin chi tiết khách hàng
**Response**: ContactResource

##### PUT /api/contacts/{id}

**Mô tả**: Cập nhật thông tin khách hàng
**Request Body**: Tương tự POST nhưng tất cả fields đều optional
**Response**: ContactResource

##### DELETE /api/contacts/{id}

**Mô tả**: Xóa khách hàng
**Validation**: Không thể xóa khách hàng có lịch hẹn
**Response**: 204 No Content hoặc 422 Unprocessable Entity

#### 3.3 Staff Management

##### GET /api/staff

**Mô tả**: Lấy danh sách nhân viên
**Query Parameters**:

-   `search`: Tìm kiếm theo tên

**Response**: Array of StaffResource

##### POST /api/staff

**Mô tả**: Tạo nhân viên mới
**Request Body**:

```json
{
    "name": "string (required, max:100)",
    "avatar": "string (optional)",
    "visible": "integer (optional, 0 or 1, default: 1)",
    "service_ids": "array (optional, array of service IDs)"
}
```

**Response**: StaffResource (201 Created)

##### GET /api/staff/{id}

**Mô tả**: Lấy thông tin chi tiết nhân viên
**Response**: StaffResource

##### PUT /api/staff/{id}

**Mô tả**: Cập nhật thông tin nhân viên
**Request Body**: Tương tự POST nhưng tất cả fields đều optional
**Response**: StaffResource

##### DELETE /api/staff/{id}

**Mô tả**: Xóa nhân viên
**Validation**: Không thể xóa nhân viên có lịch hẹn
**Response**: 204 No Content hoặc 422 Unprocessable Entity

#### 3.4 Services Management

##### GET /api/services

**Mô tả**: Lấy danh sách dịch vụ
**Query Parameters**:

-   `search`: Tìm kiếm theo tên

**Response**: Array of ServiceResource

#### 3.5 Appointment Types Management

##### GET /api/appointment-types hoặc /api/appointment_types

**Mô tả**: Lấy danh sách loại lịch hẹn
**Query Parameters**:

-   `include_deleted`: Bao gồm các loại đã xóa (boolean)
-   `search`: Tìm kiếm theo tên

**Response**: Array of AppointmentTypeResource

##### POST /api/appointment-types hoặc /api/appointment_types

**Mô tả**: Tạo loại lịch hẹn mới
**Request Body**:

```json
{
    "label": "string (required)",
    "color": "string (optional, hex color)"
}
```

**Response**: AppointmentTypeResource (201 Created)

##### GET /api/appointment-types/{id} hoặc /api/appointment_types/{id}

**Mô tả**: Lấy thông tin chi tiết loại lịch hẹn
**Response**: AppointmentTypeResource

##### PUT /api/appointment-types/{id} hoặc /api/appointment_types/{id}

**Mô tả**: Cập nhật loại lịch hẹn
**Request Body**:

```json
{
    "label": "string (optional)",
    "color": "string (optional, hex color)",
    "deleted_at": "boolean (optional, true để soft delete)"
}
```

**Response**: AppointmentTypeResource

##### DELETE /api/appointment-types/{id} hoặc /api/appointment_types/{id}

**Mô tả**: Soft delete loại lịch hẹn
**Response**: 204 No Content

##### POST /api/appointment-types/{id}/restore hoặc /api/appointment_types/{id}/restore

**Mô tả**: Khôi phục loại lịch hẹn đã xóa
**Response**: AppointmentTypeResource

#### 3.6 Settings Management

##### GET /api/settings

**Mô tả**: Lấy cài đặt hệ thống
**Response**:

```json
{
    "visibleStaffs": ["array of visible staff IDs"]
}
```

##### PUT /api/settings

**Mô tả**: Cập nhật cài đặt hệ thống
**Response**:

```json
{
    "visibleStaffs": ["array of visible staff IDs"]
}
```

### 4. Data Resources

#### 4.1 AppointmentResource

```json
{
    "id": "string",
    "title": "string",
    "start": "integer (timestamp)",
    "end": "integer (timestamp)",
    "appointment_type": {
        "id": "string",
        "label": "string",
        "color": "string"
    },
    "contact": {
        "id": "string",
        "name": "string (full name)",
        "avatar": "string"
    },
    "staff": {
        "id": "string",
        "name": "string",
        "avatar": "string"
    },
    "services": [
        {
            "id": "string",
            "name": "string"
        }
    ]
}
```

#### 4.2 ContactResource

```json
{
    "id": "string",
    "name": "string (full name for backward compatibility)",
    "first_name": "string",
    "last_name": "string",
    "email": "string",
    "phone_number": "string",
    "avatar": "string",
    "appointments_count": "integer (when loaded)",
    "appointments": "array of AppointmentResource (when loaded)"
}
```

#### 4.3 StaffResource

```json
{
    "id": "string",
    "avatar": "string",
    "name": "string",
    "service_ids": "array of service IDs (when loaded)",
    "visible": "boolean"
}
```

#### 4.4 ServiceResource

```json
{
    "id": "string",
    "name": "string",
    "appointments_count": "integer (when loaded)",
    "staff_count": "integer (when loaded)",
    "appointments": "array of AppointmentResource (when loaded)",
    "staff": "array of StaffResource (when loaded)"
}
```

#### 4.5 AppointmentTypeResource

```json
{
    "id": "string",
    "label": "string",
    "color": "string",
    "deleted_at": "timestamp or null",
    "appointments_count": "integer (when loaded)",
    "appointments": "array of AppointmentResource (when loaded)"
}
```

### 5. Business Rules

#### 5.1 Appointments

-   Mỗi lịch hẹn phải có tiêu đề
-   Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc
-   Có thể liên kết với nhiều dịch vụ
-   Có thể có hoặc không có khách hàng, nhân viên, loại lịch hẹn

#### 5.2 Contacts

-   Tên là bắt buộc
-   Số điện thoại là bắt buộc và phải đúng format
-   Email phải đúng format nếu có
-   Không thể xóa khách hàng có lịch hẹn

#### 5.3 Staff

-   Tên là bắt buộc
-   Có thể liên kết với nhiều dịch vụ
-   Có thể ẩn/hiện trong lịch
-   Không thể xóa nhân viên có lịch hẹn

#### 5.4 Appointment Types

-   Sử dụng soft delete
-   Có thể khôi phục sau khi xóa
-   Có thể có màu sắc để phân biệt

#### 5.5 Services

-   Chỉ có thể xem danh sách (read-only)
-   Có thể liên kết với nhiều nhân viên và lịch hẹn

### 6. Error Handling

#### 6.1 HTTP Status Codes

-   `200 OK`: Thành công
-   `201 Created`: Tạo mới thành công
-   `204 No Content`: Xóa thành công
-   `422 Unprocessable Entity`: Lỗi validation
-   `404 Not Found`: Không tìm thấy resource
-   `500 Internal Server Error`: Lỗi server

#### 6.2 Error Response Format

```json
{
    "message": "Error message",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

### 7. Security

#### 7.1 Authentication

-   Hệ thống hỗ trợ Laravel Sanctum cho authentication
-   Có thể bật/tắt authentication cho các endpoints

#### 7.2 Validation

-   Tất cả input đều được validate
-   Sử dụng Laravel validation rules
-   Kiểm tra foreign key constraints

### 8. Performance

#### 8.1 Database Optimization

-   Sử dụng indexes cho các trường thường xuyên query
-   Eager loading để tránh N+1 queries
-   Soft delete cho appointment types

#### 8.2 API Optimization

-   Pagination cho danh sách contacts
-   Limit kết quả cho contacts search (5 items)
-   Resource transformers để tối ưu response size

### 9. Deployment

#### 9.1 Environment Requirements

-   PHP 8.2+
-   Laravel 11.x
-   MySQL/PostgreSQL
-   Composer

#### 9.2 Docker Support

-   Có sẵn Dockerfile và docker-compose.yml
-   Scripts để build và run với Docker

### 10. API Documentation

#### 10.1 Base URL

-   Development: `http://localhost:8000/api`
-   Production: `https://your-domain.com/api`

#### 10.2 Content-Type

-   Request: `application/json`
-   Response: `application/json`

#### 10.3 CORS

-   Hệ thống hỗ trợ CORS cho cross-origin requests
-   Cấu hình trong `config/cors.php`

---

**Phiên bản**: 1.0  
**Ngày cập nhật**: 2025-01-27  
**Tác giả**: Development Team
