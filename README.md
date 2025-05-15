# Dart Back

A modern PHP application built with Symfony 7 following Clean Architecture and Domain-Driven Design principles for
managing players, games, and scoring.

## Tech Stack

- **PHP 8**
- **Symfony 7** framework
- **FrankenPHP** (High-performance PHP application server)
- **Docker** for containerization
- **PostgreSQL** database
- **Clean Architecture** and **Domain-Driven Design** methodology

## Features

- **Player Management**: Create, update, and manage players with unique names
- **Game Management**: Track games, their statuses, and associated players
- **Scoring System**: Record and retrieve scores for players in specific games
- **Event-Driven Architecture**: Uses Symfony Messenger for handling domain events
- **Validation**: Ensures data integrity with Symfony Validator
- **Error Handling**: Graceful handling of validation and transactional errors

## Prerequisites

- Docker Compose (v2.10+)
- Git

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/PALandoin/Dart_Back
   cd Dart_Back
   ```

2. Build the project:
   ```bash
   docker compose build --no-cache
   ```

3. Start the application:
   ```bash
   docker compose up --pull always -d --wait
   ```

4. Access the application:
    - Open https://localhost in your browser and accept the TLS certificate

5. Stop the application:
   ```bash
   docker compose down --remove-orphans
   ```

## Configuration

- **Environment Variables**: Configure database credentials and other settings in the `.env` file
- **Database**: PostgreSQL is used as the default database. Ensure the `POSTGRES_*` variables are set correctly

## Project Structure

The project follows a Clean Architecture approach with Domain-Driven Design principles:

```
src/
├── Domain/              # Domain layer: entities, value objects, domain services
│   ├── Entity/          # Domain entities like Player, Game, Score
│   ├── ValueObject/     # Value objects for immutable concepts
│   ├── Repository/      # Repository interfaces
│   ├── Service/         # Domain services
│   └── Event/           # Domain events like PlayerCreatedEvent
├── Application/         # Application layer: use cases, commands, queries
│   ├── Command/         # Command handlers
│   ├── Query/           # Query handlers
│   ├── EventListener/   # Application event listeners
│   └── Service/         # Application services
├── Infrastructure/      # Infrastructure layer: implementation details
│   ├── Repository/      # Repository implementations
│   ├── Persistence/     # Database configurations
│   └── Service/         # External service integrations
└── UserInterface/       # User interface layer: controllers, forms, views
    ├── Controller/      # Symfony controllers
    ├── Form/            # Form types
    └── Validator/       # Custom validators
```

## Development

- **Testing**: Use PHPUnit for unit and integration tests
- **Validation**: Symfony Validator ensures data consistency
- **Event Listeners**: Custom event listeners handle domain events like PlayerCreatedEvent

## Deployment

- **Production Ready**: Optimized for production with Docker and Symfony best practices
- **HTTPS**: Automatic HTTPS setup with Caddy

## Troubleshooting

- **Database Issues**: Check Docker logs for the database service:
  ```bash
  docker compose logs database
  ```
- **Validation Errors**: Ensure data meets the validation rules defined in the domain models

## License

This project is available under the MIT License.
