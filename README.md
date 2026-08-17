# laravue
Bootstrap website and Admin LTE integration using Laravel 13 + Inertia + Vue 3 with help of Claude

# Follow below steps for setup project in your local system
* Note: PHP virsion for this project should be PHP 8.3 or higher

Step 1 > clone repo from - https://github.com/samirkoladiya/laravue.git

Step 2 > edit .env and change database username and password

Step 3 > create blank database with name laravue

Step 4 > create virtual host with name - laravue.localhost

Step 5 > assign public folder path to virtual host
* Note: I put it to D: drive so my path is D:\htdocs\laravue\public

Step 6 > Run below commands in cmd
- composer install
- php artisan migrate
- npm install
- npm run dev

Step 7 > Open website on browser
- Front URL: http://laravue.localhost/
- Admin URL: http://laravue.localhost/admin/login

- There will be no admin users in database. So create new one.
- For development mode OTP is static 1234 to check forgot password.
