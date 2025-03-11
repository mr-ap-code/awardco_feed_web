# Awardco Feed

## Description
The Awardco Feed project is a web application that displays a dynamic feed of recognitions fetched from the Awardco API. The feed showcases various recognitions, including the name of the person recognized, their avatar, and the program under which they were recognized. The application features a rotating display of recognitions.

## Installation
### Linux/macOS
1. Clone the repository:
    ```sh
    git clone git@github.com:mr-ap-code/awardco_feed_web.git
    cd awardco-feed
    ```

2. Make the installation script executable and run it:
    ```sh
    chmod +x ./install.sh
    ./install.sh
    ```

3. Access the feed at `http://localhost:8080` if using PHP built-in web server.

### Windows
1. Clone the repository:
    ```sh
    git clone git@github.com:mr-ap-code/awardco_feed_web.git
    cd awardco-feed
    ```

2. Run the installation script:
    ```bat
    install.bat
    ```

3. Access the feed at `http://localhost:8080` if using PHP built-in web server.

## Configuration
Edit the `config.php` file to update the API key, feed URL, username list, and domain.