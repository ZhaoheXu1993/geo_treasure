drop procedure if exists batch_insert_treasure;
DELIMITER //  
CREATE PROCEDURE batch_insert_treasure()  
BEGIN
    DECLARE i int DEFAULT 0;
    DECLARE id varchar(50);
    label1: WHILE i <= 199 DO
					SET id = (select LEFT(MD5(NOW()), 6));
    	   		if (select count(*) from treasure where treasure_uuid=id) > 0 then 
						ITERATE label1;
					end if;
	      		INSERT INTO `hle_dev`.`treasure` (`treasure_uuid`, `latitude`, `longtitude`, `item`, `description`) VALUES (id, (select 29.637039+rand()*(0.651883-0.637039)), (select -82.372112+rand()*(-82.339561+82.372112)), (select floor(0+rand()*10)), 'sticker');
	      		SET i = i + 1;
    END WHILE label1;
END;
//

CALL batch_insert_treasure();