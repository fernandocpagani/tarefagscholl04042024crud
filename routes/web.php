<?php

use App\Http\Controllers\ProfileController;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Image;
use App\Models\Permission;
use App\Models\Preference;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/many-to-many-polymorphic', function(){
    // $course = Course::first();

    // Tag::create(['name' => 'tag1', 'color' => 'blue']);
    // Tag::create(['name' => 'tag2', 'color' => 'red']);
    // Tag::create(['name' => 'tag3', 'color' => 'green']);

    // $course->tags()->attach(2);

    // dd($course->tags);

    $tag = Tag::where('name', 'tag2')->first();
    dd($tag->users);
});

Route::get('/one-to-many-polymorphic', function(){
    // $course = Course::first();
    
    // $course->comments()->create([
    //     'subject' => 'Novo comentário 2',
    //     'content' => 'Apenas um comentario legal 2',
    // ]);


    // dd($course->comments);



    $comment = Comment::find(1);

    dd($comment->commentable);
});

Route::get('/one-to-one-polymorphic', function(){
    $user = User::first();

    $data = ['path' => 'path/nome-image.png'];

    $user->image->delete();

    if ($user->image){
        $user->image->update($data);
    }else{
        $user->image()->create($data);
    }

    dd($user->image->path);
});

Route::get('/many-to-many-pivot', function(){
    $user = User::with('permissions')->find(1);

    // $user->permissions()->attach([
    //     1 => ['active' => false],
    //     3 => ['active' => false],
    // ]);

    echo "<b>{$user->name}</b><br>";
    foreach($user->permissions as $permission){
        echo "{$permission->name} - {$permission->pivot->active}<br>";
    }
});

Route::get('/many-to-many', function(){
    // dd(Permission::create(['name' => 'menu_03']));

    $user = User::with('permissions')->find(1);

    // $permission = Permission::find(1);

    // $user->permissions()->save($permission);

    // $user->permissions()->saveMany([
    //     Permission::find(1),
    //     Permission::find(3),
    //     Permission::find(2),
    // ]);

    // $user->permissions()->sync([2]);

    // $user->permissions()->attach([1, 3]);

    $user->permissions()->detach([1, 3]);
    

    $user->refresh();

    dd($user->permissions);
});

Route::get('/one-to-many', function () {
    // $course = Course::create(['name' => 'Curso de Laravel']);

    $course = Course::with('modules.lessons')->first();

    echo $course->name;
    echo '<br>';
    echo '<br>';

    foreach ($course->modules as $module){
        echo "Modulo {$module->name} <br>";

        foreach ($module->lessons as $lesson) {
            echo "Aula {$lesson->name} <br>";
        }
        echo '<br>';
    }

    dd($course);

    $data = [
        'name' => 'Modulo x2'
    ];

    $course->modules()->create($data);

    $modules = $course->modules;

    dd($modules);
});

Route::get('/one-to-one', function(){
    $user = User::with('preference')->find(2);
  
    $data = [
        'background_color' => '#fff',
    ];

    if($user->preference){
        $user->preference->update($data);
    } else {
        // $user->preference()->create($data);
        $preference = new Preference($data);
        $user->preference()->save($preference);
    }
 
    $user->refresh();

    var_dump($user->preference);

    $user->preference->delete();

    $user->refresh();

    dd($user->preference);
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
