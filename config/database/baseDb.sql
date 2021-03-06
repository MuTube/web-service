SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/* TABLE CREATION */

CREATE TABLE `permission` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `role_2_permission` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `firstname` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_key` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `image_filepath` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `track` (
  `id` int(11) NOT NULL,
  `track_history_id` int(11) NOT NULL,
  `mp3_filepath` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `track_history` (
  `id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail_filepath` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `duration` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `youtube_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `youtube_channel` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `youtube_views` int(11) NOT NULL,
  `artist` varchar(100) COLLATE utf8_unicode_ci,
  `album` varchar(100) COLLATE utf8_unicode_ci,
  `year` varchar(100) COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


/* TABLES PRIMARY KEY DEFINITION */

ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `role_2_permission`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `track`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `track_history`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `role_2_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `track`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `track_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;


/* FOREIGN KEYS */
/*
ALTER TABLE user
  ADD FOREIGN KEY (role_id) REFERENCES role(id);

ALTER TABLE role_2_permission
  ADD FOREIGN KEY (role_id) REFERENCES role(id);

ALTER TABLE role_2_permission
  ADD FOREIGN KEY (permission_id) REFERENCES permission(id);

ALTER TABLE track
  ADD FOREIGN KEY (track_history_id) REFERENCES track_history(id);*/
