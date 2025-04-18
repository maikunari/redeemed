# Download Code System Development Guide

## Overview
This guide directs the development of a secure, user-friendly webapp for managing downloadable content via unique redemption codes, built with **Laravel** (backend) and **Vue.js** (frontend). The system allows admins to upload files, generate codes, and track usage, while users redeem codes to download files. The app must be deployable on shared hosting with PHP 8.1+, MySQL, and Composer support.

## Tech Stack
- **Backend**: Laravel 11.x (PHP 8.1+)
- **Frontend**: Vue.js 3 (via Inertia.js for seamless integration)
- **Database**: MySQL
- **Styling**: Tailwind CSS
- **Tools**:
  - Laravel Vite for asset compilation
  - Spatie Laravel Media Library for file and thumbnail management
  - SimpleSoftwareIO Simple QRCode for QR code generation
  - League CSV for CSV exports
- **Deployment**: Shared hosting (PHP, MySQL, Apache/Nginx)

## Development Principles
- **Simplicity**: Use Laravel's built-in features and minimal custom code.
- **Modularity**: Organize code into models, controllers, and Vue components.
- **Security**: Validate inputs, secure file handling, and protect routes.
- **Maintainability**: Follow Laravel/Vue conventions, add comments, and log errors.
- **Performance**: Optimize database queries and cache where possible.

## Project Setup
1. **Initialize Laravel Project**:
   - Run `composer create-project laravel/laravel download-code-system`.
   - Configure `.env`:
     ```env
     APP_NAME="Download Code System"
     APP_URL=http://localhost
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=download_code_system
     DB_USERNAME=root
     DB_PASSWORD=
     FILESYSTEM_DISK=local
     ```
   - Set up MySQL database `download_code_system`.

2. **Install Dependencies**:
   - Run:
     ```bash
     composer require laravel/breeze inertiajs/inertia-laravel spatie/laravel-medialibrary simplesoftwareio/simple-qrcode league/csv
     npm install @inertiajs/vue3 vue@3 tailwindcss postcss autoprefixer
     ```
   - Install Breeze with Vue/Inertia: `php artisan breeze:install vue --inertia`.
   - Initialize Tailwind CSS:
     ```bash
     npx tailwindcss init
     ```
     Update `tailwind.config.js`:
     ```javascript
     module.exports = {
       content: [
         './resources/**/*.blade.php',
         './resources/**/*.js',
         './resources/**/*.vue',
       ],
       theme: { extend: {} },
       plugins: [],
     }
     ```

3. **Configure Inertia.js**:
   - Update `resources/js/app.js`:
     ```javascript
     import { createApp, h } from 'vue';
     import { createInertiaApp } from '@inertiajs/vue3';
     import '../css/app.css';

     createInertiaApp({
       resolve: name => import(`./Pages/${name}.vue`),
       setup({ el, App, props, plugin }) {
         createApp({ render: () => h(App, props) })
           .use(plugin)
           .mount(el);
       },
     });
     ```
   - Update `resources/css/app.css`:
     ```css
     @tailwind base;
     @tailwind components;
     @tailwind utilities;
     ```

4. **File Storage**:
   - Configure `config/filesystems.php`:
     ```php
     'disks' => [
       'local' => [
         'driver' => 'local',
         'root' => storage_path('app/files'),
       ],
     ],
     ```
   - Run `php artisan storage:link`.

## Database Schema
Create migrations for the following tables:

1. **files**:
   ```php
   Schema::create('files', function (Blueprint $table) {
     $table->id();
     $table->string('title');
     $table->string('path');
     $table->string('thumbnail')->nullable();
     $table->timestamps();
   });
   ```

2. **download_codes**:
   ```php
   Schema::create('download_codes', function (Blueprint $table) {
     $table->id();
     $table->string('code')->unique();
     $table->foreignId('file_id')->constrained()->onDelete('cascade');
     $table->integer('usage_limit')->default(1);
     $table->integer('usage_count')->default(0);
     $table->timestamp('expires_at')->nullable();
     $table->timestamps();
   });
   ```

3. **users** (for admins, via Breeze):
   - Use Laravel Breeze's default `users` table.

Run migrations: `php artisan migrate`.

## Models
1. **File.php**:
   ```php
   namespace App\Models;
   use Illuminate\Database\Eloquent\Model;
   use Spatie\MediaLibrary\HasMedia;
   use Spatie\MediaLibrary\InteractsWithMedia;

   class File extends Model implements HasMedia {
     use InteractsWithMedia;
     protected $fillable = ['title', 'path'];
     public function codes() {
       return $this->hasMany(DownloadCode::class);
     }
   }
   ```

