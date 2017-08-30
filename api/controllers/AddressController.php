<?php namespace Pixiu\Commerce\api\Controllers;


use Illuminate\Http\Request;
use RainLab\User\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Pixiu\Commerce\Models\Address;

class AddressController
{
    private $user;
    private $response;

    public function add(Request $request)
    {
        $this->user = Auth::getUser();
        $validator = $this->makeAddAddressValidator($request);
        
        if ($validator->fails()) {
            return response([
                'msg' => 'Vytvoření addresy se nepodařilo.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $this->addNewAddress($request->all());

        return response(['msg' => 'Nová adresa pro uživatele ' . $this->user->email . ' vložena.'], 201);
    }

    public function edit($id, Request $request)
    {
        $this->user = Auth::getUser();
        $address = Address::findOrFail($id);

        if ($address->user_id !== $this->user->id) {
            return response([
                'msg' => 'Adresa nepatří přihlášenému uživateli.'
            ], 403);
        }

        $update = $request->except('user_id', 'created_at', 'updated_at');
        $address->update($update);

        return response(['msg' => 'Adresa byla upravena.'], 201);
    }

    public function delete($id)
    {
        $this->user = Auth::getUser();
        if (!$address = Address::find($id)){
            return response(['msg' => 'Adresa nenalezena'], 404);
        }

        if ($this->user->id !== $address->user_id){
            return response(['msg' => 'Uživatel nění oprávněn odstranit požadovanou adresu.'], 403);
        }

        $address->user_id = null;
        $address->save();

        return response(['msg' => 'Adresa byla odstraněna.'], 201);

    }

    private function makeAddAddressValidator($request)
    {
        return Validator::make($request->all(),
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'address' => 'required',
                'city' => 'required',
                'zip' => 'required',
                'country' => 'required',
            ], [
                'required' => 'Je nutné vyplnit :attribute'
            ]
        );
    }

    private function addNewAddress($values){
        $address = new Address();
        $address->user_id = $this->user->id;
        $address->first_name = $values['first_name'];
        $address->last_name = $values['last_name'];
        $address->address = $values['address'];
        $address->city = $values['city'];
        $address->zip = $values['zip'];
        $address->country = $values['country'];

        $address->dic = array_key_exists('dic', $values)
            ? $values['dic']
            : null;
        $address->ic = array_key_exists('ic', $values)
            ? $values['ic']
            : null;

        $address->save();
    }
}