SELECT TABLE_NAME, COUNT(*) AS field_count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('users','room_types','rooms','bookings','buildings') GROUP BY TABLE_NAME;
SELECT COUNT(*) AS total_business_fields FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('users','room_types','rooms','bookings','buildings');
