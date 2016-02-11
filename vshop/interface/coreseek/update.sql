REPLACE INTO 137home.sph_counter SELECT 1, MAX(id),'137_art' FROM 137home.137_article;
#REPLACE INTO 137home.sph_counter SELECT 2, MAX(id),'137_member' FROM 137home.137_member;
#REPLACE INTO 137home.sph_counter SELECT 3, MAX(id),'137_designer_project' FROM 137home.137_designer_project;