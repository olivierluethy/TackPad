<!-- PROJECT LOGO -->
<br />
<p align="center">
  <a href="github.com/Olivier_Luethy/TackPad.git">
    <img src="assets/favicon.ico" alt="Logo" width="200" height="200">
  </a>

  <h3 align="center">TackPad</h3>
  <h4 align="center">A notes application built with PHP and MVC</h4>

  <p align="center">
    Here I'll explain how I developed the note-taking application called TackPad.
    <br />
    <a href="github.com/olivierluethy/TackPad/blob/master/README.md"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/olivierluethy/TackPad/">View Demo</a>
    ·
    <a href="https://github.com/olivierluethy/TackPad/issues">Report Bug</a>
    ·
    <a href="https://github.com/olivierluethy/TackPad/issues">Request Feature</a>
  </p>
</p>

<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
    </li>
    <li>
      <a href="#installation-guide">Installation Guide</a>
    </li>
    <li>
      <a href="#installation-guide">License</a>
    </li>
    <li>
      <a href="#contributing">Contributing</a>
    </li>
  </ol>
</details>

## About The Project

I have always had the ambition to create a note-taking application, but for a long time I struggled to figure out how to approach building one. Taking a coding course transformed my skills and inspired me to finally start this project.

Once I had mapped out the use cases and features I needed for the application, I began the development phase. Surprisingly, the process was smoother and less challenging than I expected.

After completing the first version and publishing it to GitHub, I started thinking about how to improve the application. This led to the decision to implement a login system to ensure that users can securely access their own notes without seeing other people's data.

I am excited about the future of this project and look forward to seeing how it evolves. One thing is for sure - I have accomplished everything I set out to do when I first started the project!

Future Mission: This application should have end-to-end encryption so that no bulk text gets into the database, only the hash.

## Installation Guide

1. **Install Git:**
   - Download and install Git on your local machine from [here](https://git-scm.com/downloads).

2. **Clone the Project:**
   - Choose a suitable location on your computer.
   - Right-click on the folder and select "Git Bash Here".
   - In the command prompt window, type:
     ```sh
     git clone https://github.com/olivierluethy/TackPad.git
     ```

3. **Setup Local Database:**
   - You will need a local database to run the application. I recommend using [XAMPP](https://www.apachefriends.org/index.html).
   - Ensure you download and install the latest version for compatibility.
  
4. **Download Composer:**

To manage your project dependencies, including the Dotenv package, you need to have Composer installed on your system. Composer is a dependency manager for PHP that allows you to declare the libraries your project depends on and manages (installs/updates) them for you.

### Installing Composer

1. **Download Composer:**

   You can download Composer by visiting the official Composer [download page](https://getcomposer.org/download/).

2. **Install Composer:**

   Follow the installation instructions provided for your operating system on the Composer download page. The installation process may vary slightly depending on whether you are using Windows, macOS, or Linux.

### Installing Dotenv Package

Once Composer is installed, you can use it to install the Dotenv package, which allows you to load environment variables from a `.env` file into your PHP application.

1. **Navigate to your project directory:**

   Open your terminal or command prompt and navigate to the root directory of your PHP project.

   ```sh
   cd /path/to/your/project
   ```

2. **Require the Dotenv package:**

   Run the following command to require the Dotenv package using Composer:

   ```sh
   composer require vlucas/phpdotenv
   ```

   This command will download the Dotenv package and add it to your project's `composer.json` file.

### Using Dotenv in Your Project

After installing the Dotenv package, you need to initialize it in your project to start using environment variables from the `.env` file.

1. **Create a `.env` file:**

   In the root directory of your project, create a `.env` file and add your environment variables to it. For example:

   ```env
   APP_ENV=local
   APP_DEBUG=true
   DATABASE_URL=mysql://user:password@localhost/database
   ```

2. **Load the .env file in your PHP script:**

   In your PHP script (e.g., `index.php` or `config.php`), load the `.env` file using the Dotenv package:

   ```php
   <?php
   require __DIR__ . '/vendor/autoload.php';

   $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
   $dotenv->load();

   // Now you can access the environment variables
   $appEnv = getenv('APP_ENV');
   $appDebug = getenv('APP_DEBUG');
   $databaseUrl = getenv('DATABASE_URL');
   ```

By following these steps, you will have Composer installed and be able to use the Dotenv package to manage your environment variables efficiently.

6. **Run the Application:**
   - Follow the setup instructions provided with the project to configure and run it locally.

## License

This project is licensed under the [MIT License](LICENSE).

## Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please be sure to update tests as appropriate and adhere to the [Coding Conventions](CONTRIBUTING.md).

By contributing to this project, you agree to abide by the [Code of Conduct](CODE_OF_CONDUCT.md).
