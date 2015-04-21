use talk2native;
create table visitors(	uid INTEGER NOT NULL AUTO_INCREMENT, 
						dtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
						name VARCHAR(30), 
						lang1 SMALLINT,
						lang2 SMALLINT,
						active TINYINT DEFAULT 1,
						status SMALLINT DEFAULT 1, -- 1=Available, 2=Talking
						PRIMARY KEY(uid));
