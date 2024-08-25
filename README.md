# Back-office Breizh Blok

Back-office destiné à répertorier les blocs d'escalade de Bretagne.

## Pré-requis

- git
- docker

## Installation

1. Clonez le dépôt Github et déplacez-vous dans le dossier du projet:
   
```bash
git clone git@github.com:jeremyriverain/breizh-blok-back-office.git
cd breizh-blok-back-office
```

2. Exécutez le script d'initialisation Docker

```bash
./init.sh
```

3. Le projet utilise Cloud Storage pour stocker les images. Créez un fichier `.env.local` et renseignez les variables d'environnement `GCLOUD_PROJECT_ID` et `GCLOUD_BUCKET_ID`.

4. Pour récupérer vos credentials Google Cloud et pouvoir développer en local, exécutez la commande suivante:

```bash
docker run -ti --rm -v ~/.config/gcloud:/root/.config/gcloud gcr.io/google.com/cloudsdktool/google-cloud-cli gcloud auth application-default login
```

Accédez à l'application en tapant l'URL: [http://localhost:4444](http://localhost:4444)

## Se connecter à l'application

3 utilisateurs sont pré-configurés:

| Rôle                    | Email                   |
| ----------------------- | ----------------------- |
| ROLE_USER               | user@fixture.com        |
| ROLE_CONTRIBUTOR        | contributor@fixture.com |
| ROLE_ADMIN              | admin@fixture.com       |
| ROLE_SUPER_ADMIN        | super-admin@fixture.com |

Lorsque vous saisissez le mail sur le portail d'authentification, un email est envoyé contenant un lien pour se connecter sans mot de passe. Pour récupérer ce lien, vous pouvez ouvrir [http://localhost:1080](http://localhost:1080).

## Accéder au conteneur de l'application Symfony

```bash
docker compose exec php bash
```

## Accéder à la base de données

```bash
docker compose exec db bash
mysql -u root -p
```

Le mot de passe est `root`.

## Lancer l'analyse statique de code

[PHP Stan](https://github.com/phpstan/phpstan) est utilisé pour l'analyse statique de code. Pour la lancer, accéder au conteneur `php` et exécuter le script `composer` approprié:

```bash
docker compose exec php bash
composer run phpstan
```

## Lancer les tests PHP Unit

```bash
docker compose exec php bash
composer run phpunit
```

## Lancer les tests Cypress

Créez un fichier `.env.test.local` et renseignez les variables d'environnement `GCLOUD_PROJECT_ID` et `GCLOUD_BUCKET_ID`.

En mode headless:

```bash
docker compose exec cypress bash -c "npm run cy:run"
```

En mode intéractif (en dehors de Docker pour le moment):

```bash
cd e2e
npm run cy:open
```

En mode intéractif, vous devrez peut-être réinstaller les dépendances en local.
