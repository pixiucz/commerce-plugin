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

        $user = User::with([
            'addresses',
            'orders.payment_method',
            'orders.delivery_option',
            'orders.delivery_address',
            'orders.billing_address',
            'orders.variants',
            'orders.variants.product',
            'orders.variants.product.tax',
            'orders.variants.attributes',
            ])->find($user->id);

        return response([
            'user' => $user->only('id', 'username', 'email', 'name', 'surname'),
            'addresses' => $user->addresses ?? [],
            'orders' => $this->getUserOrders($user) ?? [],
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
                'email' => $request->input('login'),
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password'),
            ], true);
        } catch (\Exception $e){
            return response(['msg' => 'Registrace selhala', 'errors' => $e->getMessage()], 401);
        }

        return response(['msg' => 'Uživatel byl registrován.'], 201);
    }

    public function login(Request $request)
    {
        $user = Auth::authenticate([
            'login' => $request->input('login'),
            'password' => $request->input('password'),
        ], true);

        Auth::login($user, true);

        return $this->show();
    }

    public function history()
    {
        $user = Auth::getUser();

        $orders = $this->getUserOrders($user);

        return response([
            'msg' => 'Historie uživatele ' . $user->email . '.',
            'Orders' => $orders
        ], 201);
    }

    public function logout()
    {
        Auth::logout();
        return response([], 200);
    }

    public function validateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response(['msg' => 'Nevalidní email'], );
        }
    }

    private function makeRegisterValidator($request)
    {
        trace_log($request->all());

        return Validator::make($request->all(),
            [
            'login' => 'required|email',
            'password' => 'required|min:4|max:255'
        ], [
            'required' => 'Je nutné vyplnit :attribute',
            'email' => 'E-mail není v korektním formátě',
            'min' => ':attribute musí mít délku alespoň :min znaků',
            'max' => ':attribute musí mít méně něž :max znaků',
        ]);
    }

    // FIXME: Presunout do user modelu
    private function getUserOrders($user) {
        return Order::where('user_id', $user->id)
            ->with(['variants' => function($q) {
                $q->select('id', 'slug', 'ean', 'product_id');
            }, 'variants.product' => function($q) {
                $q->select('id', 'name', 'brand_id', 'tax_id');
            }, 'variants.product.brand' => function ($q) {
                $q->select('id', 'name');
            }, 'variants.product.tax',
            'variants.attributes'])
            ->select('id', 'created_at', 'status')
            ->get()
            ->each(function($item) {
                $item->variants->map(function($variant) {
                    $variant->product_name = isset($variant->product->name) ? $variant->product->name : null;
                    $variant->brand = isset($variant->product->brand->name) ? $variant->product->brand->name : null;
                    $variant->quantity = $variant->pivot->quantity;
                    $variant->price = $variant->pivot->price;
                    $variant->tax_rate = isset($variant->product->tax) ? $variant->product->tax->rate : null;
                    unset($variant->pivot, $variant->product);
                    return $variant;
                });
            })
            ->toArray();
    }

}