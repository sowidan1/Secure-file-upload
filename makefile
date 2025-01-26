after-pull:
    php artisan migrate && npm install && npm run dev
