<?php

namespace App\DTO;

/**
 * @OA\Schema(
 *     schema="UserDTO",
 *     type="object",
 *     required={"name", "email", "password"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="John Doe",
 *         description="User's name"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="john.doe@example.com",
 *         description="User's email"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         example="secret123",
 *         description="User's password"
 *     )
 * )
 */
class UserDTO
{
    public string $name;
    public string $email;
    public string $password;

    /**
     * UserDTO constructor.
     *
     * @param array $data Array of data with keys: name, email, password.
     */
    public function __construct(array $data)
    {
        $this->name     = $data['name'];
        $this->email    = $data['email'];
        $this->password = $data['password'];
    }

    /**
     * Converts the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }
}
