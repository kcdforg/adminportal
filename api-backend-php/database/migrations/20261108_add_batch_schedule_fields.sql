ALTER TABLE `student_batches`
    ADD COLUMN `schedule_days` VARCHAR(100) NULL AFTER `end_date`,
    ADD COLUMN `start_time` TIME NULL AFTER `schedule_days`,
    ADD COLUMN `end_time` TIME NULL AFTER `start_time`;
