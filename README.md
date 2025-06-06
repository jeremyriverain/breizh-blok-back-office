# Breizh Blok Back-office

Back-office application designed to catalog climbing blocks in Brittany.

## Prerequisites

- git
- docker

## Installation

1. Clone the Github repository and navigate to the project folder:
   
```bash
git clone git@github.com:jeremyriverain/breizh-blok-back-office.git
cd breizh-blok-back-office
```

2. Set the environment variables `GCLOUD_PROJECT_ID`, `GCLOUD_BUCKET_ID` in the `.env.local` file

3. Run the Docker initialization script

```bash
./init.sh
```

4. The project uses Cloud Storage to store images. Create a `.env.local` file and set the environment variables `GCLOUD_PROJECT_ID` and `GCLOUD_BUCKET_ID`.

5. To retrieve your Google Cloud credentials and be able to develop locally, run the following command:

```bash
docker run -ti --rm -v ~/.config/gcloud:/root/.config/gcloud gcr.io/google.com/cloudsdktool/google-cloud-cli gcloud auth application-default login
```
Access the application by typing the URL: [http://localhost:4444](http://localhost:4444)

## Logging into the application

4 users are pre-configured:

| Role                    | Email                   |
| ----------------------- | ----------------------- |
| ROLE_USER               | user@fixture.com        |
| ROLE_CONTRIBUTOR        | contributor@fixture.com |
| ROLE_ADMIN              | admin@fixture.com       |
| ROLE_SUPER_ADMIN        | super-admin@fixture.com |

When you enter the email on the authentication portal, an email is sent containing a link to log in without a password. To retrieve this link, you can open [http://localhost:1080](http://localhost:1080).

## Accessing the Symfony application container

```bash
docker compose exec php bash
```

## Accessing the database

```bash
docker compose exec db bash
mysql -u root -p
```

The password is `root`.

## Running static code analysis

[PHP Stan](https://github.com/phpstan/phpstan) is used for static code analysis. To run it, access the `php` container and execute the appropriate `composer` script:

```bash
docker compose exec php bash
composer run phpstan
```

## Running PHP Unit tests

```bash
docker compose exec php bash
composer run test-fixtures
composer run phpunit
```