<?php

declare(strict_types=1);

namespace App\Infra\Http\Controllers;

use App\Application\Exceptions\CheckAuth\InvalidTokenException;
use App\Infra\Http\Validator\CheckAuthControllerValidator;

use App\Application\UseCases\CheckAuth\CheckAuth;
use App\Application\UseCases\CheckAuth\InputBoundary;

use App\Infra\Contracts\Request;
use App\Infra\Contracts\Response;
use App\Infra\Http\Conventions\Response as ResponseConvention;

class CheckAuthController
{
    public function __construct(
        private CheckAuthControllerValidator $validator,
        private CheckAuth $useCase,
    ) {
    }

    public function handle(Request $request): Response
    {
        $headers = $request->getHeaders();

        $reqErrors = $this->validator->validate($headers);
        if (!empty($reqErrors)) {
            return new ResponseConvention(400, ['errors' => $reqErrors]);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $inputBoundary = new InputBoundary($token);

        try {
            $output = $this->useCase->handle($inputBoundary);
            return new ResponseConvention(
                200,
                ['isAuthenticated' => true, 'user' => $output->getUser()->toArray()]
            );
        } catch (InvalidTokenException $th) {
            return new ResponseConvention(
                401,
                ['isAuthenticated' => false, 'user' => null, 'message' => $th->getMessage()]
            );
        } catch (\Throwable $th) {
            return new ResponseConvention(
                500,
                ['isAuthenticated' => false, 'user' => null, 'message' => $th->getMessage()]
            );
        }
    }
}