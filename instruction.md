1. Create a MySQL database named "task_manager" with a table named "tasks" having the following columns:
   - id (int, auto increment, primary key)
   - title (varchar(255))
   - description (text)
   - status (enum: "Pending", "In Progress", "Completed")
   - created_at (timestamp, default: current timestamp)
   - updated_at (timestamp, default: current timestamp on update)
2. Create a PHP file named "index.php" as the entry point of the app. This file should include the following functionality:
   - Display a list of existing tasks (title and status) from the "tasks" table.
   - Provide a form to add a new task with title and description fields.
   - Allow updating the status of each task (e.g., changing from "Pending" to "In Progress" or "Completed").
   - Provide a search functionality to filter tasks by title and/or status.
3. Create a "config.php" file to store database connection details (e.g., hostname, username, password, database name) and include it in "index.php".
4. Organize your PHP code using a clean code structure, following best practices such as:
   - Using meaningful variable and function names.
   - Separating concerns by using classes or functions for different tasks.
   - Properly commenting and documenting your code.
   - Avoiding redundant code and ensuring code reusability.
   - Using appropriate error handling and validation techniques.
5. Use prepared statements or parameterized queries in your SQL queries to prevent SQL injection attacks.
6. Test the app by adding, updating, and searching for tasks. Ensure that everything is working as expected.
7. Create README.md contains how to setup server, apps description, and any information needed for other developer to test the apps