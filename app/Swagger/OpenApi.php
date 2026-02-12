<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "API Lost & Found",
    version: "1.0.0",
    description: "Dokumentasi API Kampus",
    contact: new OA\Contact(
        email: "admin@kampus.ac.id"
    )
)]

#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "API Server Lokal"
)]

#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Masukkan token login Anda di sini"
)]

class OpenApi
{
    // File ini hanya untuk deklarasi global OpenAPI
}
