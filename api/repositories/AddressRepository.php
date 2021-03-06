<?php namespace Pixiu\Commerce\Api\Repositories;


use Illuminate\Http\Request;
use Pixiu\Commerce\Models\Address;
use RainLab\User\Facades\Auth;
use RainLab\User\Models\User;

/**
 * Class AddressRepository
 * @package Pixiu\Commerce\Api\Repositories
 */
class AddressRepository
{

    /**
     * @var Address
     */
    private $address;

    /**
     * @var Request
     */
    private $request;

    /**
     * AddressRepository constructor.
     */
    public function __construct(Address $address, Request $request)
    {
        $this->address = $address;
        $this->request = $request;
    }

    /**
     * @return $this
     */
    public function new()
    {
        $this->address = new Address();

        return $this;
    }

    /**
     * @return Address
     */
    public function get()
    {
        $clone = clone $this->address;
        $clone->save();

        return $clone;
    }


    /**
     * @return $this
     * @throws \Exception
     */
    public function fillDeliveryFromRequest()
    {
        $content = $this->request->input('deliveryAddress') ?? [];

        if (empty($content)) {
            throw new \Exception('Request doesn\'t contain deliveryAddress');
        }

        $this->address->fill($content);

        return $this;
    }

    /**
     * @return $this
     *
     * Assumes billing is the same as delivery
     * if no billing found in request
     *
     */
    public function fillBillingFromRequest()
    {
        $content = $this->request->input('billingAddress') ?? [];

        if (empty($content)) {
            $this->fillDeliveryFromRequest();
            return $this;
        }

        $this->address->fill($content);

        return $this;
    }

    /**
     * @param User|null $passedUser
     * @return $this
     * @throws \Exception
     */
    public function setUser(User $passedUser = null)
    {
        if (!$user = $passedUser ?? Auth::getUser()) {
            throw new \Exception('User not passed or logged in. Provide user');
        }

        $this->address->user_id = $user->id;

        return $this;
    }
}