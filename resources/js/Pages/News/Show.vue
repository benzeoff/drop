<template>
    <Head :title="news.title" />
    <div class="bg-black text-white min-h-screen flex flex-col">
        <header class="py-6 px-6 lg:px-12 flex justify-center items-center">
            <img
                src="/images/drop-logo3.png"
                alt="Drop Club Logo"
                class="h-24 w-auto transition-all hover:scale-105"
            />
            <nav v-if="canLogin" class="ml-auto space-x-4">
                <Link
                    :href="route('about')"
                    class="text-white hover:text-[#FF2D20] px-3 py-2 rounded transition"
                >
                    О клубе
                </Link>
                <Link
                    :href="route('booking')"
                    class="text-white hover:text-[#FF2D20] px-3 py-2 rounded transition"
                >
                    Бронирования
                </Link>
                <Link
                    :href="route('tournaments')"
                    class="text-white hover:text-[#FF2D20] px-3 py-2 rounded transition"
                >
                    Турниры
                </Link>
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-white hover:text-[#FF2D20] px-3 py-2 rounded transition"
                >
                    Профиль
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="text-white hover:text-[#FF2D20] px-3 py-2 rounded transition"
                    >
                        Войти
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="text-white hover:text-[#FF2D20] px-3 py-2 rounded transition"
                    >
                        Регистрация
                    </Link>
                </template>
            </nav>
        </header>

        <main class="flex-grow p-6 lg:p-12">
            <div
                class="bg-white text-black rounded-lg p-6 shadow-lg max-w-3xl mx-auto"
            >
                <h1 class="text-3xl font-semibold mb-4">{{ news.title }}</h1>
                <img
                    :src="news.image"
                    :alt="news.title"
                    class="w-full h-64 object-cover rounded-lg mb-4"
                />
                <p class="text-gray-700">{{ news.description }}</p>
                <Link
                    :href="route('welcome')"
                    class="mt-6 inline-block bg-[#FF2D20] text-white px-4 py-2 rounded hover:bg-[#e6251e] transition"
                >
                    Назад
                </Link>
            </div>
        </main>

        <footer class="bg-[#FF2D20] text-white p-6 text-center">
            <div
                class="flex flex-col lg:flex-row justify-between items-center gap-6"
            >
                <div>
                    <iframe
                        src="https://yandex.ru/map-widget/v1/?ll=50.826198%2C61.677702&mode=poi&poi%5Bpoint%5D=50.825564%2C61.677702&poi%5Buri%5D=ymapsbm1%3A%2F%2Forg%3Foid%3D232821190503&z=18.6"
                        width="300"
                        height="200"
                        frameborder="0"
                        class="rounded-lg"
                    ></iframe>
                </div>
                <div class="flex flex-col items-center gap-4">
                    <div class="flex items-center gap-2">
                        <img
                            src="/images/social/tel.png"
                            alt="Phone Icon"
                            class="w-6 h-6"
                        />
                        <a
                            href="tel:+79042068089"
                            class="underline hover:text-white"
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
            <div class="mt-6">
                Компьютерный клуб © <span ref="currentYearRef"></span>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

defineProps({
    news: Object,
    canLogin: Boolean,
    canRegister: Boolean,
});

const currentYearRef = ref(null);
onMounted(() => {
    if (currentYearRef.value) {
        currentYearRef.value.textContent = new Date().getFullYear();
    }
});
</script>
