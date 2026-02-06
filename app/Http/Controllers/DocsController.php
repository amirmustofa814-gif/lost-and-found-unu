<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;
use Illuminate\Http\Request;

#[OA\Info(version: "1.0.0", title: "API Lost & Found", description: "Dokumentasi API")]
#[OA\Server(url: "http://127.0.0.1:8000/api", description: "Local Server")]
class DocsController extends Controller
{
    #[OA\Post(
        path: "/register",
        tags: ["Auth"],
        summary: "Register User Baru",
        operationId: "register",
        description: "Mendaftarkan user dan kirim OTP",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "nim", "email", "phone_number", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Budi"),
                    new OA\Property(property: "nim", type: "string", example: "12345"),
                    new OA\Property(property: "email", type: "string", example: "budi@email.com"),
                    new OA\Property(property: "phone_number", type: "string", example: "08123456789"),
                    new OA\Property(property: "password", type: "string", example: "pass123"),
                    new OA\Property(property: "password_confirmation", type: "string", example: "pass123"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Sukses Register"),
            new OA\Response(response: 422, description: "Gagal Validasi")
        ]
    )]
    public function register()
    {
        // Kosong saja
    }
}