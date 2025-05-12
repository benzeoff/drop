<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

// Пропсы для управления авторизацией
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

// Динамический год для футера
const currentYearRef = ref(null);
onMounted(() => {
    if (currentYearRef.value) {
        currentYearRef.value.textContent = new Date().getFullYear();
    }
});
</script>

<template>
    <Head title="О клубе - Drop Cyber Lounge" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div
            class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <!-- Шапка -->
                <header
                    class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3"
                >
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <img
                            src="/images/drop-logo3.png"
                            alt="Drop Club Logo"
                            class="h-32 w-auto transition-all hover:scale-105 lg:h-36"
                        />
                    </div>
                    <nav
                        v-if="canLogin"
                        class="-mx-3 flex flex-1 justify-end space-x-2"
                    >
                        <Link
                            :href="route('about')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            О клубе
                        </Link>
                        <Link
                            :href="route('booking')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Бронирования
                        </Link>
                        <Link
                            :href="route('tournaments')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Турниры
                        </Link>
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Профиль
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Войти
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Регистрация
                            </Link>
                        </template>
                    </nav>
                </header>

                <!-- Основной контент -->
                <main class="mt-6">
                    <h1
                        class="text-center text-3xl font-semibold text-black dark:text-white mb-8"
                    >
                        О клубе Drop Cyber Lounge
                    </h1>
                    <div
                        class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-6"
                    >
                        <p
                            class="text-sm/relaxed text-black dark:text-white mb-4"
                        >
                            Кибер-лаундж Drop — это современное пространство для
                            геймеров в самом сердце Сыктывкара. Мы предлагаем 20
                            мощных компьютеров с видеокартами RTX 3070,
                            мониторами 144 Гц и удобными креслами, чтобы вы
                            могли полностью погрузиться в игровой процесс.
                        </p>
                        <p
                            class="text-sm/relaxed text-black dark:text-white mb-4"
                        >
                            Наш клуб работает круглосуточно, чтобы вы могли
                            играть в любое время дня и ночи. Мы регулярно
                            проводим турниры по популярным играм, таким как
                            CS:GO, Dota 2 и Valorant, с ценными призами для
                            победителей.
                        </p>
                        <p class="text-sm/relaxed text-black dark:text-white">
                            Присоединяйтесь к нам по адресу: Интернациональная
                            ул., 32, Сыктывкар. Забронируйте место прямо сейчас
                            и станьте частью нашего сообщества!
                        </p>
                    </div>
                </main>

                <!-- Футер -->
                <footer class="py-16 text-sm text-black dark:text-white/70">
                    <div
                        class="flex flex-col lg:flex-row justify-between items-start gap-8"
                    >
                        <!-- Мини-карта (слева) -->
                        <div class="flex-shrink-0">
                            <iframe
                                src="https://yandex.com/map-widget/v1/?um=constructor%3Ayour_map_id&z=15&ll=50.836496%2C61.667614&mode=placemark"
                                width="300"
                                height="200"
                                frameborder="0"
                                class="rounded-lg"
                            ></iframe>
                        </div>

                        <!-- Контакты (справа) -->
                        <div class="flex flex-col items-start gap-4">
                            <div class="flex items-center gap-2">
                                <img
                                    src="/images/social/tel.png"
                                    alt="Phone Icon"
                                    class="w-6 h-6"
                                />
                                <a
                                    href="tel:+79042068089"
                                    class="underline hover:text-[#FF2D20]"
                                >
                                    +7 (904) 206-80-89
                                </a>
                            </div>
                            <a
                                href="https://vk.com/dropgames11?from=groups"
                                target="_blank"
                                class="flex items-center gap-2"
                            >
                                <img
                                    src="/images/social/vk.png"
                                    alt="VK Icon"
                                    class="w-6 h-6"
                                />
                            </a>
                        </div>
                    </div>

                    <!-- Компьютерный клуб © (по центру снизу) -->
                    <div class="mt-8 text-center">
                        Компьютерный клуб © <span ref="currentYearRef"></span>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>
