<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SIS7BE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased">

<div class="flex min-h-screen">
    <div class="flex-1 flex justify-center items-center p-8 bg-white">
        <div class="w-full max-w-sm space-y-6">
            <div class="text-center">
                <h1 class="text-2xl font-black text-red-600">SIS7BE</h1>
                <flux:heading size="xl" class="mt-2">Log in</flux:heading>
            </div>
            
            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                @csrf
                <flux:input name="email" label="Email" type="text" required autofocus placeholder="username atau email@example.com" />
                <flux:input name="password" label="Password" type="password" placeholder="Password" required viewable />
                <flux:button variant="primary" type="submit" class="w-full">Log in</flux:button>
            </form>
        </div>
    </div>

    <div class="hidden lg:flex flex-1 relative overflow-hidden">
        <img src="{{ asset('images/utama.jpg') }}" class="absolute inset-0 w-full h-full object-cover" alt="Login Background">
        
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
        
    </div>
</div>

</body>
</html>