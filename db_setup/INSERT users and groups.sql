USE `availability_calendar`;

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
     (1,'admin','Administrator'),
     (2,'members','General User'),
     (3,'super_admin','Super administrator');

# Default password, 'password', changes to fbaa5e216d163a02ae630ab1a43372635dd374c0 with default salt.

 INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `phone`, `previous_project`) VALUES
     ('1','127.0.0.1','administrator', '$2a$07$SeBknntpZror9uyftVopmu61qg0ms8Qv1yV6FG.kQOSM.9QhmTo36','CIDOADJxX2XWwECirHDce.','admin@admin.com','',NULL,'1268889823','1268889823','1', 'Leon','Sennomo','0123456789',NULL),
     ('2','127.0.0.1','adam',          '$2a$07$SeBknntpZror9uyftVopmu61qg0ms8Qv1yV6FG.kQOSM.9QhmTo36','','a@a.com',        '',NULL,'1268889824','1268889824','1', 'Adam','Admin',NULL,NULL),
     ('3','127.0.0.1','mike',          '$2a$07$SeBknntpZror9uyftVopmu61qg0ms8Qv1yV6FG.kQOSM.9QhmTo36','','m@m.com',        '',NULL,'1268889825','1268889825','1', 'Mike','Moomin',NULL,NULL);


 INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
      (1,1,3),
      (2,2,1),
      (3,3,2); # initial user as super_admin
