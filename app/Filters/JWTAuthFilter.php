<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\TokenBlacklistedModel;

class JWTAuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $JwtAuthorization = $request->getServer('HTTP_AUTHORIZATION');

        if (!$JwtAuthorization) {
            return Services::response()->setStatusCode(400)->setJSON([
                'status' => false,
                "message" => 'Unauthorized Access'
            ]);
        }

        $JwtAuthorizationParts = explode(' ', $JwtAuthorization);
        if (count($JwtAuthorizationParts) !== 2 || $JwtAuthorizationParts[0] !== "Bearer") {
            return Services::response()->setStatusCode(400)->setJSON([
                'status' => false,
                "message" => 'Unauthorized Access'
            ]);
        }
        try {
            $decodedData = JWT::decode($JwtAuthorizationParts[1], new Key(getenv("JWT_KEY"), "HS256"));

            $blacklisttoken = new TokenBlacklistedModel();

            $token = $blacklisttoken->where('token', $JwtAuthorizationParts[1])->first();
            if ($token) {
                return Services::response()->setStatusCode(400)->setJSON([
                    'status' => false,
                    "message" => 'Unauthorized Access'
                ]);
            }
            $request->jwtToken = $JwtAuthorizationParts[1];
            $request->userData = (array) $decodedData;
        } catch (Exception $ex) {
            return Services::response()->setStatusCode(400)->setJSON([
                'status' => false,
                "message" => 'Failed to validate token'
            ]);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}