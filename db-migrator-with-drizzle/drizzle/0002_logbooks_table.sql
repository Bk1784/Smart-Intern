CREATE TABLE `logbooks` (
	`id` bigint unsigned AUTO_INCREMENT NOT NULL,
	`user_id` bigint unsigned NOT NULL,
	`tanggal` date NOT NULL,
	`deskripsi` text NOT NULL,
	`created_by` bigint unsigned,
	`updated_by` bigint unsigned,
	`deleted_by` bigint unsigned,
	`created_at` timestamp,
	`updated_at` timestamp,
	`deleted_at` timestamp,
	CONSTRAINT `logbooks_id` PRIMARY KEY(`id`)
);
--> statement-breakpoint
ALTER TABLE `logbooks` ADD CONSTRAINT `logbooks_user_id_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE no action ON UPDATE no action;--> statement-breakpoint
CREATE INDEX `logbooks_user_id_index` ON `logbooks` (`user_id`);--> statement-breakpoint
CREATE INDEX `logbooks_tanggal_index` ON `logbooks` (`tanggal`);