<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Susan Brigitt Studio</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-[#FAF7F4] min-h-screen">

    <div class="max-w-7xl mx-auto">

        <header class="flex items-center justify-between py-8">

            <div>
                <h1 class="text-3xl font-bold text-[#8B5A5A]">
                    Susan Brigitt
                </h1>

                <p class="text-sm text-gray-500">
                    Studio
                </p>
            </div>

            <nav class="flex gap-8 text-gray-700">
                <a href="#">Inicio</a>
                <a href="#">Gestión</a>
                <a href="#">Contacto</a>
            </nav>

            <button
                class="bg-[#D98F9D] text-white px-6 py-3 rounded-xl">
                Iniciar sesión
            </button>

        </header>

        <section
            class="grid lg:grid-cols-2 gap-12 items-center py-20">

            <div>

                <h2
                    class="text-6xl font-bold leading-tight text-gray-900">

                    Controla tu inventario,
                    ventas y ganancias
                    desde un solo lugar

                </h2>

                <p
                    class="mt-8 text-xl text-gray-600">

                    Plataforma privada para gestionar
                    productos, compras, ventas,
                    inventario disponible y rentabilidad.

                </p>

                <div class="flex gap-4 mt-10">

                    <button
                        class="bg-[#D98F9D] text-white px-8 py-4 rounded-xl">

                        Entrar al sistema

                    </button>

                    <button
                        class="border border-[#D98F9D]
                        text-[#D98F9D]
                        px-8 py-4 rounded-xl">

                        Ver resumen

                    </button>

                </div>

            </div>

            <div
                class="bg-white rounded-3xl p-10 shadow-sm">

                <h3 class="font-semibold text-xl mb-6">
                    Resumen general
                </h3>

                <div class="grid grid-cols-2 gap-4">

                    <div class="bg-[#FAF7F4] p-6 rounded-2xl">
                        <p class="text-sm text-gray-500">
                            Ventas del mes
                        </p>
                        <h4 class="text-3xl font-bold">
                            $1.240
                        </h4>
                    </div>

                    <div class="bg-[#FAF7F4] p-6 rounded-2xl">
                        <p class="text-sm text-gray-500">
                            Inventario
                        </p>
                        <h4 class="text-3xl font-bold">
                            186
                        </h4>
                    </div>

                    <div class="bg-[#FAF7F4] p-6 rounded-2xl">
                        <p class="text-sm text-gray-500">
                            Ganancia
                        </p>
                        <h4 class="text-3xl font-bold">
                            $410
                        </h4>
                    </div>

                    <div class="bg-[#FAF7F4] p-6 rounded-2xl">
                        <p class="text-sm text-gray-500">
                            Tasa BCV
                        </p>
                        <h4 class="text-3xl font-bold">
                            36.92
                        </h4>
                    </div>

                </div>

            </div>

        </section>

    </div>

</body>

</html>