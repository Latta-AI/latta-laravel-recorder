To implement Latta into Laravel backend do:

1. Install Latta Recorder via Composer

```
composer require lattaai/latta-laravel-recorder
```

2. Insert API Key into ENV File

```
LATTA_API_KEY=xyz
```

3. Add lines to bootstrap/app.php into withExceptions() function

```
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->report(function (Throwable $e) {
        $lattaRecorder = new LattaLaravelRecorder(env('LATTA_API_KEY'));
        $lattaRecorder->reportError($e);
    });
})->create();
```

4. Add lines to app/providers/AppServiceProvider.php into boot function

```
public function boot(): void
{
    $lattaRecorder = new LattaLaravelRecorder(env('LATTA_API_KEY'));
    $lattaRecorder->startRecording("Laravel", app()->version(), PHP_OS, "en", "server");
}
```