I'm highly recommend using yarn (https://classic.yarnpkg.com/en/docs/install)
Please make sure nodejs latest version

How to run this project?

- composer install
- copy .env.example to .env
- php artisan key:generate
- Please pay attention to each attribute's value some example: APP_*, DB_*
- Run php artisan migrate
- Run php artisan db:seed
- Run yarn install
- Run yarn dev / yarn build (production) / yarn watch (for testing)