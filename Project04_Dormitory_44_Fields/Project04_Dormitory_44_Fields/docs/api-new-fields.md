# ฟิลด์เพิ่มเติมใน API เดิม

ทุกฟิลด์ด้านล่างเป็น optional เพื่อรองรับ client เดิม กฎ authentication/role ของ endpoint เดิมยังใช้

| Endpoint | ฟิลด์เพิ่มเติมที่รับ |
|---|---|
| POST /api/register | student_code (unique, ≤30), faculty (≤150), major (≤150) |
| POST /api/room-types | description (≤5000), capacity (1–65535), deposit_amount (0–99999999.99) |
| POST /api/rooms และ PUT /api/rooms/{id} | building_id (ต้องมีอยู่ใน buildings) |
| POST /api/bookings | check_out_date (≥check_in_date), notes (≤2000) |

POST /api/bookings กำหนด monthly_rate, deposit_amount และ created_at ฝั่ง server ไม่รับราคาที่ส่งจาก client
GET /api/me และ login/register คืน student_code, faculty, major ให้เจ้าของบัญชี
GET /api/rooms คืนอาคารเฉพาะ id/code/name และประเภทห้องที่มีฟิลด์ใหม่
การเพิ่มอาคารใช้หน้า Blade ที่ /admin/dashboard; POST /admin/buildings ต้องมี session ผู้ดูแลและ CSRF token
ข้อมูลใหม่ทั้งหมดอธิบายใน docs/database.md
