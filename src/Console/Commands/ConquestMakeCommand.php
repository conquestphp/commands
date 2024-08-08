<?php

// namespace Conquest\Assemble\Console\Commands;

// use Illuminate\Console\Command;
// use Illuminate\Support\Facades\Artisan;

// class EndpointMakeCommand extends Command
// {
//     /**
//      * The name and signature of the console command.
//      *
//      * @var string
//      */
//     protected $signature = 'make:conquest {name} {--m|modal} {--p|page} {--f|form}';

//     /**
//      * The console command description.
//      *
//      * @var string
//      */
//     protected $description = 'Make a single action controller and request pair';

//     /**
//      * Execute the console command.
//      */

//     protected function getInputName()
//     {
//         return $this->argument('name');
//     }

//     public function handle()
//     {
//         $rawPath = explode('/', $this->getInputName());
//         $end = $this->removeType(array_pop($rawPath));
//         $path = implode('/', $rawPath);

//         $controller = $this->buildPath($path, $end.'Controller');
//         $controllerPath = app_path("Http/Controllers/$controller.php");
//         $request = $this->buildPath($path, $end.'Request');
//         $vue = $this->buildPath($path, $end);
        
//         $this->call('make:controller', [
//             'name' => $controller,
//             '--invokable' => true,
//         ]);


//         $this->call('make:request', [
//             'name' => $request,
//         ]); 

//         // Update request in controller to use namespace 
//         $this->replaceInFile(
//             'use Illuminate\Http\Request;',
//             'use '.self::REQUEST_NAMESPACE.str_replace('/', '\\', $request).';',
//             $controllerPath
//         );

//         $this->replaceInFile(
//             'Request $request',
//             $end."Request \$request",
//             $controllerPath
//         );

//         if ($this->option('modal')) {
//             $this->call('make:modal', [
//                 'name' => $this->buildPath($path, $end),
//                 '-f' => $this->option('form')
//             ]);

//             $this->replaceInFile(
//                 '//',
//                 "return Inertia::modal('".$vue."')\n\t\t\t->with([\n\t\t\t\t\n\t\t\t])->baseRoute('".self::MODAL_BASEROUTE."');",
//                 $controllerPath
//             );
//         }
//         else if ($this->option('page')) {
//             $this->call('make:page', [
//                 'name' => $this->buildPath($path, $end),
//                 '-f' => $this->option('form')
//             ]);

//             $this->replaceInFile(
//                 '//',
//                 "return Inertia::render('".$vue."', [\n\t\t\t\n\t\t]);",
//                 $controllerPath
//             );
//         }

//         if ($this->option('page') || $this->option('modal')) {
//             $this->replaceInFile(
//                 'use App\Http\Controllers\Controller;',
//                 'use App\Http\Controllers\Controller;'.PHP_EOL.'use Inertia\Inertia;',
//                 $controllerPath
//             );
//         }
//     }

//     protected function replaceInFile($search, $replace, $path)
//     {
//         file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
//     }

//     protected function buildPath(string $path, string $file)
//     {
//         if ($path === '') return $file;
//         return $path . '/' . $file;
//     }

//     protected function removeType(string $name)
//     {
//         $remove = ['Controller', 'Request'];
//         foreach ($remove as $r) {
//             $name = str_replace($r, '', $name);
//         }
//         return $name;
//     }
// }
