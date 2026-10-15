<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Building;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $building = Building::create([
            'building_code' => 'DEMO-A', 'name' => 'อาคารตัวอย่าง A',
            'total_floors' => 4, 'address' => 'ข้อมูลสมมติสำหรับสาธิต',
            'contact_phone' => '0000000000', 'description' => 'ข้อมูลสาธิต ไม่ใช่อาคารจริง',
        ]);
        // 1. Seed Accounts (Admin & User with bcrypt hash)
        $admin = User::create([
            'name' => 'ผู้ดูแลหอพัก (Admin Manager)',
            'email' => 'admin@dormitory.com',
            'password' => Hash::make('admin1234'),
            'phone' => '0812345678',
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'สมชาย นักศึกษา (Student Tenant)',
            'email' => 'user@dormitory.com',
            'password' => Hash::make('user1234'),
            'phone' => '0898765432',
            'role' => 'user',
            'student_code' => 'DEMO0001', 'faculty' => 'คณะตัวอย่าง', 'major' => 'สาขาตัวอย่าง',
        ]);

        // 2. Seed Room Types
        $fan = RoomType::create([
            'name' => 'ห้องพัดลม (Standard Fan)',
            'monthly_rate' => 2500.00,
        ]);

        $singleAir = RoomType::create([
            'name' => 'ห้องแอร์เดี่ยว (Single Air-Con)',
            'monthly_rate' => 3800.00,
        ]);

        $deluxeAir = RoomType::create([
            'name' => 'ห้องแอร์คู่ (Deluxe Twin Air-Con)',
            'monthly_rate' => 4500.00,
        ]);

        $suite = RoomType::create([
            'name' => 'ห้องสวีทสูทนักศึกษา (Premium Suite)',
            'monthly_rate' => 5500.00,
        ]);

        foreach ([$fan, $singleAir, $deluxeAir, $suite] as $type) {
            $type->update(['capacity' => $type->id === $deluxeAir->id ? 2 : 1,
                'deposit_amount' => $type->monthly_rate,
                'description' => 'ข้อมูลตัวอย่างสำหรับสาธิต']);
        }
        // 3. Seed Rooms
        $r101 = Room::create([
            'room_number' => '101',
            'building_id' => $building->id,
            'room_type_id' => $fan->id,
            'floor' => 1,
            'status' => 'available',
            'description' => 'ห้องพัดลม ชั้น 1 ระเบียงกว้าง ใกล้ทางเข้าอาคาร',
        ]);

        $r102 = Room::create([
            'room_number' => '102',
            'building_id' => $building->id,
            'room_type_id' => $fan->id,
            'floor' => 1,
            'status' => 'occupied',
            'description' => 'ห้องพัดลม ชั้น 1 เตียงเดี่ยว โต๊ะทำงาน พัดลมเพดาน',
        ]);

        $r201 = Room::create([
            'room_number' => '201',
            'building_id' => $building->id,
            'room_type_id' => $singleAir->id,
            'floor' => 2,
            'status' => 'available',
            'description' => 'ห้องแอร์เดี่ยว ชั้น 2 วิวสวน แอร์ประหยัดไฟเบอร์ 5',
        ]);

        $r202 = Room::create([
            'room_number' => '202',
            'building_id' => $building->id,
            'room_type_id' => $singleAir->id,
            'floor' => 2,
            'status' => 'occupied',
            'description' => 'ห้องแอร์เดี่ยว ชั้น 2 ตู้เย็น เครื่องทำน้ำอุ่นครบชุด',
        ]);

        $r301 = Room::create([
            'room_number' => '301',
            'building_id' => $building->id,
            'room_type_id' => $deluxeAir->id,
            'floor' => 3,
            'status' => 'available',
            'description' => 'ห้องแอร์คู่ ชั้น 3 เตียงคู่ 2 หลัง เหมาะสำหรับรูมเมท',
        ]);

        $r302 = Room::create([
            'room_number' => '302',
            'building_id' => $building->id,
            'room_type_id' => $deluxeAir->id,
            'floor' => 3,
            'status' => 'maintenance',
            'description' => 'ห้องแอร์คู่ ชั้น 3 กำลังซ่อมบำรุงระบบเครื่องปรับอากาศ',
        ]);

        $r401 = Room::create([
            'room_number' => '401',
            'building_id' => $building->id,
            'room_type_id' => $suite->id,
            'floor' => 4,
            'status' => 'available',
            'description' => 'ห้องพรีเมียมสวีท ชั้น 4 ระเบียงส่วนตัว สมาร์ททีวี โซฟาพักผ่อน',
        ]);

        // 4. Seed Bookings
        Booking::create([
            'tracking_code' => 'DORM-2026001',
            'user_id' => $user->id,
            'room_id' => $r102->id,
            'monthly_rate' => $r102->roomType->monthly_rate,
            'deposit_amount' => $r102->roomType->deposit_amount,
            'notes' => 'รายการสาธิต',
            'check_in_date' => '2026-08-01',
            'status' => 'active',
        ]);

        Booking::create([
            'tracking_code' => 'DORM-2026002',
            'user_id' => $user->id,
            'room_id' => $r202->id,
            'monthly_rate' => $r202->roomType->monthly_rate,
            'deposit_amount' => $r202->roomType->deposit_amount,
            'notes' => 'รายการสาธิต',
            'check_in_date' => '2026-08-15',
            'status' => 'completed',
        ]);

        Booking::create([
            'tracking_code' => 'DORM-2026003',
            'user_id' => $user->id,
            'room_id' => $r201->id,
            'monthly_rate' => $r201->roomType->monthly_rate,
            'deposit_amount' => $r201->roomType->deposit_amount,
            'notes' => 'รายการสาธิต',
            'check_in_date' => '2026-09-20',
            'status' => 'pending',
        ]);
    }
}
