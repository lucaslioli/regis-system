# Regis - Retrieval Evaluation for Geoscientific Information Systems

Regis is a system to provide relevance judgments about multimodal documents for determined queries. This process will help to construct a new multimodal test collection in the Geoscience domain. Regis is being developed as part of a collaboration between the Institute of Informatics at UFRGS and Petrobras.

## Functionalities

* Interface to provide relevance judgments for documents retrieved in response to queries, describing the information need;
* Adapted to multimodal documents, PDF, text, and images;
* The system shows the source PDF document and the text extracted from it, with the query words highlighted;
* Navigation buttons to go through all highlighted words that match with the query;
* The user can skip a query if it demand extra domain knowledge;
* Each query-document pair are judged by two annotators, in case of disagreement, it's set a tie to that pair;
* Tiebreak management, where other annotators can solve it and the majority class is selected.
* Admin controller to follow the annotation progress, manage users role, and CRUD operations over the documents and queries.
* Download the ```qrels``` file with the result of all relevance judgments.
* Basic search interface that performs queries over all collection using Solr system.

## Requirements and tools

Regis is being developed using the MVC open-source PHP web framework, Laravel 8.0. The requirements are the same as the framework, found in the [documentation (version 8.x)](https://laravel.com/docs/8.x). Laravel has support to different databases, the chosen one was [MySQL](https://www.mysql.com/).

To run the project you will need to have installed [Composer](https://getcomposer.org/).

### Database

The database ER diagram, generated considering default Laravel tables, can be found in [documentation/db-model.png](documentation/db-model.png). The creation of all tables is made with Laravel migrations during the project initialization, except the own database that has to be created manually.

## How to run the project

After cloning the project, inside the project folder, create the file `.env`, by copying and renaming the `.env.exemple` file. This file has basic settings  for Laravel. If necessary, you will  need to configure the database connection, default admin user, and mail settings (to recover the user's password).

Before running the commands, you have to create the MySQL database named `regis`, the same `DB_DATABASE` informed in `.env` file.

Finally, you can run the following commands:

```
$ composer install
$ php artisan migrate
$ php artisan key:generate
$ php artisan serve
```
The last command will generate a locally URL to access the system into the browser.

If you have some doubts or problems to run, maybe this [link](https://gist.github.com/hootlex/da59b91c628a6688ceb1) can help.

## User information

The usability was thought to make the annotation as easier as possible. The user could judge a document with only two clicks. Extra functionalities were added to facilitate the judgment, such as the navigation buttons to go through all highlighted words that match with the query, a link to the original document, and a progress bar over the query annotations. In case the query requires extra domain knowledge, the user can skip it.

The admin user (except for the main one, that can't judge) has the same functionalities as the default user but also has control, with CRUD operations, over the documents and queries, it also can list, search and manage admin privileges of the users. There are some screenshots with test data available in ```documentation/``` folder, where the menu links 'Documents', 'Queries', 'Users' and 'Project' are restrict admin areas.

Under the menu Project, it's available the ```qrels``` file download link and the Basic Search over all the collection.

## Extra information

### About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects.

Laravel is accessible, powerful, and provides tools required for large, robust applications.

### Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

### License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## Team:

Developer: Lucas L Oliveira (lloliveira@inf.ufrgs.br) <br>
Coordination: Viviane P Moreira (viviane@inf.ufrgs.br)