<?php namespace Pixiu\Commerce\api\Controllers;

use Backend\Facades\BackendAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Pixiu\Commerce\Models\Address;
use Pixiu\Commerce\Models\Order;
use RainLab\User\Facades\Auth;
use RainLab\User\Models\User;
use October\Rain\Auth\Models\User as FuckOff;

class UserController
{
    public function show()
    {
        $user = Auth::getUser();

        $addresses = Address::where('user_id', $user->id)->get();

        return response([
            'user' => $user->only('id', 'username', 'email', 'name', 'surname'),
            'addresses' => $addresses
        ], 200);

    }

    public function edit()
    {
        //
    }

    public function register(Request $request)
    {
        $validator = $this->makeRegisterValidator($request);

        if ($validator->fails()) {
            return response([
                'msg' => 'Validace selhala', 'errors' => $validator->errors()
            ], 401);
        }

        try {
            Auth::register([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password'),
            ], true);
        } catch (\Exception $e){
            return response(['msg' => 'Registrace selhala', 'errors' => $e->getMessage()], 401);
        }

        return response(['msg' => 'Uživatel byl registrován.']);
    }

    public function login(Request $request)
    {
        $user = Auth::authenticate([
            'login' => post('login'),
            'password' => post('password')
        ], true);
        Auth::login($user, true);
        return response(['msg' => 'Uživatel '. $user->email . ' přihlášen.'], 201);
    }

    public function history()
    {
        $user = Auth::getUser();

        $orders = Order::where('user_id', $user->id)
            ->with(['variants' => function($q) {
                $q->select('id', 'slug', 'ean', 'product_id');
            }, 'variants.product' => function($q) {
                $q->select('id', 'name', 'brand_id');
            }, 'variants.product.brand' => function ($q) {
                $q->select('id', 'name');
            }])
            ->select('id', 'created_at')
            ->get()
            ->each(function($item) {
                $item->variants->map(function($variant) {
                    $variant->name = isset($variant->product->name) ? $variant->product->name : null;
                    $variant->brand = isset($variant->product->brand->name) ? $variant->product->brand->name : null;
                    $variant->quantity = $variant->pivot->quantity;
                    $variant->price = $variant->pivot->price;
                    unset($variant->pivot, $variant->product);
                    return $variant;
                });
            })
            ->toArray();

        return response([
            'msg' => 'Historie uživatele ' . $user->email . '.',
            'Orders' => $orders
        ], 201);
    }

    private function makeRegisterValidator($request)
    {
        return Validator::make($request->all(),
            [
            'username' => 'required|min:5|max:255',
            'email' => 'required|email',
            'password' => 'required|min:5|max:255'
        ], [
            'required' => 'Je nutné vyplnit :attribute',
            'email' => 'E-mail není v korektním formátě',
            'min' => ':attribute musí mít délku alespoň :min znaků',
            'max' => ':attribute musí mít méně něž :max znaků',
        ]);
    }
}