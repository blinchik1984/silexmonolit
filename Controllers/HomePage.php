<?php

namespace Monolit\Controllers;

use AdServer\Engine\Components\Engine;
use Symfony\Component\HttpFoundation\Response;

class HomePage
{
    public function index()
    {
        return Engine::getDI()->get('targetClientApi')->request('/');
    }

    public function hello()
    {
        return new Response("Hello");
    }
}