<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Clínica Más Cerca del Cielo - Atención Humana</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        html {
            scroll-behavior: smooth;
        }

        /* css */
        .hero-image-container {
            position: relative;
            overflow: hidden; 
            background-color: #E0E0E0; 
        }

        .hero-image-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; 
            object-position: center; 
            opacity: 1; 
            inset: 0; 
        }
    </style>
</head>
<body class="antialiased bg-[#F5F5F5]">

    <nav class="bg-[#FFFFFF] shadow-sm fixed w-full top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-[#0A2342]">Clínica Más Cerca del Cielo</a>

            <div class="hidden md:flex items-center space-x-6">
                <a href="#servicios" class="text-[#4B5563] hover:text-[#0A2342] transition duration-300">Servicios</a>
                <a href="#ubicacion" class="text-[#4B5563] hover:text-[#0A2342] transition duration-300">Ubicación</a>
                <a href="#contacto" class="bg-[#4FBDBA] text-[#FFFFFF] px-5 py-2 rounded-lg shadow-sm hover:bg-opacity-80 transition duration-300">
                    Contacto
                </a>

                <span class="h-6 w-px bg-[#D1D5DB]"></span>

                @auth
                    <a href="{{ url('/dashboard') }}" class="text-[#1F2937] font-medium hover:text-[#2E8BC0] transition duration-300">Portal</a>
                @else
                    <a href="{{ route('login') }}" class="text-[#1F2937] font-medium hover:text-[#2E8BC0] transition duration-300">Iniciar Sesión</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-[#1F2937] font-medium hover:text-[#2E8BC0] transition duration-300">Registrarse</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <section class="bg-[#FFFFFF] pt-24 md:pt-32">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 items-center gap-12 px-6 py-16">

            <div class="text-center md:text-left">
                <h1 class="text-5xl font-bold text-[#0A2342] leading-tight">
                    Mala Atención Médica, <br class="hidden md:block"> Totalmente <span class="text-[#2E8BC0]"> ilegal.</span>
                </h1>
                <p class="mt-6 text-lg text-[#4B5563]">
                    Tu bienestar es nuestra prioridad. Agenda tu cita y accede a tu historial médico completo desde nuestro portal de paciente.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="{{ route('register') }}" class="bg-[#4FBDBA] text-[#FFFFFF] font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-opacity-80 transition duration-300 text-center">
                        Agendar Cita
                    </a>
                    <a href="{{ route('login') }}" class="bg-[#E5E7EB] text-[#1F2937] font-semibold py-3 px-6 rounded-lg hover:bg-[#D1D5DB] transition duration-300 text-center">
                        Portal de Paciente
                    </a>
                </div>
            </div>

            {{-- ============ IMAGEN ESTÁTICA (SIN SLIDESHOW) ============ --}}
            <div class="hero-image-container relative w-full h-[300px] md:h-[450px] rounded-lg shadow-2xl overflow-hidden">
                {{-- Dejamos solo la imagen de los cirujanos --}}
                <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?q=80&w=1770&auto=format&fit=crop"
                     alt="Equipo médico profesional">
            </div>

        </div>
    </section>

    <section id="servicios" class="bg-[#F5F5F5] py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center text-[#0A2342] mb-12">Atención Simplificada</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div class="bg-[#FFFFFF] p-8 rounded-lg shadow-lg text-center">
                    <div class="flex justify-center mb-4">
                        <span class="flex items-center justify-center h-16 w-16 rounded-full bg-[#4FBDBA] text-[#FFFFFF]">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFFFFF">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25" />
                            </svg>
                        </span>
                    </div>
                    <h3 class="font-bold text-2xl mb-2 text-[#0A2342]">1. Agenda Desde Casa</h3>
                    <p class="text-[#4B5563]">
                        Regístrate una vez en nuestro portal y agenda tu próxima consulta médica en segundos.
                    </p>
                </div>

                <div class="bg-[#FFFFFF] p-8 rounded-lg shadow-lg text-center">
                    <div class="flex justify-center mb-4">
                        <span class="flex items-center justify-center h-16 w-16 rounded-full bg-[#4FBDBA] text-[#FFFFFF]">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFFFFF">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v5.25m-4.5-5.25H7.5v5.25H3M15 10.5v5.25H12v-5.25H15m4.5-5.25v5.25H16.5v-5.25H19.5" />
                            </svg>
                        </span>
                    </div>
                    <h3 class="font-bold text-2xl mb-2 text-[#0A2342]">2. Atención Eficiente</h3>
                    <p class="text-[#4B5563]">
                        Nuestras instalaciones cuentan con equipos modernos para agilizar diagnósticos y centralizar tus resultados.
                    </p>
                </div>

                <div class="bg-[#FFFFFF] p-8 rounded-lg shadow-lg text-center">
                    <div class="flex justify-center mb-4">
                        <span class="flex items-center justify-center h-16 w-16 rounded-full bg-[#4FBDBA] text-[#FFFFFF]">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFFFFF">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h7.5M8.25 12h7.5m-7.5 5.25h7.5" />
                            </svg>
                        </span>
                    </div>
                    <h3 class="font-bold text-2xl mb-2 text-[#0A2342]">3. Gestión Integral</h3>
                    <p class="text-[#4B5563]">
                        Si requieres un especialista, lo gestionamos. Tu historial y resultados siempre disponibles.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <section id="ubicacion" class="bg-[#FFFFFF] py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-4xl font-bold text-center text-[#0A2342] mb-12">Visítanos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

                <div>
                    <h3 class="text-2xl font-semibold text-[#0A2342] mb-4">Horarios de Atención</h3>
                    <ul class="text-[#4B5563] space-y-2 mb-6">
                        <li><strong>Lunes a Viernes:</strong> 08:00 - 20:00</li>
                        <li><strong>Sábados:</strong> 09:00 - 14:00</li>
                        <li><strong>Domingos y Feriados:</strong> Solo emergencias</li>
                    </ul>

                    <h3 class="text-2xl font-semibold text-[#0A2342] mb-4">Nuestra Ubicación</h3>
                    <p class="text-[#4B5563] mb-2">C. Héroes del Acre, esq N° 1855</p>
                    <p class="text-[#4B5563]">La Paz, Bolivia</p>
                </div>

                <div class="rounded-lg shadow-2xl overflow-hidden h-full min-h-[400px]">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d61206.43501870011!2d-68.1739751!3d-16.5057811!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915f207b3d119039%3A0xecdc050310cd3fe0!2sUnifranz%20La%20Paz!5e0!3m2!1ses-419!2sbo!4v1762825721766!5m2!1ses-419!2sbo" 
                        width="100%" height="100%" style="border:0;"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

            </div>
        </div>
    </section>

    <footer id="contacto" class="bg-[#0A2342] text-[#9CA3AF] py-16">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-[#FFFFFF] mb-6">Ponte en Contacto</h2>
            <p class="text-lg text-[#D1D5DB] mb-8 max-w-2xl mx-auto">
                ¿Tienes preguntas o quieres agendar una cita? Estamos aquí para ayudarte.
            </p>

            <div class="flex flex-col md:flex-row justify-center items-center gap-8 mb-12">
                <div class="text-left">
                    <p class="font-semibold text-[#FFFFFF]">Email:</p>
                    <a href="mailto:info@clinica.com" class="hover:text-[#FFFFFF]">info@clinica.com</a>
                </div>
                <div class="text-left">
                    <p class="font-semibold text-[#FFFFFF]">Teléfono:</p>
                    <a href="tel:+59163002352" class="hover:text-[#FFFFFF]">(+591) 63002352</a>
                </div>
            </div>

            <a href="https://wa.me/59163002352?text=Hola%2C%20quisiera%20agendar%20una%20cita%20en%20la%20Cl%C3%ADnica%20M%C3%A1s%20Cerca%20del%20Cielo." 
               target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center bg-[#25D366] text-[#FFFFFF] font-semibold py-3 px-6 rounded-lg shadow-md hover:bg-opacity-90 transition duration-300">
                <svg class="h-6 w-6 mr-2" fill="#FFFFFF" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.06 21.94L7.31 20.58C8.75 21.38 10.36 21.82 12.04 21.82C17.5 21.82 21.95 17.37 21.95 11.91C21.95 6.45 17.5 2 12.04 2Z"/></svg>
                Chatea con nosotros
            </a>

            <div class="mt-12 border-t border-[#374151] pt-6">
                <p class="text-sm">© 2025 Clínica Más Cerca del Cielo. Todos los derechos reservados.</p>
                <p class="text-xs mt-1">Desarrollado por Jorge y Farid — Financiado por Terry.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>