2. **DownloadCode.php**:
   ```php
   namespace App\Models;
   use Illuminate\Database\Eloquent\Model;

   class DownloadCode extends Model {
     protected $fillable = ['code', 'file_id', 'usage_limit', 'usage_count', 'expires_at'];
     public function file() {
       return $this->belongsTo(File::class);
     }
   }
   ```

## Backend Implementation
### Controllers
1. **FileController.php**:
   ```php
   namespace App\Http\Controllers;
   use App\Models\File;
   use Illuminate\Http\Request;
   use Inertia\Inertia;
   use Spatie\MediaLibrary\MediaCollections\Models\Media;

   class FileController extends Controller {
     public function index() {
       return Inertia::render('Files/Index', [
         'files' => File::with('media')->get(),
       ]);
     }

     public function store(Request $request) {
       $request->validate([
         'title' => 'required|string|max:255',
         'file' => 'required|file|mimes:pdf,zip,mp3,jpg,png|max:20480',
       ]);

       $file = File::create(['title' => $request->title]);
       $file->addMedia($request->file('file'))
            ->toMediaCollection('files');

       return redirect()->route('files.index')->with('success', 'File uploaded.');
     }

     public function destroy(File $file) {
       $file->delete();
       return redirect()->route('files.index')->with('success', 'File deleted.');
     }
   }
   ```

2. **DownloadCodeController.php**:
   ```php
   namespace App\Http\Controllers;
   use App\Models\DownloadCode;
   use App\Models\File;
   use Illuminate\Http\Request;
   use Inertia\Inertia;
   use SimpleSoftwareIO\QrCode\Facades\QrCode;
   use League\Csv\Writer;

   class DownloadCodeController extends Controller {
     public function store(Request $request, File $file) {
       $request->validate([
         'usage_limit' => 'required|integer|min:1',
         'expires_at' => 'nullable|date|after:now',
       ]);

       $code = DownloadCode::create([
         'code' => \Str::random(12),
         'file_id' => $file->id,
         'usage_limit' => $request->usage_limit,
         'expires_at' => $request->expires_at,
       ]);

       return redirect()->route('files.index')->with('success', 'Code generated.');
     }

     public function redeem(Request $request) {
       $request->validate(['code' => 'required|string']);
       $code = DownloadCode::where('code', $request->code)->firstOrFail();

       if ($code->usage_count >= $code->usage_limit || ($code->expires_at && $code->expires_at->isPast())) {
         return back()->withErrors(['code' => 'Invalid or expired code.']);
       }

       $code->increment('usage_count');
       $media = $code->file->getFirstMedia('files');
       return response()->download($media->getPath(), $media->file_name);
     }

     public function export(File $file) {
       $csv = Writer::createFromString();
       $csv->insertOne(['Code', 'Usage Limit', 'Usage Count', 'Expires At']);
       foreach ($file->codes as $code) {
         $csv->insertOne([$code->code, $code->usage_limit, $code->usage_count, $code->expires_at]);
       }
       return response($csv->toString(), 200, [
         'Content-Type' => 'text/csv',
         'Content-Disposition' => "attachment; filename=codes-{$file->id}.csv",
       ]);
     }

     public function qrcode(DownloadCode $code) {
       $url = route('redeem', ['code' => $code->code]);
       return QrCode::size(200)->generate($url);
     }
   }
   ```

### Routes
Update `routes/web.php`:
```php
use App\Http\Controllers\FileController;
use App\Http\Controllers\DownloadCodeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
  Route::get('/files', [FileController::class, 'index'])->name('files.index');
  Route::post('/files', [FileController::class, 'store'])->name('files.store');
  Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
  Route::post('/files/{file}/codes', [DownloadCodeController::class, 'store'])->name('codes.store');
  Route::get('/files/{file}/codes/export', [DownloadCodeController::class, 'export'])->name('codes.export');
  Route::get('/codes/{code}/qrcode', [DownloadCodeController::class, 'qrcode'])->name('codes.qrcode');
});

Route::get('/', fn () => Inertia::render('Welcome'))->name('welcome');
Route::post('/redeem', [DownloadCodeController::class, 'redeem'])->name('redeem');
```

## Frontend Implementation

