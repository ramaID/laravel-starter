# Laravel 10: Starter

Sebuah starter project untuk Laravel 10 yang sudah dilengkapi dengan beberapa fitur yang akan meningkatkan _developer experience_. Diantaranya:

1. Script composer:
    - `composer test` untuk menjalankan capaian dan target test
    - `composer typed` untuk menjalankan capaian dan target type checking
    - `composer qc` shortcut untuk menjalankan `composer test` dan `composer typed`
2. Sample CI/CD GitLab pada `.gitlab-ci.yml` untuk menjalankan test dan type checking
3. Sample config SonarQube pada `sonar-project.properties.example` untuk menjalankan analisis kode
4. Capaian test dan type checking yang sudah 100% coverage

## Instalasi

1. Clone repository ini
2. Jalankan `composer install`
3. Copy `.env.example` ke `.env`
4. Jalankan `php artisan key:gen`
5. Jalankan `php artisan migrate`

### Validasi quality control

1. Setelah melakukan instalasi di atas
2. Copy `.env.testing.example` ke `.env.testing`
3. Jalankan `php artisan key:gen --env=testing`
4. Jalankan `composer qc`

### Menjalankan sonar-scanner

1. Copy `sonar-project.properties.example` ke `sonar-project.properties`
2. Sesuaikan konfigurasi pada `sonar-project.properties`, terutama pada:
    - `sonar.host.url`
    - `sonar.login`
    - `sonar.projectKey`
3. Jalankan binary `sonar-scanner`
