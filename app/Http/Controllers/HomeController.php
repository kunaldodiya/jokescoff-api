<?php namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\SigninRequest;
use App\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @Middleware("web")
 */
class HomeController extends Controller
{
    /**
     * @Get("/login", as="get-login")
     */
    public function login()
    {
        return view('admin.account.login');
    }

    /**
     * @Get("/logout", as="get-logout")
     */
    public function logout()
    {
        auth()->logout();

        return redirect()->to('/login');
    }

    /**
     * @Post("/login", as="post-login")
     * @param SigninRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function loginProcess(SigninRequest $request)
    {
        $auth = auth()->attempt([
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ]);

        return $auth ? redirect('admin/dashboard') : back()->with(['error' => "Login Failed."]);
    }

    /**
     * @Get("/401", as="401")
     */
    public function unAuthorizedAccess()
    {
        return 'unauthorized access';
    }

    /**
     * @Post("/sync", as="sync")
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function sync(Request $request)
    {
        $sync_at = Carbon::now()->format("Y-m-d H:i:s");
        $last_sync = request()->get("last_sync");

        $categories_query = DB::table('categories');
        if ($last_sync) {
            $categories_query->where('updated_at', '>=', $last_sync);
        }
        $categories = $categories_query->get();

        $categories_query = DB::table('posts');
        if ($last_sync) {
            $categories_query->where('updated_at', '>=', $last_sync);
        }
        $posts = $categories_query->get();

        return response(['last_sync' => $sync_at, 'categories' => $categories, 'posts' => $posts]);
    }
}