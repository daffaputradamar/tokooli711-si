-- Working hours for karyawan (Toko Oli 711).
--
-- Commit 6c540c4 added start_working_hour / end_working_hour to the karyawan
-- controller, model, views and login check, but the columns were never added
-- to the database, so /karyawan and karyawan login fail with
-- "Unknown column 'k.start_working_hour'".
--
-- Existing rows get 00:00:00 - 23:59:59 so every current karyawan can still
-- log in at any time; set real hours per karyawan from the Karyawan form.
--
-- Safe to run more than once (MariaDB: ADD COLUMN IF NOT EXISTS).
--
-- Run:  mysql -u <user> -p <database> < 2026_10_karyawan_working_hours.sql

ALTER TABLE karyawan
	ADD COLUMN IF NOT EXISTS start_working_hour TIME NOT NULL DEFAULT '00:00:00',
	ADD COLUMN IF NOT EXISTS end_working_hour TIME NOT NULL DEFAULT '23:59:59';
