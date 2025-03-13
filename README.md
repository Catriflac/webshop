# Apps needed

+ [Composer](https://getcomposer.org/download/)

  *PHP dependency manager*

+ [Laravel](https://laravel.com/docs/12.x/installation#installing-php) 

  *PHP framework*

+ [Laragon (optional)](https://laragon.org/download/)

  *Web developement tool*

+ [Postman](https://www.postman.com/downloads/)

  *API platform*

---
---
---
---
---

# Starting new project

+ ## new Laravel project
> laravel new project_name

+ ## generate new key
> php artisan key:generate

+ ## run first migration
> php artisan migrate:fresh

+ ## serve laravel and listen on local network
> php artisan serve --host=0.0.0.0 --port=8000

| If you get an error like |
| ------------- |
| *Failed opening required 'C:\../resources/server.php' (include_path='.;C:\php\pear') in Unknown on line 0*
|
| 1. composer update 
| *If Curl error 60 occurs, disable antivirus*
| 2. composer install
| 3. php artisan serve --host=0.0.0.0 --port=8000
| *Antivirus also likes to quarantine server.php*

Now your server is running on [localhost:8000](http://localhost:8000) and you can even access it with different devices on the local network. This comes useful when working on frontend.

To access it from a different device, get your host pc's ip address: 

1. Open Laragon and check the top of the window (Laragon Full ... {IP})

2. or open **cmd** and run
> for /f "tokens=14" %i in ('ipconfig ^| findstr /i "IPv4"') do @echo %i

*192.168.0.249*

and simply open the echoed **ip** with the **port** on a web browser:
> 192.168.0.249:8000

---
---
---
---
---

# Model, migration, factory

+ ## make new model
> php artisan make:model
*PascalCase, singular: RentalItem*
*create migration, controller, factory too if needed*

+ ## define migration
```php
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rental_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rental_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('quantity');
            $table->longText('info')->nullable();
            $table->timestamps();

            $table->foreign('rental_id')->references('id')->on('rentals')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_items', function (Blueprint $table) {
            $table->dropForeign(['rental_id']);
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('rental_items');
    }
};
```

+ ## define model class
```php
class RentalItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rental_id',
        'item_id',
        'quantity',
        'info',
    ];

    /**
     * Get the rental that owns the rental item.
     * 
     */
    public function rental()
    {
        return $this->belongsTo(Rentals::class);
    }

    /**
     * Get the item that owns the rental item.
     */
    public function item()
    {
        return $this->belongsTo(Items::class);
    }
}
```
methods can be called
```php
$rentalItem = RentalItem::find(1); // Assuming you have a rental item with ID 1
$parentRental = $rentalItem->getParentRental();
```
or you can directly access the relationship property
```php
$rentalItem = RentalItem::find(1); // Assuming you have a rental item with ID 1
$parentRental = $rentalItem->rental;
```

+ ## require faker for the factories
> composer require fakerphp/faker

+ + ### generate data by calling methods
```php
public function definition(): array
{

    $faker = \Faker\Factory::create('hu_HU');

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
        'remember_token' => Str::random(10),

        // if a foreign id is needed to be retrieved from an existing table
        //'category_id' => Category::pluck('id')->random(),
    ];
}
```

+ ## set up seed with DatabaseSeeder.php
```php
public function run(): void
{
    RentalItem::factory(10)->create();
}
```

+ ## run migration with seed option
> php artisan migrate --seed

---
---
---
---
---

# Create API endpoint

+ ## install api scaffolding
> php artisan install:api

+ ## define resource route for API endpoint and set in prefix group
```php
Route::apiResource('/rentalitems', RentalItemController::class);
```
+ + ### or define singular route with method
```php
Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::get('/user/{user}', [UserController::class, 'show']);
Route::put('/user/{user}', [UserController::class, 'update']);
Route::delete('/user/{user}', [UserController::class, 'destroy'])->middleware('auth:sanctum', 'role:admin');
```
+ + ### alternatively, you can further shorten this code with a prefix for the model you're referencing
```php
Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{user}', [UserController::class, 'show']);
    Route::put('/{user}', [UserController::class, 'update']);
    Route::delete('/{user}', [UserController::class, 'destroy']);
});
```

+ + ### set prefix group for path(s)
```php
Route::prefix('v1')->group(function () {
    // Routes in /api/v1
})
```

+ + ### set middleware for authenticated calls
```php
Route::get('/profile', [ProfileController::class, 'index'])->middleware('auth:sanctum');
```

+ + ### set admin middleware
```php
Route::get('/profile', [ProfileController::class, 'index'])->middleware('auth:sanctum', 'role:admin');
```

+ ## check routes
> php artisan route:list --path=api

+ ## whole package
+ ### authenticated routes
```php
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

}
```

+ ### authenticated admin routes
```php
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('v1')->group(function () {

});
```

---
---
---
---
---

# Working with eloquent API resource

+ ## create resource
> php artisan make:resource RentalItemResource

+ ## define resource and what parameters should be returned
```php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'quantity' => $this->quantity,
        'rental' => $this->rental,
        'item' => $this->item

        // if boolean is returned at some point, you can cast it with
        // 'is_completed' => (bool) $this->is_completed
    ]
}

```

+ ## in the controller, return resource instead of eloquent collection
```php
public function index()
{
    return RentalItemResource::collection(RentalItem::all())
}

public function show(RentalItem $RentalItem)
{
    return RentalItemResource::make($RentalItem)
}
```

---
---
---
---
---

# Request validation

+ ## define rules in StoreRequest and UpdateRequest
```php
public function rules(): array
{
    return [
        'name'->'required|string|max:255'
    ]
}
```

+ ## define store method in controller
```php
class UserController extends Controller
{
    // Retrieve all resources
    // GET http://localhost/webshop/public/api/user
    public function index()
    {
        return UserResource::collection(User::all());
    }

    // Store a newly created resource in storage.
    // POST http://localhost/webshop/public/api/user
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        return UserResource::make($user);
    }

    // Display the specified resource.
    // GET http://localhost/webshop/public/api/user/10
    public function show(User $user)
    {
        return UserResource::make($user);
    }

    // Update the specified resource in storage.
    // PUT http://localhost/webshop/public/api/user/10
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        return UserResource::make($user);
    }

    // Remove the specified resource from storage.
    // DELETE http://localhost/webshop/public/api/user/10
    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }
}
```

+ ## create invokable controller
this is useful to manage various states of a record

> php artisan make:controller
>CompleteRentalItemController
> select invokable

```php
public function __invoke(Request $request, RentalItem $RentalItem)
{
    $RentalItem->is_completed = $request->is_completed;
    $RentalItem->save();

    return RentalItemResource::make($RentalItem)
}
```
and define route
```php
Route::patch('/rentalitem/{RentalItem}/complete', CompleteRentalItemController::class)
```

---
---
---
---
---

# Sanctum autentication and middleware authorization

+ ## setting up Sanctum

> php artisan install:api

installs api functionality and Sanctum authentication service, which uses Laravel's built-in **session cookies** sent in the request authorization header

in your .env file set

```php
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,yourdomain.com, ...
```

to manage stateful domains.

In **./bootstrap/app.php** set

```php
    ->withMiddleware(function (Middleware $middleware)     {
        $middleware->statefulApi();
    })
```

call to instuct Laravel to let your SPA use session cookie authentication.

+ ## Configure CORS

Since the Vue SPA is independent of the Laravel API, you'll need to set up Cross Origin Resource Sharing.

run

> php artisan config:publish cors

to set up the cors configuration file inside
**./config/cors.php**

and inside that file set

```php
'supports_credentials' = true,
```

In the .env file, set

```php
SESSION_DOMAIN=localhost
```
and later when deploying
```php
SESSION_DOMAIN=.yourdomain.com
```

+ ## Create admin middleware

Create new middleware with

> php artisan make:middleware RoleMiddleware

and set it up like

```php
    public function handle(Request $request, Closure $next, $role): Response
    {
        if ($request->user()->role !== $role) {
            return redirect('/');
        }
        return $next($request);
    }
```

In **./app/Http/Kernel.php** register the middleware with the alias **role** by adding the line

```php
    protected $middlewareAliases = [
        
        ...

        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];
```

+ ## Using authenticated routes with authorization

To access **yourdomain.com/api/admin/ImportantAdminStuff** API endpoint, use

```php
Route::group(['middleware' => ['auth:sanctum', 'role:admin'], 'prefix' => 'admin'], function () {

    Route::get('/ImportantAdminStuff', [AdminController::class, 'ImportantAdminStuff']);

});
```

in **api.php**. Note that this middleware authorization can also be used to protect web routes.

---
---
---
---
---

















    
