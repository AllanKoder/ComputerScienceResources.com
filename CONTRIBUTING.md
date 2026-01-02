# Contributing to ComputerScienceResources.com

First off, **thank you** for considering contributing to ComputerScienceResources.com!

This project is built on the values of **helpfulness**, **kindness**, and **collaboration**. Whether you're fixing a typo, reporting a bug, or implementing a major feature, your contribution matters and is appreciated.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
  - [Reporting Bugs](#reporting-bugs)
  - [Suggesting Features](#suggesting-features)
  - [Your First Code Contribution](#your-first-code-contribution)
  - [Pull Requests](#pull-requests)
- [Development Setup](#development-setup)
- [Style Guide](#style-guide)
- [Community](#community)

## Code of Conduct

This project is a safe and welcoming space for everyone. We expect all contributors to:

- Be respectful and considerate
- Welcome newcomers and help them get started
- Give and receive constructive feedback gracefully
- Focus on what's best for the community
- Show empathy towards other community members

Unacceptable behavior includes harassment, trolling, insulting comments, or any conduct that would make others feel unwelcome.

## How Can I Contribute?

### Reporting Bugs

Found a bug? Help us squash it!

1. **Check existing issues** to see if it's already been reported
2. **Open a new issue** if it hasn't been reported yet
3. **Include details:**
   - What you expected to happen
   - What actually happened
   - Steps to reproduce the issue
   - Your environment (OS, browser, PHP version, etc.)
   - Screenshots if applicable

### Suggesting Features

Have an idea? We'd love to hear it!

1. **Check the [Discussions tab](https://github.com/AllanKoder/ComputerScienceResources.com/discussions)** to see if someone else has suggested it
2. **Start a new discussion** with your feature idea
3. **Explain:**
   - What problem does this solve?
   - How would it work?
   - Why would this be valuable to users?

Once there's community interest, we can create an issue and start working on it!

### Your First Code Contribution

New to the project? Welcome! Here's how to get started:

1. **Look for `good first issue` labels** — these are beginner-friendly tasks
2. **Read through the [Development Setup](#development-setup)** section below
3. **Comment on the issue** to let others know you're working on it
4. **Ask questions!** We're here to help. No question is too small.

### Pull Requests

Ready to submit your changes? Here's the process:

1. **Fork the repository** and create a new branch from `master`
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/your-bug-fix
   ```

2. **Make your changes** following our [Style Guide](#style-guide)

3. **Test your changes** thoroughly
   ```bash
   ./sail test
   ```

4. **Commit your changes** with clear, descriptive messages
   ```bash
   git commit -m "Add feature: user profile avatars"
   ```

5. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Open a Pull Request** with:
   - A clear title and description
   - Reference to any related issues (e.g., "Fixes #123")
   - Screenshots or GIFs if the changes are visual
   - Notes on any breaking changes

7. **Respond to feedback** — we might suggest changes or ask questions

## Development Setup

This project uses [Laravel 11](https://laravel.com/) (PHP 8.4+) as the backend framework, with [Inertia.js](https://inertiajs.com/) and [Vue 3](https://vuejs.org/) for the frontend. We use [Laravel Sail](https://laravel.com/docs/11.x/sail) for easy Docker-based development.

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) - Required for Laravel Sail
- [Git](https://git-scm.com/)
- Basic knowledge of PHP, Laravel, and Vue.js

We use [Laravel Sail](https://laravel.com/docs/11.x/sail) for easy setup and configuration. For detailed instructions, see the [Laravel Sail documentation](https://laravel.com/docs/11.x/installation#docker-installation-using-sail).

### Installation

1. **Clone your fork:**
   ```bash
   git clone https://github.com/YOUR-USERNAME/ComputerScienceResources.com.git
   cd ComputerScienceResources.com
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Set up environment:**
   ```bash
   cp .env.example .env
   # Edit .env if needed
   ```

4. **Generate application key:**
   ```bash
   ./sail artisan key:generate
   ```

5. **Install JavaScript dependencies:**
   ```bash
   ./sail npm install
   ```

6. **Run migrations:**
   ```bash
   ./sail artisan migrate
   ```

7. **Start development servers:**
   ```bash
   # Terminal 1: Start Laravel
   ./sail up

   # Terminal 2: Start Vite dev server
   ./sail npm run dev
   ```

8. **Visit the app** at `http://localhost`

### Running Tests

Always run tests before submitting a PR:

```bash
# Run all tests
./sail test

# Run specific test file
./sail test tests/Feature/YourTest.php

# Exclude slow tests for faster feedback
./sail test --exclude-group=slow
```

## Style Guide

Following consistent code style makes collaboration easier! We follow the [Laravel naming conventions](https://webdevetc.com/blog/laravel-naming-conventions/) for controllers, models, migrations, and more.

### General Conventions

- **PascalCase** for class names: `ResourceController`, `CommentPolicy`, `ResourceReviewProcessed`
- **camelCase** for methods and variables: `getUserProfile()`, `$resourceData`
- **snake_case** for database columns, table names, migration files, and fields in request bodies: `created_at`, `resource_reviews`
- **kebab-case** for Vue component props: `user-profile`, `comment-text`

### PHP & Laravel

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) code standards (enforced by Pint)
- Use [Laravel naming conventions](https://webdevetc.com/blog/laravel-naming-conventions/)
- Keep controllers thin and focused on HTTP concerns
- Place business logic in **Service classes** or **Actions**, not controllers
- Use type hints and return types
- Write descriptive variable and method names

**Run Pint to auto-fix code style:**
```bash
./sail pint
```

### Vue & Frontend

- Use [Vue 3](https://vuejs.org/) with [Inertia.js](https://inertiajs.com/)
- Use Vue 3 Composition API when possible
- Follow [Vue style guide](https://vuejs.org/style-guide/)
- Use [Tailwind CSS](https://tailwindcss.com/) for styling
- Use `primary` and `secondary` color classes over raw hex values according to tailwind.config.js
  - Example: `class="bg-primary"`
- Use existing components from `resources/js/Components/` whenever possible (e.g., `PrimaryButton.vue`, `SecondaryButton.vue`)

### Commit Messages

Write clear commit messages that explain **what** and **why**:

```bash
# Good
git commit -m "Add pagination to resource comments"
git commit -m "Fix N+1 query in review listing"

# Not ideal
git commit -m "Update stuff"
git commit -m "WIP"
```

## Debugging with Xdebug & VS Code

Xdebug is pre-configured in the Sail Docker environment for local debugging.

### Setup

1. **Ensure Xdebug is enabled:**
   - By default, Xdebug is enabled in Sail via the `SAIL_XDEBUG_MODE` and `SAIL_XDEBUG_CONFIG` environment variables in your `.env` file.

2. **VS Code Setup:**
   - Install the [PHP Debug extension](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-debug)
   - Add a launch configuration to your `.vscode/launch.json`:

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for Xdebug",
      "type": "php",
      "request": "launch",
      "port": 9003,
      "pathMappings": {
        "/var/www/html": "${workspaceFolder}"
      }
    }
  ]
}
```

3. **Start debugging:**
   - Set breakpoints in your PHP code
   - Start the "Listen for Xdebug" configuration in VS Code
   - Trigger a request (web, test, or CLI) and Xdebug will connect to VS Code

## Deployment

For maintainers: We use Ansible for deployment with a `deploy.yaml` script and the inventory `production.ini`:

```bash
ansible-playbook -i ./ansible/inventory/production.ini deploy.yml
```

## Community

- **Questions?** Open a [Discussion](https://github.com/AllanKoder/ComputerScienceResources.com/discussions)
- **Found a bug?** Open an [Issue](https://github.com/AllanKoder/ComputerScienceResources.com/issues)
- **Want to chat?** Comment on existing issues or PRs

Remember: we're all learning together. Be patient, be kind, and have fun building something awesome!

---

**Thank you for contributing to ComputerScienceResources.com!** Your efforts help make learning computer science more accessible for everyone.
