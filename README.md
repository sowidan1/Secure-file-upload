# Laravel File Upload Handler

This project provides a secure and robust solution for uploading and processing image files in a Laravel application. It ensures uploaded files are validated, sanitized, and stored securely while preventing potential malicious content.

## Features

- Validates file type and size.
- Scans and ensures uploaded files are valid images using `getimagesize()` and `Intervention Image`.
- Strips malicious content by re-encoding the image.
- Stores files securely in a non-web-accessible location.
- Logs upload activities for monitoring and debugging.

- [Intervention Image](https://image.intervention.io/)
- [Laravel](https://laravel.com/)


