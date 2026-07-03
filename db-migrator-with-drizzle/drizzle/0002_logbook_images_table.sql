CREATE TABLE `logbook_images` (
	`id` bigint unsigned AUTO_INCREMENT NOT NULL,
	`logbook_id` bigint unsigned NOT NULL,
	`file_path` varchar(255) NOT NULL,
	`created_at` timestamp,
	CONSTRAINT `logbook_images_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
ALTER TABLE `logbook_images` ADD CONSTRAINT `logbook_images_logbook_id_logbooks_id_fk` FOREIGN KEY (`logbook_id`) REFERENCES `logbooks`(`id`) ON DELETE no action ON UPDATE no action;--> statement-breakpoint
CREATE INDEX `logbook_images_logbook_id_index` ON `logbook_images` (`logbook_id`);