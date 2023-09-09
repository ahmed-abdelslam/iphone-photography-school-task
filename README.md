## Side Note

Instead of hard coding the achievements and badges, I've created tables for both of them.
This is better because, in a real project, we would have to manage achievements and badges via the admin dashboard

### Instructions
- **composer install**
- **cp .env.example .env**
- **update DB credits**
- **php artisan key:generate**
- **php artisan migrate**
- **php artisan test**
