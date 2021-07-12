# api

Первое задание:
SELECT `users`.`id`, CONCAT(`users`.`first_name`, ' ',`users`.`last_name`) AS `name`,`books`.`author` AS `author`, `books`.`name` AS `book1` FROM `user_books` JOIN `users` ON `user_books`.`user_id` = `users`.`id` JOIN `books` ON `user_books`.`book_id` = `books`.`id` WHERE `age` >= 7 and `age` <=17 GROUP BY `author` HAVING COUNT(*)=2
