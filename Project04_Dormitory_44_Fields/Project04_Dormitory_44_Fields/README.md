# Project 04 — ระบบบริหารจัดการหอพักนักศึกษา (44 ฟิลด์)

ปรับจากโปรเจกต์ 04 ที่ผู้ใช้ให้มา: เดิม 4 ตาราง 24 ฟิลด์ → 5 ตารางธุรกิจ 44 ฟิลด์
ไฟล์ของโปรเจกต์ 08 ไม่เกี่ยวข้องกับชุดนี้

| ตาราง | ฟิลด์ |
|---|---:|
| users | 10 |
| room_types | 7 |
| rooms | 8 |
| bookings | 11 |
| buildings | 8 |
| รวม | 44 |

PK และ FK นับเป็นฟิลด์ด้วย แต่ไม่นับ index หรือคอลัมน์ในตารางระบบ migrations ของ Laravel

## อัปเกรดระบบเดิม (แนะนำ)

1. สำรองฐานข้อมูลเดิม และโฟลเดอร์โปรเจกต์ไว้ก่อน
2. คัดลอกโค้ดชุดนี้ทับโปรเจกต์ 04 เดิม โดยเก็บ backend/.env, vendor และข้อมูลเดิมไว้
3. เปิด MySQL และ CMD ที่ backend ของโปรเจกต์ 04 แล้วรัน:

```bat
php artisan migrate
php artisan optimize:clear
php artisan serve --port=8080
```

หรือรัน `upgrade_database.bat` จากโฟลเดอร์หลัก แล้ว `start.bat`
Migration ใหม่เพิ่มอาคารและ 12 ฟิลด์ในตารางเดิม ไม่ลบตาราง ไม่ลบข้อมูล และไม่ใส่ seed อัตโนมัติ
ห้ามใช้ start.bat รุ่นเก่า เพราะรุ่นเดิมมี migrate:fresh ซึ่งล้างข้อมูล

## ติดตั้งใหม่

ปรับ Laravel 11 → Laravel 12 ตามคู่มืออัปเกรด และคง Flutter ตามเดิม ต้องใช้ PHP 8.2+, Composer และ MySQL
เปิด MySQL ใน XAMPP แล้วเปิด CMD ที่ backend:

```bat
copy .env.example .env
composer install
php setup_db.php
php artisan key:generate
php artisan migrate
```

ตรวจการเชื่อมต่อใน .env ก่อน setup_db.php ค่าเริ่มต้นคือ dormitory_db, root, รหัสผ่านว่าง (เปลี่ยนตามเครื่องจริง)
หากต้องการข้อมูลตัวอย่างและฐานข้อมูลยังว่างเท่านั้น รัน `php artisan db:seed`
บัญชีสาธิตเดิม: admin@dormitory.com / admin1234 และ user@dormitory.com / user1234
จากนั้น `php artisan serve --port=8080` แล้วเปิด http://localhost:8080/login
มี install_first_time.bat สำหรับติดตั้งและ migrate โดยไม่ seed และ start.bat สำหรับเปิดเซิร์ฟเวอร์อย่างเดียว

แก้ข้อผิดพลาด security advisories ของ Laravel 11 โดยเปลี่ยน constraint เป็น Laravel ^12.0 และคง security blocking ของ Composer ไว้ หากมี composer.lock รุ่นเก่าให้รัน composer update --with-all-dependencies ก่อน ไม่ใช้ --ignore-platform-reqs เพื่อข้ามข้อกำหนดเครื่อง

## ทางเลือก SQL โดยตรง

- database/schema_44_fields.sql: ฐานข้อมูลว่างเท่านั้น (เฉพาะโครงสร้าง ไม่มีข้อมูลตัวอย่าง)
- database/upgrade_from_24_to_44.sql: ฐานข้อมูลเดิม 24 ฟิลด์เท่านั้น รันครั้งเดียว
- database/verify_44_fields.sql: ตรวจจำนวนฟิลด์จริง ผลรวมต้องเท่ากับ 44

เลือกฐานข้อมูลที่ต้องการก่อน import ผ่าน phpMyAdmin สำหรับ Laravel ให้ใช้ migration เป็นหลัก ไม่รัน SQL และ migration ซ้ำกัน: SQL ไม่บันทึกประวัติใน migrations

## ใช้งานฟิลด์ใหม่

- สมัครสมาชิก: รหัสนักศึกษา คณะ สาขาวิชา (ไม่บังคับ)
- ผู้ดูแล /admin/dashboard: เพิ่มอาคาร แล้วเลือกอาคารเมื่อเพิ่มห้องใหม่
- ผู้ดูแล /admin/room-types: กำหนดรายละเอียด จำนวนผู้พัก และเงินประกัน
- สมาชิก /rooms: ระบุวันสิ้นสุดที่คาดไว้และหมายเหตุในการจอง
- /my-bookings: แสดงค่าเช่าและเงินประกันที่บันทึกจากประเภทห้อง ณ วันจอง
- /api-docs: แสดง schema ใหม่ 5 ตาราง 44 ฟิลด์
- REST API เดิมรับฟิลด์ใหม่ตาม docs/api-new-fields.md; Flutter models อ่านค่าที่เพิ่มได้ และคำขอเก่าที่ไม่ส่งฟิลด์ใหม่ยังรองรับ หน้า Flutter เดิมไม่ได้เพิ่มฟอร์มอาคารใหม่ ใช้หน้า Blade สำหรับกรอกข้อมูลใหม่

ข้อมูลราคาของสัญญาเก่ายังคง NULL เพราะไม่มีหลักฐานราคาตอนจอง วันสร้างรายการเก่าไม่ถูกกำหนดเป็นวันอัปเกรด
ดู docs/database.md สำหรับพจนานุกรมทุกคอลัมน์และ docs/verification.md สำหรับขอบเขตการตรวจสอบ

## แก้ Composer ของชุดก่อนหน้า

เปิด CMD ใน backend:

```bat
composer require "laravel/framework:12.*" --with-all-dependencies
```

หากคัดลอก composer.json จากชุดแก้ไขนี้ไปใช้ ให้ใช้ `composer update --with-all-dependencies` แทนคำสั่งข้างต้น
รอ Composer สำเร็จก่อนใช้ Artisan; จากนั้น `php artisan --version` ต้องแสดง Laravel Framework 12.x
ไม่มีการปิด audit.block-insecure และไม่มีการเปลี่ยนโครงสร้าง 44 ฟิลด์ในแพตช์นี้
อ้างอิง: https://laravel.com/docs/12.x/upgrade

ยังไม่ยืนยันการติดตั้ง dependencies หรือการรัน Laravel 12 จริงในสภาพแวดล้อมจัดทำ เนื่องจากไม่มี PHP/Composer จึงต้องตรวจผลติดตั้งบนเครื่องผู้ใช้
