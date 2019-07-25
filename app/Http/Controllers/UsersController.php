<?php
namespace App\Http\Controllers;

use App\Http\Resources\UsersResource;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Class UsersController
 * @property User user
 * @package App\Http\Controllers
 */
class UsersController extends Controller
{
    /**
     * @var User
     */
    protected $user;
    /**
     * UsersController constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /**
     * List of users in Json format
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = $this->user->orderBy($request->column ?? 'id', $request->order ?? 'ASC');
        $users = $query->paginate($request->per_page ?? 10);
        return UsersResource::collection($users);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        
	$validationData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'email|string|max:255|unique:users',
            'id_number' => 'required|string|max:11|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

	if ($validationData->fails()) {
	    return response()->json($validationData->errors()->toArray(), 422);
	}

        $user = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'gender' => $data['gender'],
            'email' => $data['email'],
            'id_number' => $data['id_number'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json($user);
    }
}
