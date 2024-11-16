<?php

namespace App\Controllers;

use App\Models\AuthModel;

use App\Models\TokenBlacklistedModel;
use Firebase\JWT\JWT;

class AuthController extends BaseController
{
    protected $authmodel;
    protected $tokenblacklistedmodel;
    public function __construct()
    {
        $this->authmodel = new AuthModel();
        $this->tokenblacklistedmodel = new TokenBlacklistedModel();
    }

    // REGISTERATION

    public function registerAuth()
    {
        $validationRules = array(
            "username" => array(
                "rules" => 'required|min_length[5]',
                'errors' => array(
                    "required" => "Username field is required",
                    "min_length" => "Username should have atleast 5 minimum characters"
                )
            ),
            "email" => array(
                "rules" => 'required|min_length[5]|is_unique[authors.email]',
                "errors" => array(
                    "required" => "Email field is required",
                    "min_length" => "Email should have atleast 5 minimum characters",
                    "is_unique" => "Email already exist"
                )
            ),
            "password" => array(
                "rules" => 'required|min_length[5]',
                "errors" => array(
                    "required" => "Password field is required",
                    "min_length" => "Password should have atleast 5 minimum characters"
                )
            ),
            "phone_no" => array(
                "rules" => 'permit_empty|min_length[10]',
                "errors" => array(
                    "permit_empty" => "Phone number is optional",
                    "min_length" => "Phone number should have at least 10 digits",
                )
            ),
        );

        if (!$this->validate($validationRules)) {

            return $this->response->setStatusCode(400)->setJSON([
                'status' => "false",
                "message" => "Form submission failed",
                "errors" => $this->validator->getErrors()
            ]);
        }

        $AuthorizationData = [
            "username" => $this->request->getVar('username'),
            "email" => $this->request->getVar('email'),
            "password" => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            "phone_no" => $this->request->getVar('phone_no')
        ];

        if ($this->authmodel->save($AuthorizationData)) {
            return $this->response->setJSON([
                'status' => "true",
                "message" => "Author registered successfully"
            ]);
        } else {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                "message" => 'Failed to register user'
            ]);
        }
    }

    public function loginAuth()
    {
        $validationRules =
            array(
                "email" => array(
                    "rules" => 'required|min_length[5]',
                    "errors" => array(
                        "required" => "Email field is required",
                        "min_length" => "Email should have minimum 5 characters"
                    )
                ),
                "password" => array(
                    "rules" => 'required|min_length[5]',
                    "errors" => array(
                        "required" => "Password field is required",
                        "min_length" => "Password should have minimum 5 characters"
                    )
                )
            );

        if (!$this->validate($validationRules)) {
            return $this->response->setStatusCode(400)->setJSON([
                "status" => "false",
                "message" => "Failed to login user ",
            ]);
        }

        $authorData = $this->authmodel->where('email', $this->request->getVar('email'))->first();

        if ($authorData) {

            if (password_verify($this->request->getVar('password'), $authorData['password'])) {

                $key = getenv("JWT_KEY");

                $payloadData = [
                    "iss" => "localhost",
                    "aud" => "localhost",
                    "iat" => time(),
                    "exp" => time() + 3600,
                    "user" => [
                        "id" => $authorData['id'],
                        "email" => $authorData['email']
                    ]
                ];
                $token = JWT::encode($payloadData, $key, "HS256");

                return $this->response->setJSON([
                    "status" => "true",
                    "message" => "User loggedin successfully",
                    "token" => $token
                ]);
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => "failed",
                    "message" => "login failed",
                ]);
            }

        } else {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => "failed",
                "message" => "login failed",
            ]);
        }
    }

    public function logoutAuth()
    {
        $token = $this->request->jwtToken;

        if (
            $this->tokenblacklistedmodel->insert([
                'token' => $token,
            ])
        ) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'User logged out successfully'
            ]);
        } else {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => "false",
                'message' => "Failed to blacklist token"
            ]);
        }
    }
}