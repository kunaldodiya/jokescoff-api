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
     * @Post("/sync/posts", as="sync-posts")
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function syncPosts(Request $request)
    {
        $last_sync = $request->get("last_sync");
        $limit = $request->get("limit");

        return response(['data' => $this->getSyncData("posts", $last_sync, $limit)]);
    }

    /**
     * @Post("/sync/categories", as="sync-categories")
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function syncCategories(Request $request)
    {
        $last_sync = $request->get("last_sync");
        $limit = $request->get("limit");

        return response(['data' => $this->getSyncData("categories", $last_sync, $limit)]);
    }

    public function getSyncData($table, $last_sync, $limit)
    {
        $query = DB::table($table);

        if ($last_sync) {

            $query->where('updated_at', '>', $last_sync);
        }

        return $query->orderBy("updated_at", "ASC")->limit($limit)->get();
    }
}