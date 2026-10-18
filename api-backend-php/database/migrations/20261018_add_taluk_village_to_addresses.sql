-- Add optional address subdivisions used by family and trainer address forms.
ALTER TABLE `addresses`
    ADD COLUMN `taluk` VARCHAR(100) NULL AFTER `address_line_2`,
    ADD COLUMN `village` VARCHAR(100) NULL AFTER `taluk`;
