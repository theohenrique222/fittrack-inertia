<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:create-super-admin')]
#[Description('Criação de usuário super admin')]

class CreateSuperAdmin extends Command
{
    public function handle()
    {
        $name = $this->ask('Digite seu nome 👤');
        $email = $this->ask('Digite seu email 📧');
        $password = $this->secret('Digite sua senha 🔑');

        $user = User::where('email', $email)->first();
        if ($user) {
            $this->error("Já existe um usuário com esse email: {$email}");
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $this->info('✅ Super Admin criado com sucesso! 🎉');
        $this->info("👤 Nome: {$name}");
        $this->info("📧 Email: {$email}");

        return 0;

    }
}
