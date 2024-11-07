<?php

declare(strict_types=1);

namespace App\Infra\Http\Controllers;

use App\Infra\Http\Validator\AuthControllerValidator;

use App\Application\UseCases\Login\LoginUser;
use App\Application\UseCases\Login\InputBoundary as InputBoundaryLogin;
use App\Application\Exceptions\Login\UserNotFoundException;
use App\Application\Exceptions\Login\WrongPasswordException;

use App\Application\UseCases\Register\RegisterUser;
use App\Application\UseCases\Register\InputBoundary as InputBoundaryRegister;
use App\Application\Exceptions\Register\EmailAlreadyRegisteredException;

use App\Infra\Contracts\Request;
use App\Infra\Contracts\Response;
use App\Infra\Http\Conventions\Response as ResponseConvention;

final class AuthController
{
    public function __construct(
        private AuthControllerValidator $validator,
        private LoginUser $useCaseLoginUser,
        private RegisterUser $useCaseRegisterUser
    ) {
    }

    public function handleLogin(Request $request): Response
    {
        $data = $request->getBody();

        $reqErrors = $this->validator->validateLogin($data);
        if (!empty($reqErrors)) {
            return new ResponseConvention(400, ['errors' => $reqErrors]);
        }

        $inputBoundary = new InputBoundaryLogin($data['email'], $data['password']);

        try {
            $output = $this->useCaseLoginUser->handle($inputBoundary);
            return new ResponseConvention(200, ['token' => $output->getToken()]);
        } catch (UserNotFoundException | WrongPasswordException $th) {
            return new ResponseConvention(401, ['message' => $th->getMessage()]);
        } catch (\Throwable $th) {
            return new ResponseConvention(500, ['message' => $th->getMessage()]);
        }
    }

    public function handleRegister(Request $request): Response
    {
        $data = $request->getBody();

        $reqErrors = $this->validator->validateRegister($data);
        if (!empty($reqErrors)) {
            return new ResponseConvention(400, ['errors' => $reqErrors]);
        }
        
        $inputBoundary = new InputBoundaryRegister(
            $data['name'],
            $data['registration_number'],
            $data['email'],
            $data['password']
        );

        try {
            $this->useCaseRegisterUser->handle($inputBoundary);
            return new ResponseConvention(201, ['message' => 'User registered successfully']);
        } catch (EmailAlreadyRegisteredException $th) {
            return new ResponseConvention(409, ['message' => $th->getMessage()]);
        } catch (\Throwable $th) {
            return new ResponseConvention(500, ['message' => $th->getMessage()]);
        }
    }
}