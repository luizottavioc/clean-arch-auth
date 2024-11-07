<?php

declare(strict_types=1);

namespace App\Infra\Http\Controllers;

use App\Application\UseCases\Register\RegisterUser;
use App\Application\UseCases\Register\InputBoundary as InputBoundaryRegister;
use App\Application\Exceptions\Register\EmailRegisteredException;

use App\Application\Exceptions\Register\RegistNumRegisteredException;
use App\Infra\Http\Validator\RegisterControllerValidator;

use App\Infra\Contracts\Request;
use App\Infra\Contracts\Response;
use App\Infra\Http\Conventions\Response as ResponseConvention;

final class RegisterController
{
    public function __construct(
        private RegisterControllerValidator $validator,
        private RegisterUser $useCase
    ) {
    }

    public function handle(Request $request): Response
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
            $output = $this->useCase->handle($inputBoundary);
            return new ResponseConvention(201, [
                'message' => 'User registered successfully',
                'token' => $output->getToken()
            ]);
        } catch (EmailRegisteredException | RegistNumRegisteredException $th) {
            return new ResponseConvention(409, ['message' => $th->getMessage()]);
        } catch (\Throwable $th) {
            return new ResponseConvention(500, ['message' => $th->getMessage()]);
        }
    }
}