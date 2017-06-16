drop procedure if exists doWhile;
DELIMITER //  
CREATE PROCEDURE doWhile()  
BEGIN
    DECLARE i int DEFAULT 0;
    WHILE i <= 9 DO
        INSERT INTO `hle_dev`.`user` (`user_uuid`, `name`, `email`, `link`, `fb_id`, `img_url`, `created_at`, `updated_at`) VALUES (CONCAT('594082909b58',i), CONCAT('Person 1',i), CONCAT('1', i,'@a.com'), 'https://www.facebook.com/app_scoped_user_id/451113005239190/', CONCAT('45111300523920',i), 'https://scontent.xx.fbcdn.net/v/t1.0-1/c0.8.50.50/p50x50/12508710_200540233629803_3727138865285141474_n.jpg?oh=36a6a54e10e80dc4b4be5eadf81d641a&oe=59D2856A', '2017-06-14 00:25:52', '2017-06-14 00:25:52');
        SET i = i + 1;
    END WHILE;
END;
//

CALL doWhile();