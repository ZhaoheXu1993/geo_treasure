drop procedure if exists batch_random_user_take_treasure;
DELIMITER //  
CREATE PROCEDURE batch_random_user_take_treasure()  
BEGIN
    DECLARE u_id varchar(50);
    DECLARE t_id varchar(50);
    DECLARE cnt int DEFAULT 0;
    label: LOOP
 		SET cnt = (select count(*) from treasure where is_taken=0);
		SET u_id = (select user_uuid from user order by RAND() limit 1);
		SET t_id = (select treasure_uuid from treasure where is_taken=0 order by RAND() limit 1);
		
		insert into user_treasure (user_uuid, treasure_uuid) values (u_id, t_id);
		
		# update treasure information
		update treasure set is_taken=1 where treasure_uuid=t_id;
		
		# update user rank information
		if (select count(*) from user_rank where user_uuid=u_id and rank_name='take') > 0 then
			update user_rank set value=value+1 where user_uuid=u_id and rank_name='take';
			update user_rank set value=value+1 where user_uuid=u_id and rank_name='scan';
		else
			insert into user_rank (user_uuid, rank_name, value) values (u_id, 'take', 1);
			if (select count(*) from user_rank where user_uuid=u_id and rank_name='scan') > 0 then
				update user_rank set value=value+1 where user_uuid=u_id and rank_name='scan';
			else
				insert into user_rank (user_uuid, rank_name, value) values (u_id, 'scan', 1);
			end if;
		end if;
			
   	# 检查是否还有x个未被拿走的宝藏
   	if cnt > 5 then
   		iterate label;
		else 
			leave label;
		end if;
	end LOOP;
END;
//

CALL batch_random_user_take_treasure();
