# AudioCatalogue

This is web application organizes your mp3 catalogue and lets you play music and audiobooks in your browser. The application is supposed to run in your home LAN without being accessible from the internet.

## Requirements

* You need a linux server that has a samba server containing your music and audiobooks.
* Laravel needs PHP `^8.2`, any webserver (`Apache` and `Nginx` should both work fine) a `MySQL` or `MariaDB` server so Laravel can run on it.
* The Laravel application needs to be able to read and write on the samba share. This can be acomplished by having the webserver user, probably `www-data` be a member of the samba user group. All Samba files should be group read/writeable.
* The samba server needs to have
    ```conf
    create mask = 0770
    directory mask = 0775
    ```
* `PHP` needs to have a sensible execution time and memory limit, `mbstring` and `exif` extensions should be enabled, as well as the required drivers for `MySQL` or `MariaDB`.
* The application uses several native shell commands. `find` should be available, `zip` might need to be installed (I couldn't get php-zip to work on Debian13/php8.4). 
* The application is tailored for my mp3 collection and in some cases assumes a certain structure, be prepared to customize this.
* Frontend needs NodeJS `^24`.

## Warning

* This is a hobby project for myself and not production-ready.
* Only make this accessible from the internet if you really know what you are doing. I haven't done any hardening, there is no auth and not many security precautions have been taken. Use at your own risk. 
* There is no `docker-compose`, if you want to dockerize this, you will need to cook it yourself. 
* Most of the texts are in german with a mix of english thrown in. Feel free to translate, there is currently no i18n planned.

## Installation

Clone repository, edit `.env` and `config/collection`, install vendor packages with composer, and `npm run build` to create the production bundle.

* `php artisan migrate:fresh`
* `php artisan db:seed`
* `php artisan app:update`

## Setup IDE for development

I am using IntelliJ, other IDEs probably work as well; I just don't know them.

### A) Prettier

Prettier needs to be run on save.

#### IntelliJ

* `Settings` → `Languages & Frameworks` → `Javascript` → `Prettier`
* Select `Automatic Prettier configuration`
* Run for files: `**/*.{js,ts,json,vue,scss}`
* `Run on save` must be checked

### B) ESLint

ESLint should be run while editing in the IDE.

#### IntelliJ

* `Settings` → `Languages & Frameworks` → `Javascript` → `Code Quality Tools` → `ESLint`
* Select `Automatic ESLint configuration`
* Run for files: `**/*.{js,ts,html,vue}`
* `Run on eslint --fix on save` must be checked.

### C) Stylelint

StyleLint should be run while editing in the IDE. This does not work well in `.vue` files currently.

#### IntelliJ

* `Settings` → `Languages & Frameworks` → `Style Sheets` → `Stylelint`
* Select `Enable`
* Run for files: `**/*.{scss, vue}`
* `Run on stylelint --fix on save` must be checked

## Artisan commands

### `php artisan app:update`

The main command that reads the samba share files and updates the database. This should run as a daily cronjob. Includes all following console commands.

### `php artisan app:clean`

Cleans samba share of unwanted files (MacOS, Samba junk etc) and cleans storage folder.

### `php artisan app:csv:audiobook` and `php artisan app:csv:music`

Creates CSV files with a list of all `.mp3` files on the samba share. Later commands need these CSV files to know which files to check.

### `php artisan app:db:audiobook` and `php artisan app:db:music`

Reads the CSV files and creates database entries.

## NPM commands

### `npm run dev`

Development mode. Ensure you have `APP_ENV=local` in `.env` and the server has a `hot` file in `public` directory.

### `npm run build`

Production mode. Ensure you have `APP_ENV=production` in `.env`.

### `npm run lint`

Run Eslint and Stylelint separately.

## License
`AudioCatalogue` is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
