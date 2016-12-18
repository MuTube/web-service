INSERT INTO `permission` (`id`, `name`, `description`) VALUES
  (1, 'user_management', 'Allow to manage the users'),
  (2, 'settings_management', 'Allow to manage the settings'),
  (3, 'settings_permissions_edit', 'Allow to edit the permission settings'),
  (4, 'user_list', 'Allow to list the users'),
  (5, 'user_read', 'Allow to read the users'),
  (6, 'user_edit', 'Allow to edit the users'),
  (7, 'settings_list', 'Allow to list the settings'),
  (8, 'user_remove', 'Allow to remove the users'),
  (9, 'user_login', 'Allow to login into the UI'),
  (10, 'track_management', 'Allow to manage the tracks')
;


INSERT INTO `role_2_permission` (`id`, `role_id`, `permission_id`) VALUES
  (1, 1, 1),
  (2, 1, 2),
  (3, 2, 4),
  (4, 2, 5),
  (5, 1, 9),
  (6, 2, 9),
  (7, 3, 9),
  (8, 1, 10)
;


INSERT INTO `user` (`id`, `firstname`, `lastname`, `email`, `username`, `password`, `api_key`, `role_id`, `image_filepath`) VALUES
  (1, 'Admin', '', '', 'admin', '$1$jORggZSK$VnSUkk6AHBCefFP8GfdVq0', '', 1, NULL),
  (2, 'Guest', '', '', 'guest', '$1$LZBZdNxO$lSv/kgNAUkw7Ps2B5dX1T.', '', 3, NULL)
;


INSERT INTO `role` (`id`, `name`) VALUES
  (1, 'admin'),
  (2, 'user'),
  (3, 'guest')
;
