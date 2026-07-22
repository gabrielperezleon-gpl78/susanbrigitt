<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="robots"
        content="noindex, nofollow">

    <title>Acceso | Susan Brigitt Studio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#F8F5F2] text-[#171717]">
    <main class="flex min-h-screen items-center justify-center px-5 py-10">
        <section class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#17191D] text-xl font-semibold text-white shadow-sm">
                    SB
                </div>

                <h1 class="mt-6 text-4xl font-semibold tracking-tight text-zinc-900">
                    Susan Brigitt
                </h1>

                <p class="mt-1 text-xs font-semibold uppercase tracking-[0.38em] text-[#C9A15D]">
                    Studio
                </p>

                <p class="mt-5 text-sm leading-6 text-zinc-500">
                    Ingresa tus credenciales para acceder al panel administrativo.
                </p>
            </div>

            <div class="rounded-3xl border border-black/5 bg-white p-7 shadow-xl shadow-black/5 sm:p-9">
                @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
                @endif

                <form
                    action="{{ route('login.store') }}"
                    method="POST"
                    class="space-y-5">
                    @csrf

                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-semibold text-zinc-700">
                            Correo electrónico
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            autofocus
                            placeholder="correo@ejemplo.com"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3.5 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-300 focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <div>
                        <label
                            for="password"
                            class="mb-2 block text-sm font-semibold text-zinc-700">
                            Contraseña
                        </label>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="Ingresa tu contraseña"
                            class="w-full rounded-xl border border-black/10 bg-white px-4 py-3.5 text-sm text-zinc-900 outline-none transition placeholder:text-zinc-300 focus:border-[#E46F8A] focus:ring-4 focus:ring-[#E46F8A]/10"
                            required>
                    </div>

                    <label class="flex cursor-pointer items-center gap-3">
                        <input
                            name="remember"
                            type="checkbox"
                            value="1"
                            class="h-4 w-4 rounded border-zinc-300 text-[#E46F8A] focus:ring-[#E46F8A]">

                        <span class="text-sm text-zinc-600">
                            Mantener la sesión iniciada
                        </span>
                    </label>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-[#E46F8A] px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#D75E7C] focus:outline-none focus:ring-4 focus:ring-[#E46F8A]/20">
                        Iniciar sesión
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-zinc-400">
                Acceso exclusivo para personal autorizado.
            </p>
        </section>
    </main>
</body>

</html>