# Getting the API up and running

The API and other necessary services has been set up as a multi-container app. To get the up running locally,
the following steps must be performed:

### Set up Docker Desktop
1. Install Docker Compose if not already installed. [Instructions for your operating system can be found here.](https://docs.docker.com/get-docker/)
2. If running Windows, it is recommended to run Docker Desktop in WSL2 mode. [Follow the guide here.](https://docs.docker.com/desktop/wsl/)

### Set up Laravel
1. Open a new terminal window and change the directory to where the application files have been downloaded
2. Run the following commands:
    ````
   docker compose build
   docker compose up -d
    ````

3. Run ``docker ps`` to see a list of all running containers. The following should now be running:
    - test-app
    - test-db
    - test-nginx
4. Next, we have to set up the Laravel dependencies and app key. Run the following in the same terminal:
    ````
   docker compose exec app bash
   
   # Install dependancies
   composer install
   
   # Generate the app key and cache app config and settings
   php artisan key:generate
   php artisan optimize:clear
   php artisan optimize
   
   # Seed the DB with mock data
   php artisan migrate:fresh --seed
    ````
5. Go to http://localhost:8001/. The Laravel Welcome Page should now be loaded.
