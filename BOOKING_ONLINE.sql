CREATE TABLE `users` (
  `id` integer PRIMARY KEY,
  `username` varchar(255),
  `password` varchar(255),
  `role` integer,
  `name` varchar(255),
  `image` varchar(255),
  `address` varchar(255),
  `created_at` timestamp
);

CREATE TABLE `Schedules` (
  `id` integer PRIMARY KEY,
  `user_id` integer,
  `name` varchar(255),
  `start_time` timestamp,
  `end_time` timestamp,
  `created_at` timestamp
);

CREATE TABLE `Store_Information` (
  `id` integer PRIMARY KEY,
  `name` varchar(255),
  `address` varchar(255),
  `phone` varchar(255),
  `image` varchar(255),
  `created_at` timestamp
);

CREATE TABLE `categorys` (
  `id` integer PRIMARY KEY,
  `name` varchar(255),
  `created_at` timestamp
);

CREATE TABLE `services` (
  `id` integer PRIMARY KEY,
  `category_id` integer,
  `name` integer,
  `describe` varchar(255),
  `price` integer,
  `created_at` timestamp
);

CREATE TABLE `Booking` (
  `id` integer PRIMARY KEY,
  `schedule_id` integer,
  `user_id` integer,
  `status` varchar(255),
  `booking_time` timestamp,
  `created_at` timestamp
);

CREATE TABLE `Service_Booking` (
  `id` integer PRIMARY KEY,
  `service_id` integer,
  `booking_id` integer,
  `created_at` timestamp
);

CREATE TABLE `Base` (
  `id` integer PRIMARY KEY,
  `booking_id` integer,
  `store_information_id` integer,
  `name` varchar(255),
  `address` varchar(255),
  `phone` varchar(255),
  `image` varchar(255),
  `created_at` timestamp
);

CREATE TABLE `Feedbacks` (
  `id` integer PRIMARY KEY,
  `booking_id` integer,
  `feedback_text` varchar(255),
  `feedback_rating` varchar(255),
  `created_at` timestamp
);

CREATE TABLE `Setting` (
  `id` integer PRIMARY KEY,
  `logo` varchar(255),
  `font` varchar(255),
  `created_at` timestamp
);

ALTER TABLE `Feedbacks` ADD FOREIGN KEY (`booking_id`) REFERENCES `Booking` (`id`);

ALTER TABLE `Schedules` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `services` ADD FOREIGN KEY (`category_id`) REFERENCES `categorys` (`id`);

ALTER TABLE `Booking` ADD FOREIGN KEY (`schedule_id`) REFERENCES `Schedules` (`id`);

ALTER TABLE `Booking` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `Base` ADD FOREIGN KEY (`booking_id`) REFERENCES `Booking` (`id`);

ALTER TABLE `Base` ADD FOREIGN KEY (`store_information_id`) REFERENCES `Store_Information` (`id`);

ALTER TABLE `Service_Booking` ADD FOREIGN KEY (`booking_id`) REFERENCES `Booking` (`id`);

ALTER TABLE `Service_Booking` ADD FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);
