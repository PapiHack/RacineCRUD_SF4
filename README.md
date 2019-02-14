# RacineCRUD_SF4
Modifiez la variable `DATABASE_URL` dans le fichier .env en fonction de votre base de donnée 
(`hôte, mot de passe, @ip, port`).  
## Création de la base de donnée  
`php bin/console doctrine:database:create`  
## Appliquez les migrations  
`php bin/console doctrine:migrations:diff`   
`php bin/console doctrine:migrations:migrate`   

