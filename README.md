# HaikUp

22/04/2025

------------------------------------ BACK END ------------------------------------------------
Actions terminées : 
- Installation symfony 
- Installation webpack + SASS + fontawesome
- Installation bundle Mercure + Setup avec Docker pour la gestion notif (Follow / Like / Comment / Notif??)
- Installation security bundle + setup Route et création User avec FormSignUp
- Création formulaire formSignUp (setup des champs + constraints pour mot de passe et username).
- Installation Mailer
- Notification Controller = Mise en place de la logique de redirection lorsque l'on clique sur la modale turbo stream 

CREATION ENTITES : 
    - Création entité User
    - Création entité Follows (utilisation UX Turbo)
    - Création entité words
    - Création entité user_words 
    - Création entité haïkus
    - Création entité Collections
    - Création entité comments
    - Création entité likes
    - Création entité notifications
    - Création entité entity_type ?? 

------------------------------------

ENTITIES : 
- S'occuper de créer les controllers pour chaque action notifications !! Gros sujet prévoir une bonne semaine ! IMPORTANCE +++
- Mise en place du template turbo stream 
- Création de la div notification turbo dans le base.html pour créer la modale

ATTENTION ON VA PASSER SUR CDN google material icons pour récupérer le material symbols -> marche pas sinon et on veut pas fontawesome finalement

- Setup de l'adresse DSN mailer dans le .env + test de fonctionnement
- Création des formulaires pour l'envoi des mots
- Création des formulaires pour la création des haïkus
- Création du formulaire de contact
- Création du récupérateur de mot de passe






------------------------------------ FRONT END ------------------------------------------------
