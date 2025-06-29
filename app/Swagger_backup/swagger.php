<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="User Balance API",
 *     description="API для управления балансом пользователей",
 *     @OA\Contact(
 *         email="support@testuserbalanser.com",
 *         name="Support Team"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://balancer/api",
 *     description="Основной сервер API"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */