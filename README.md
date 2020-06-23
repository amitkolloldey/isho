
## Isho Laravel Test

**How To Setup The Project**

First clone the repo by following command:

`git clone https://github.com/amitkolloldey/isho.git isho` 

Then 

`cd isho`

Run The Following commands one By One

`composer require laravel/ui`

`php artisan ui bootstrap --auth`

`npm install`

`npm run dev`

`composer require laravel/passport`

Copy the env.example to .env

`php artisan key:generate`

Add database credentials then run

`php artisan migrate`

`php artisan db:seed`

`php artisan passport:install`

Add Passport Client Id and Secret To .env file

`PASSPORT_LOGIN_ENDPOINT=http://isho.test/oauth/token`

`PASSPORT_CLIENT_ID`

`PASSPORT_CLIENT_SECRET`


Add Mailtrap Credentials on the .env file for Email Verification

Then,

Register A New User

Login With That Credentials

Verify Email

Then you will be redirect into the Dashboard


## Thanks


