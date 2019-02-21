# Setting up local development using Docker

To make it easier and save time it's advisable you use Docker for quickly setting up a local environment. You're required to have up to date Docker and docker-compose installed locally.

The docker-compose.yml file will build the PHP image and create a Mysql database container, linking them together.

### Docker structure:

docker-compose.yml creates two containers and links them under the same network. 
  - The first container contains PHP with Apache. The files from the /www directory are copied into apache's default directory. The port 80 on the container is mapped to port 8000 on your computer (host)
  - The second container is a MySql 5.7 host from Docker Hub. The database files created are mapped to /sqldata, so that your database changes are persisted. Port 3306 (default mysql port) is mapped to 3306 on your host

### Steps:

1. Make sure docker is installed by running `docker --version`. I'm running version 17.09, any version newer than 16 should be OK.
2. Make sure docker-compose is installed by running `docker-compose --version`. I'm running version 1.16
3. From the root directory run `docker-compose build`. Containers will be downloaded and built
4. Run `docker-compose up` or `docker-compose up -d` if you want to run in detached mode (runs separately without console output)
5. Using a MySql application (I recommend MySql Workbench) create a new database (schema) called 'sc2ladders' and import the sc2ladders.sql SQL dump into it. Default MySql connection settings are:
    - Port: 3306 (To change the port mapping change the first port for the mysql container in docker-compose.yml)
    - Username: root
    - Password: root (change in the docker-compose.yml if you want extra security)
6. Copy the ./www/dbconf-example.php file to ./www/dbconf.php and update any required connection details.
6. Navigate to [http://localhost:8000](http://localhost:8000) and you should see a working site. Changes are synced in real-time as you update files.