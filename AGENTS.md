# AGENTS.md - Agentic Coding Guidelines

This repository is a CodeIgniter 3 PHP application for a workshop management system (Bengkel).

## Build, Lint & Test Commands

### Running Tests
```bash
# PHP-based projects don't have standard test commands configured
# To test individual controller functionality, run locally:
php -S localhost:8000

# For database operations, use:
php -c application/config/database.php
```

### Code Quality Checks
```bash
# PHP syntax validation (no linter configured)
php -l <file.php>

# To validate all PHP files:
find application -name "*.php" -exec php -l {} \;
```

### Development Server
```bash
# Start local PHP development server
php -S localhost:8000

# Then navigate to http://localhost:8000/index.php
```

## Code Style Guidelines

### 1. PHP Formatting & Indentation
- Use **Allman indent style** (opening braces on same line, closing on new line)
- Use **tabs** for indentation (not spaces)
- Follow CodeIgniter PHP Style Guide: https://codeigniter.com/user_guide/general/styleguide.html
- All code must be readable and consistent with existing codebase

Example:
```php
class Example extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Example_model');
    }

    public function index()
    {
        if ($condition) {
            // do something
        }
        else {
            // do something else
        }
    }
}
```

### 2. Naming Conventions
- **Classes**: PascalCase (e.g., `Admin`, `User_model`)
- **Functions/Methods**: lowercase with underscores (e.g., `get_limit_data()`, `selectById()`)
- **Variables**: lowercase with underscores (e.g., `$user_id`, `$config`)
- **Constants**: UPPER_CASE (e.g., `BASEPATH`)
- **Database**: snake_case for tables and columns (e.g., `kode_admin`, `nama_admin`)

### 3. Type Declarations & Documentation
- Use **PHPDoc blocks** for all classes, methods, and properties
- Include `@param`, `@return`, `@throws` tags
- Example:
```php
/**
 * Retrieves admin data by ID
 * 
 * @param int $id Admin ID
 * @return object|bool Admin object or FALSE if not found
 */
public function selectById($id)
{
    // ...
}
```

### 4. Imports & Includes
- Use CodeIgniter's loader system (`$this->load->model()`, `$this->load->view()`)
- Load models in constructor when possible
- Load libraries as needed in methods: `$this->load->library('form_validation')`
- Example:
```php
public function __construct()
{
    parent::__construct();
    $this->load->model('Admin_model');
    $this->load->library('form_validation');
}
```

### 5. Error Handling
- Use CodeIgniter's error handling mechanisms
- Check for direct script access at controller start:
```php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
```
- Validate form input using `form_validation` library
- Use `show_error()` and `show_404()` for error responses
- Always check if model results exist before using them:
```php
$row = $this->Admin_model->selectById($id);
if ($row) {
    // process data
}
```

### 6. Session Management
- Initialize session: `session_start();`
- Check session variables: `isset($_SESSION['level'])`
- Implement access control in constructor:
```php
if (!isset($_SESSION['level'])) {
    redirect('login');
}
```

### 7. Database Interactions
- Use CodeIgniter's Active Record pattern (now Query Builder in CI4)
- Use prepared statements via model methods
- Naming: Model files should be `Model_name_model.php`
- Load models in constructor: `$this->load->model('Admin_model')`

### 8. Views & Data Passing
- Pass data as associative arrays to views:
```php
$data = array(
    'key' => $value,
    'list' => $items
);
$this->load->view('admin/view_name', $data);
```
- Always load navigation and footer:
```php
$this->load->view('nav');
// ... page content ...
$this->load->view('foot');
```

### 9. Controller Structure
- All user-facing controllers must extend `CI_Controller`
- Create constructor with parent call and required dependencies
- Implement access control checks in constructor
- Keep methods focused and under 100 lines when possible

### 10. Common Patterns
```php
// Form validation
$this->form_validation->set_rules('field', 'label', 'rules');
if ($this->form_validation->run() == FALSE) {
    // show form with errors
}

// Pagination
$config['base_url'] = base_url() . 'admin';
$config['per_page'] = 10;
$this->pagination->initialize($config);
```

## Project Structure

- `application/controllers/` - Request handlers (Admin, Home, Login, etc.)
- `application/models/` - Database models (Admin_model, etc.)
- `application/views/` - HTML templates
- `application/config/` - Configuration files (database, routes, etc.)
- `application/libraries/` - Custom libraries
- `application/helpers/` - Helper functions
- `system/` - CodeIgniter core framework
- `assets/` - CSS, JS, images

## PHP Compatibility

- **Minimum**: PHP 5.2.4
- **Recommended**: PHP 5.4+
- Avoid PHP 5.3+ features without fallbacks for older versions
- Check CodeIgniter compatibility for all libraries used

## Git Workflow

Follow CodeIgniter's Git-Flow branching model:
- `develop` branch for features
- `master` branch for stable releases
- Create feature branches from `develop`
- One change per pull request (can have multiple commits)
- Sign commits: `git commit -s` or `git commit --signoff`

## Additional Resources

- CodeIgniter User Guide: https://codeigniter.com/user_guide/
- Style Guide: https://codeigniter.com/user_guide/general/styleguide.html
- Contributing Guide: See `contributing.md` in repository root
