# พจนานุกรมข้อมูล Project 04: 44 ฟิลด์

นับคอลัมน์จริง รวม PK/FK จำนวน 5 ตารางธุรกิจ ไม่รวมตาราง `migrations` ของ Laravel (3 ฟิลด์) และไม่นับ index เป็นฟิลด์ หากนับทุกตารางในฐานข้อมูล Laravel จะมี 47 คอลัมน์

| ตาราง | เดิม | เพิ่ม | รวม |
|---|---:|---:|---:|
| users | 7 | 3 | 10 |
| room_types | 4 | 3 | 7 |
| rooms | 7 | 1 | 8 |
| bookings | 6 | 5 | 11 |
| buildings | 0 | 8 | 8 |
| รวม | 24 | 20 | 44 |

## users (10 ฟิลด์)

| ลำดับ | ฟิลด์ | ชนิด / เงื่อนไข | ความหมาย |
|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY | PK ผู้ใช้ |
| 2 | `name` | VARCHAR(255) NOT NULL | ชื่อผู้ใช้ |
| 3 | `email` | VARCHAR(255) NOT NULL UNIQUE | อีเมลไม่ซ้ำ |
| 4 | `password` | VARCHAR(255) NOT NULL | รหัสผ่านแบบ hash |
| 5 | `phone` | VARCHAR(255) NOT NULL UNIQUE | เบอร์โทรไม่ซ้ำ |
| 6 | `role` | ENUM('admin','user') NOT NULL DEFAULT 'user' | บทบาท |
| 7 | `created_at` | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | วันเวลาสมัคร |
| 8 | `student_code` | VARCHAR(30) NULL UNIQUE | รหัสนักศึกษา ไม่ซ้ำเมื่อระบุ |
| 9 | `faculty` | VARCHAR(150) NULL | คณะ |
| 10 | `major` | VARCHAR(150) NULL | สาขาวิชา |

## room_types (7 ฟิลด์)

| ลำดับ | ฟิลด์ | ชนิด / เงื่อนไข | ความหมาย |
|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY | PK ประเภทห้อง |
| 2 | `name` | VARCHAR(255) NOT NULL | ชื่อประเภท |
| 3 | `monthly_rate` | DECIMAL(10,2) NOT NULL | ค่าเช่าต่อเดือน |
| 4 | `created_at` | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | วันเวลาสร้าง |
| 5 | `description` | TEXT NULL | รายละเอียดประเภทห้อง |
| 6 | `capacity` | SMALLINT UNSIGNED NULL | จำนวนผู้พักที่รองรับ |
| 7 | `deposit_amount` | DECIMAL(10,2) NULL | เงินประกันตามประเภทห้อง ไม่ใช่ยอดรับชำระ |

## rooms (8 ฟิลด์)

| ลำดับ | ฟิลด์ | ชนิด / เงื่อนไข | ความหมาย |
|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY | PK ห้อง |
| 2 | `room_number` | VARCHAR(255) NOT NULL UNIQUE | เลขห้องไม่ซ้ำทั้งระบบตามกฎเดิม |
| 3 | `room_type_id` | BIGINT UNSIGNED NOT NULL | FK → room_types.id |
| 4 | `floor` | INT NOT NULL | ชั้น |
| 5 | `status` | ENUM('available','occupied','maintenance') NOT NULL DEFAULT 'available' | สถานะห้อง |
| 6 | `description` | VARCHAR(255) NULL | รายละเอียดห้อง |
| 7 | `created_at` | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | วันเวลาสร้าง |
| 8 | `building_id` | BIGINT UNSIGNED NULL | อาคารที่ตั้ง FK → buildings.id |

## bookings (11 ฟิลด์)

| ลำดับ | ฟิลด์ | ชนิด / เงื่อนไข | ความหมาย |
|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY | PK การจอง |
| 2 | `tracking_code` | VARCHAR(255) NOT NULL UNIQUE | รหัสติดตามไม่ซ้ำ |
| 3 | `user_id` | BIGINT UNSIGNED NOT NULL | FK → users.id |
| 4 | `room_id` | BIGINT UNSIGNED NOT NULL | FK → rooms.id |
| 5 | `check_in_date` | DATE NOT NULL | วันเข้าพัก |
| 6 | `status` | ENUM('pending','active','completed','cancelled') NOT NULL DEFAULT 'pending' | สถานะการจอง |
| 7 | `check_out_date` | DATE NULL | วันที่คาดว่าจะสิ้นสุดการพัก ต้องไม่น้อยกว่าวันเข้า |
| 8 | `monthly_rate` | DECIMAL(10,2) NULL | ค่าเช่ารายเดือนที่บันทึก ณ วันจอง |
| 9 | `deposit_amount` | DECIMAL(10,2) NULL | เงินประกันที่บันทึก ณ วันจอง |
| 10 | `notes` | TEXT NULL | หมายเหตุการจอง |
| 11 | `created_at` | TIMESTAMP NULL DEFAULT NULL | วันเวลาสร้างรายการใหม่; รายการเก่าคง NULL |

## buildings (8 ฟิลด์)

| ลำดับ | ฟิลด์ | ชนิด / เงื่อนไข | ความหมาย |
|---|---|---|---|
| 1 | `id` | BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY | PK รหัสอาคาร |
| 2 | `building_code` | VARCHAR(20) NOT NULL UNIQUE | รหัสอาคารไม่ซ้ำ |
| 3 | `name` | VARCHAR(100) NOT NULL | ชื่ออาคาร |
| 4 | `address` | TEXT NULL | ที่อยู่อาคาร |
| 5 | `total_floors` | SMALLINT UNSIGNED NOT NULL | จำนวนชั้น |
| 6 | `contact_phone` | VARCHAR(20) NULL | โทรศัพท์ติดต่ออาคาร |
| 7 | `description` | TEXT NULL | รายละเอียดอาคาร |
| 8 | `created_at` | TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP | วันเวลาสร้างอาคาร |

## ความสัมพันธ์

- buildings 1:N rooms
- room_types 1:N rooms
- users 1:N bookings
- rooms 1:N bookings

ฟิลด์ใหม่ในตารางเดิมอนุญาต NULL เพื่อรักษาข้อมูลเดิม ไม่เดาราคา/วันสร้างสัญญาเก่าที่ไม่มีหลักฐาน การจองใหม่เก็บค่าเช่าและเงินประกันจาก room_types ฝั่งเซิร์ฟเวอร์ ไม่รับยอดจากผู้จอง ราคาประวัติจึงไม่เปลี่ยนเมื่อแก้ราคาประเภทห้อง

เงินประกันเป็นข้อมูลจำนวนเงินที่กำหนด ไม่ได้หมายความว่ามีการชำระแล้ว วันที่ check_out_date คือวันสิ้นสุดที่คาดไว้ ไม่เปลี่ยนสถานะการจองอัตโนมัติ เลขห้องยัง unique ทั้งระบบตามเดิม อาคารใหม่ไม่เปลี่ยนกฎเลขห้อง
