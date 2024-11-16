<?php

namespace App\Filters;

use \CodeIgniter\Filters\FilterInterface;
use \CodeIgniter\HTTP\RequestInterface;
use \CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class BasicAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $Authorization = $request->getServer('HTTP_AUTHORIZATION');

        if (!$Authorization) {
            return Services::response()->setStatusCode(400)->setJSON([
                'status' => 'false',
                'message' => 'UnAuthorized Access'
            ]);
        }

        // Verifying Headers ['Basic', 'username:password']
        $AuthorizedParts = explode(' ', $Authorization);
        if (count($AuthorizedParts) !== 2 || $AuthorizedParts[0] !== 'Basic') {
            return Services::response()->setStatusCode(400)->setJSON([
                'status' => 'false',
                'message' => 'UnAuthorized Access'
            ]);
        }

        list($username, $password) = explode(":", base64_decode($AuthorizedParts[1]));
        if ($username !== 'sonali' || $password !== 'c3BEwphAm') {
            return Services::response()->setStatusCode(400)->setJSON([
                'status' => 'false',
                'message' => 'username & password are incorrect'
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

    }
}