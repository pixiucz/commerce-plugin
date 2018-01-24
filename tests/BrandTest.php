<?php namespace Pixiu\Commerce\Tests;

use Backend\Facades\BackendAuth;
use Backend\Models\User;
use Backend\Widgets\Form;
use Illuminate\Contracts\Session\Session;
use Pixiu\Commerce\Controllers\Brands;
use PluginTestCase;
use Pixiu\Commerce\Models\Brand;
use Backend\Facades\Backend;

class BrandTest extends PluginTestCase
{
    public function setUp()
    {
        parent::setUp();
        $user = BackendAuth::register([
            'login' => 'someuser',
            'email' => 'some@website.tld',
            'password' => 'changeme',
            'password_confirmation' => 'changeme'
        ]);

        $user->is_superuser = 1;

//        BackendAuth::login($user);
    }

    public function testCreateFirstPost()
    {
        $brand = (new Brand())->fill(['name' => 'KOKOT']);
        $brand->save();
        $this->assertEquals(1, $brand->id);
    }

    public function testAccessController()
    {
        $this->get(Backend::url('pixiu/commerce/products'))->assertSee('Control Panel');
    }

    public function test_superuser_can_create_brand()
    {

        $controller = new Brands();

        $_POST['Brand'] = [
            'name' => 'KOKOT',
        ];
        $controller->create_onSave();

        $brand = Brand::first();
        $this->assertInstanceOf(Brand::class, $brand);
    }
}