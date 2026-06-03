# Event Management Mobile

Flutter mobile app for the Laravel Event Management and Quotation System.

## Run

Start Laravel from the project root:

```bash
php artisan serve --host=0.0.0.0
```

Run Flutter from this folder:

```bash
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

Use `10.0.2.2` for the Android emulator. For a real phone, replace it with your computer's LAN IP address.
