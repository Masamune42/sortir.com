# sortir.com
Projet de groupe PHP sous Symfony avec Boostrap.

## Participants au projet
- <a href="https://github.com/AClaich" target="_blank"> CLAICH Alexis </a>
- <a href="https://github.com/Masamune42" target="_blank"> CAIGNARD Alexandre </a>
- <a href="https://github.com/SlothVia" target="_blank"> MEDONNE Vianney </a>
- <a href="https://github.com/ronanturpin" target="_blank"> TURPIN Ronan </a>

## Technologie
- Symfony v4.4
- Dépendances :
  - apache-pack
  - doctrine
  - debug
  - symfony/twig-bundle
  - var-dumper
  - make
  - form : formulaires
  - requirements-checker
  - asset
  - validator
  - security
  - security-csrf
  - orm-fixtures : utilisation des fixtures
  - symfony/http-foundation : ajout de gestion des requêtes
  - sensio/framework-extra-bundle : parameter converter
  - translation : traduction de messages uniquement en anglais
  - league/csv : import de CSV
  - twig/intl-extra : ajout d'un format de date
  - symfony/swiftmailer-bundle : envoi de mails
  
## Description du projet
Nous avons réalisé un site d'organisation de sorties à destination du campus ENI.

Il faut se connecter avec ses identifiants afin d'avoir accès au contenu du site.

Sur la page des sorties, pourra les filtrer et en rechercher suivant plusieurs paramètres comme : la date (de début ou de fin), si je suis organisateur, etc.
On peut afficher les détails d'une sortie et s'y inscrire si l'on souhaite.

On a aussi la possibilité de pouvoir créer une sortie en choisissant tous les champs indiqués. Si un lieu n'existe pas, on peut le créer nous-même. On peut enregistrer la sortie créée en brouillon ou la publier de suite.

Si on est organisateur de la sortie et qu'elle n'a pas encore commencé, on peut l'annuler en justifiant ce choix avec un message.

L'administrateur possède des droits et des accès privilégiés comme :
- Rajouter un utilisateur manuellement,
- Rajouter une liste d'utilisateurs dans un fichier au format CSV,
- Désactiver ou supprimer des utilisateurs.
