### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd camagru
```

2. Start the application:
```bash
docker compose up -d --build
```

3. Initialize the database:
```bash
docker compose exec app php public/SQLschema.php
```
   > **Note:** If this command fails, wait 30 seconds for the database to fully initialize, then retry.

4. Access the application:
   - Register: `http://localhost:8000/register`
   - Login: `http://localhost:8000/login`
   - and so on..