### Code Input Requirements
- **Code Format**: Eight numerical digits in `XXXX-XXXX` format (e.g., `1234-5678`)
- **Input Behavior**:
  - Numbers only input (no letters allowed)
  - Manual entry only (no paste functionality)
  - Auto-insert hyphen after first 4 digits
  - Auto-advance between digits
  - Visual feedback/highlighting as each digit is entered
  - Auto-submit when all 8 digits are entered
  - Similar to Google Authenticator interface behavior
  - Mobile-friendly input experience

### Vue Components
1. **resources/js/Pages/Welcome.vue** (Public Redemption Page):
   ```vue
   <template>
     <div class="min-h-screen flex items-center justify-center bg-gray-100">
       <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
         <h1 class="text-2xl font-bold mb-6">Redeem Code</h1>
         <form @submit.prevent="redeem">
           <input v-model="form.code" type="text" placeholder="Enter code" class="w-full p-2 border rounded mb-4" />
           <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Redeem</button>
         </form>
         <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
       </div>
     </div>
   </template>

   <script setup>
   import { ref } from 'vue';
   import { Inertia } from '@inertiajs/inertia';

   const form = ref({ code: '' });
   const error = ref(null);

   const redeem = () => {
     Inertia.post('/redeem', form.value, {
       onError: (errors) => (error.value = errors.code),
     });
   };
   </script>
   ```

2. **resources/js/Pages/Files/Index.vue** (Admin Dashboard):
   ```vue
   <template>
     <div class="p-6">
       <h1 class="text-3xl font-bold mb-6">File Management</h1>
       <form @submit.prevent="upload" class="mb-8">
         <input type="text" v-model="form.title" placeholder="File title" class="p-2 border rounded mr-2" />
         <input type="file" @change="form.file = $event.target.files[0]" class="p-2" />
         <button type="submit" class="bg-green-500 text-white p-2 rounded">Upload</button>
       </form>

       <div class="grid gap-4">
         <div v-for="file in files" :key="file.id" class="border p-4 rounded">
           <h2 class="text-xl">{{ file.title }}</h2>
           <img v-if="file.media[0]" :src="file.media[0].original_url" class="w-20 h-20" />
           <button @click="generateCode(file)" class="bg-blue-500 text-white p-2 rounded">Generate Code</button>
           <button @click="exportCodes(file)" class="bg-gray-500 text-white p-2 rounded">Export Codes</button>
           <button @click="deleteFile(file)" class="bg-red-500 text-white p-2 rounded">Delete</button>
         </div>
       </div>
     </div>
   </template>

   <script setup>
   import { ref } from 'vue';
   import { Inertia } from '@inertiajs/inertia';

   const props = defineProps(['files']);
   const form = ref({ title: '', file: null });

   const upload = () => {
     const formData = new FormData();
     formData.append('title', form.value.title);
     formData.append('file', form.value.file);
     Inertia.post('/files', formData);
   };

   const generateCode = (file) => {
     Inertia.post(`/files/${file.id}/codes`, { usage_limit: 1 });
   };

   const exportCodes = (file) => {
     window.location = `/files/${file.id}/codes/export`;
   };

   const deleteFile = (file) => {
     Inertia.delete(`/files/${file.id}`);
   };
   </script>
   ```

## Security
- **Input Validation**: Use Laravel's validation in controllers.
- **File Security**: Store files in `storage/app/files`, serve via Laravel's download response.
- **Authentication**: Use Breeze's middleware for admin routes.
- **CSRF**: Handled by Inertia.js and Laravel's CSRF token.
- **Error Logging**: Configure `config/logging.php` for file-based logging.

## Deployment
1. **Shared Hosting**:
   - Upload files via FTP, excluding `node_modules` and `vendor`.
   - Run `composer install --optimize-autoloader` on the server.
   - Copy `.env.example` to `.env` and update database credentials.
   - Run `php artisan key:generate` and `php artisan migrate`.

2. **Asset Compilation**:
   - Run `npm run build` locally and upload compiled assets.
   - Ensure `.htaccess` enables URL rewriting:
     ```htaccess
     <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteBase /
       RewriteRule ^index\.php$ - [L]
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteRule . /index.php [L]
     </IfModule>
     ```

## Testing
- Test file uploads, code generation, redemption, and QR code generation.
- Verify security (e.g., unauthorized access, invalid inputs).
- Check responsiveness on mobile and desktop.

## AI Agent Instructions
- Follow Laravel/Vue conventions.
- Use provided code as a starting point; extend for features like QR code display or drag-and-drop uploads.
- Log errors to `storage/logs/laravel.log`.
- Test each feature incrementally.
- Optimize queries with Eloquent's `with()` for relationships.
- Keep components reusable and minimal.