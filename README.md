# php-tic-tac-toe

# How to start the application

1. Firstly you need to have [Docker](https://docs.docker.com/get-docker/) installed on your system.
2. Clone this repository to your system with ```git clone```
3. Start the application by writing in your terminal ```docker-compose up -d```
3. Install Composer dependencies by running ```docker-compose exec web composer install```
4. Install npm dependencies by running ```docker-compose exec web npm install```
5. Build the frontend by running ```docker-compose exec web npm run build```
6. Start the game server by running ```docker-compose exec web -d symfony console SocketServer```
7. Start playing the game (http://localhost:8080/)