<?php

declare(strict_types=1);

namespace App\Infra\Http\Controllers;

use App\Infra\Http\Validator\LoginControllerValidator;

use App\Application\UseCases\Login\LoginUser;
use App\Application\UseCases\Login\InputBoundary as InputBoundaryLogin;
use App\Application\Exceptions\Login\UserNotFoundException;
use App\Application\Exceptions\Login\WrongPasswordException;

use App\Infra\Contracts\Request;
use App\Infra\Contracts\Response;
use App\Infra\Http\Conventions\Response as ResponseConvention;

final class LoginController
{
    public function __construct(
        private LoginControllerValidator $validator,
        private LoginUser $useCase,
    ) {
    }

    public function handle(Request $request): Response
    {
        $data = $request->getBody();

        $reqErrors = $this->validator->validate($data);
        if (!empty($reqErrors)) {
            return new ResponseConvention(400, ['errors' => $reqErrors]);
        }

        $inputBoundary = new InputBoundaryLogin($data['email'], $data['password']);

        try {
            $output = $this->useCase->handle($inputBoundary);
            return new ResponseConvention(200, ['token' => $output->getToken()]);
        } catch (UserNotFoundException | WrongPasswordException $th) {
            return new ResponseConvention(401, ['message' => $th->getMessage()]);
        } catch (\Throwable $th) {
            return new ResponseConvention(500, ['message' => $th->getMessage()]);
        }
    }
}