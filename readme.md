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


# Using the API

There are 4 request groups namely Auth, Posts, Comments, Stats. The request can be found in the PostMan export.

### Auth
Handles all the authentication logic:
- Register: Registers a new **_Normal_** type user
- Login: Logs an exiting user into the system
- Logout: Logs the currently logged-in user out

### Posts
Handles all the post logic:
- All: Retrieves all posts.
    1. User does not have to be authenticated for this endpoint.
    2. User can specify an _**order_by_occurrence**_ request parameter to sort the posts by the number of occurrences.
- View: Views a specific post.
    1. Once viewed it will set an environment variable named _**post_id**_.
- Create: Creates a new post.
    1. Once created it will set an environment variable named _**post_id**_.
    2. A **_content_** body parameter also needs to be specified. It needs to be between 150 and 1500 characters.
- Like: Likes a post.
    1. A **_post_id_** needs to be specified in the url. By default, it uses the _**post_id**_ environment variable.
- Flag: Flags a post.
    1. User needs to be an **_Admin_** type user to flag posts.
    2. A **_post_id_** needs to be specified in the url. By default, it uses the _**post_id**_ environment variable.

### Comments
Handles all the comment logic:
- All: Retrieves all comments.
- View: Views a specific comment.
    1. A **_comment_id_** needs to be specified in the url. By default, it uses the _**comment_id**_ environment variable.
    2. Once viewed it will set the _**comment_id**_ environment variable.
- Create: Creates a new comment.
    1. A **_post_id_** needs to be specified in the request body. By default, it uses the _**post_id**_ environment variable
    2. A **_content_** body parameter also needs to be specified. It needs to be between 20 and 150 characters.
    3. Once created it will set an environment variable named _**comment_id**_.

### Stats
Handles all the stats logic:
- All: Retrieves the post and comments stats grouped by hour.
