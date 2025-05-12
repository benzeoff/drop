<template>
    <Head title="Drop Cyber Lounge">
        <!-- Подключение AOS CSS -->
        <link
            href="https://unpkg.com/aos@2.3.1/dist/aos.css"
            rel="stylesheet"
        />
        <link href="/css/app.css" rel="stylesheet" />
    </Head>
    <div class="bg-[#121212] text-[#E0E0E0] min-h-screen flex flex-col">
        <header
            class="py-6 px-6 lg:px-12 flex justify-center items-center border-b border-[#2A2A2A]"
        >
            <div class="logo transition-all hover:scale-105">
                <span class="drop-text">dr<span class="drop-o">o</span>p</span>
            </div>
            <nav v-if="canLogin" class="ml-auto space-x-4">
                <Link
                    :href="route('about')"
                    class="text-[#E0E0E0] hover:text-[#FF4040] px-3 py-2 rounded transition"
                >
                    О клубе
                </Link>
                <Link
                    :href="route('booking')"
                    class="text-[#E0E0E0] hover:text-[#FF4040] px-3 py-2 rounded transition"
                >
                    Бронирования
                </Link>
                <Link
                    :href="route('tournaments')"
                    class="text-[#E0E0E0] hover:text-[#FF4040] px-3 py-2 rounded transition"
                >
                    Турниры
                </Link>
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-[#E0E0E0] hover:text-[#FF4040] px-3 py-2 rounded transition"
                >
                    Профиль
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="text-[#E0E0E0] hover:text-[#FF4040] px-3 py-2 rounded transition"
                    >
                        Войти
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="text-[#E0E0E0] hover:text-[#FF4040] px-3 py-2 rounded transition"
                    >
                        Регистрация
                    </Link>
                </template>
            </nav>
        </header>

        <main class="flex-grow p-6 lg:p-12">
            <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                <!-- Карточки "О клубе", "Бронирования", "Турниры" -->
                <div
                    class="rounded-lg p-6 shadow-lg"
                    style="
                        background: rgba(30, 30, 30, 0.7);
                        backdrop-filter: blur(10px);
                        -webkit-backdrop-filter: blur(10px);
                    "
                >
                    <img
                        src="/images/drop-zal1.png"
                        alt="Drop Cyber Lounge"
                        class="w-full h-48 object-cover rounded-t-lg"
                    />
                    <div class="p-4">
                        <h2 class="text-xl font-semibold">
                            Добро пожаловать в Drop!
                        </h2>
                        <p class="mt-2 text-sm text-[#A0A0A0]">
                            Кибер-лаундж Drop — это 20 мощных ПК с RTX 3070, 144
                            Гц мониторами и круглосуточным доступом. Бронируй
                            место и участвуй в турнирах!
                        </p>
                        <Link
                            :href="route('about')"
                            class="mt-4 inline-block bg-[#FF4040] text-[#E0E0E0] px-4 py-2 rounded hover:bg-[#CC3333] transition"
                        >
                            Узнать больше
                        </Link>
                    </div>
                </div>
                <div
                    class="rounded-lg p-6 shadow-lg"
                    style="
                        background: rgba(30, 30, 30, 0.7);
                        backdrop-filter: blur(10px);
                        -webkit-backdrop-filter: blur(10px);
                    "
                >
                    <img
                        src="/images/booking.jpg"
                        alt="Booking"
                        class="w-full h-48 object-cover rounded-t-lg"
                    />
                    <div class="p-4">
                        <h2 class="text-xl font-semibold">Забронировать</h2>
                        <p class="mt-2 text-sm text-[#A0A0A0]">
                            Выберите удобную дату и время, а также нужное
                            оборудование для комфортной игры.
                        </p>
                        <Link
                            :href="route('booking')"
                            class="mt-4 inline-block bg-[#FF4040] text-[#E0E0E0] px-4 py-2 rounded hover:bg-[#CC3333] transition"
                        >
                            Бронировать
                        </Link>
                    </div>
                </div>
                <div
                    class="rounded-lg p-6 shadow-lg"
                    style="
                        background: rgba(30, 30, 30, 0.7);
                        backdrop-filter: blur(10px);
                        -webkit-backdrop-filter: blur(10px);
                    "
                >
                    <img
                        src="/images/tournament.jpg"
                        alt="Tournaments"
                        class="w-full h-48 object-cover rounded-t-lg"
                    />
                    <div class="p-4">
                        <h2 class="text-xl font-semibold">Турниры</h2>
                        <p class="mt-2 text-sm text-[#A0A0A0]">
                            Участвуй в турнирах по CS:GO, Dota 2 и другим играм.
                            Выигрывай призы!
                        </p>
                        <Link
                            :href="route('tournaments')"
                            class="mt-4 inline-block bg-[#FF4040] text-[#E0E0E0] px-4 py-2 rounded hover:bg-[#CC3333] transition"
                        >
                            Подробнее
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Слайдер "Популярные игры" -->
            <section class="mt-12" data-aos="fade-up">
                <h2 class="text-2xl font-semibold text-center mb-6">
                    Популярные игры в Drop
                </h2>
                <div class="relative">
                    <div class="overflow-hidden">
                        <div
                            class="flex transition-transform duration-300 ease-in-out"
                            :style="{
                                transform: `translateX(-${
                                    currentGameIndex * 50
                                }%)`,
                            }"
                        >
                            <div
                                v-for="(game, index) in games"
                                :key="index"
                                class="min-w-[50%] flex justify-center"
                            >
                                <div
                                    class="rounded-lg p-4 w-full max-w-md mx-2 shadow-lg"
                                    style="
                                        background: rgba(30, 30, 30, 0.7);
                                        backdrop-filter: blur(10px);
                                        -webkit-backdrop-filter: blur(10px);
                                    "
                                    data-aos="fade-up"
                                    :data-aos-delay="index * 100"
                                >
                                    <img
                                        :src="game.image"
                                        :alt="game.name"
                                        class="w-full h-48 object-cover rounded-md"
                                    />
                                    <h3
                                        class="mt-4 text-center text-lg font-semibold"
                                    >
                                        {{ game.name }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button
                        @click="prevGame"
                        class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-[#FF4040] text-[#E0E0E0] p-2 rounded-full z-10 hover:bg-[#CC3333] transition"
                        v-if="currentGameIndex > 0"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </button>
                    <button
                        @click="nextGame"
                        class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-[#FF4040] text-[#E0E0E0] p-2 rounded-full z-10 hover:bg-[#CC3333] transition"
                        v-if="
                            currentGameIndex <
                            Math.ceil(games.length / gamesPerSlide) - 1
                        "
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>
            </section>

            <!-- Сетка "Акции" -->
            <section class="bg-[#121212] py-12" data-aos="fade-up">
                <div class="max-w-7xl mx-auto px-6 lg:px-12">
                    <h2 class="text-3xl font-bold text-center mb-8">Акции</h2>
                    <div
                        v-if="promotions && promotions.length > 0"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <div
                            v-for="(promotion, index) in promotions"
                            :key="promotion.id"
                            class="rounded-lg shadow-lg overflow-hidden"
                            style="
                                background: rgba(30, 30, 30, 0.7);
                                backdrop-filter: blur(10px);
                                -webkit-backdrop-filter: blur(10px);
                            "
                            data-aos="fade-up"
                            :data-aos-delay="index * 100"
                        >
                            <img
                                :src="promotion.image"
                                :alt="promotion.title"
                                class="w-full h-48 object-cover"
                            />
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2">
                                    {{ promotion.title }}
                                </h3>
                                <p class="text-[#A0A0A0] mb-4">
                                    {{ promotion.description }}
                                </p>
                                <Link
                                    :href="
                                        route('promotions.show', promotion.id)
                                    "
                                    class="inline-block bg-[#FF4040] text-[#E0E0E0] px-6 py-2 rounded-full font-semibold hover:bg-[#CC3333] transition"
                                >
                                    Подробнее
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-[#A0A0A0]">
                        Акции отсутствуют.
                    </div>
                </div>
            </section>

            <!-- Секция "Киберновости" -->
            <section class="mt-12 mb-16">
                <h2 class="text-2xl font-semibold text-center mb-6">
                    Киберновости
                </h2>
                <div class="grid gap-6 lg:grid-cols-3">
                    <div
                        v-for="news in cyberNews"
                        :key="news.id"
                        class="rounded-lg p-6 shadow-lg"
                        style="
                            background: rgba(30, 30, 30, 0.7);
                            backdrop-filter: blur(10px);
                            -webkit-backdrop-filter: blur(10px);
                        "
                    >
                        <img
                            :src="news.image"
                            :alt="news.title"
                            class="w-full h-48 object-cover rounded-t-lg"
                        />
                        <div class="p-4">
                            <h3 class="text-lg font-semibold">
                                {{ news.title }}
                            </h3>
                            <p class="text-sm text-[#A0A0A0] mb-2">
                                {{ formatDate(news.formattedDate) }}
                            </p>
                            <p class="mt-2 text-sm">
                                {{ truncateDescription(news.description) }}
                                <Link
                                    :href="route('news.show', news.id)"
                                    class="text-[#40C4FF] hover:underline"
                                >
                                    Читать далее...
                                </Link>
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Секция "Контакты" -->
            <section class="bg-[#181818] py-12 px-4 text-[#E0E0E0]">
                <div class="max-w-7xl mx-auto">
                    <h2 class="text-3xl font-bold text-center mb-8">
                        DROP CYBER Сыктывкар
                    </h2>
                    <div class="relative rounded-xl overflow-hidden">
                        <iframe
                            src="https://yandex.ru/map-widget/v1/?ll=50.825564%2C61.677677&z=16&poi%5Bpoint%5D=50.825564%2C61.677677&poi%5Buri%5D=ymapsbm1%3A%2F%2Forg%3Foid%3D232821190503"
                            width="100%"
                            height="500"
                            frameborder="0"
                            class="w-full h-[500px]"
                        ></iframe>
                        <div
                            class="absolute top-4 left-4 bg-[#1E1E1E] text-[#E0E0E0] rounded-lg shadow-lg px-6 py-4 flex flex-col lg:flex-row items-center gap-4 z-10"
                        >
                            <div class="flex items-center gap-2">
                                <svg
                                    class="w-5 h-5 text-[#E0E0E0]"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 12.414M12 21c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9c0 1.657-.457 3.21-1.243 4.536"
                                    />
                                </svg>
                                <a
                                    href="https://yandex.ru/maps"
                                    target="_blank"
                                    class="text-[#40C4FF] underline hover:text-[#E0E0E0]"
                                    >Построить маршрут</a
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <svg
                                    class="w-5 h-5 text-[#E0E0E0]"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3 5a2 2 0 012-2h3.586a1 1 0 01.707.293l1.414 1.414A1 1 0 0012.414 5H19a2 2 0 012 2v2"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17 16l-4-4m0 0l-4 4m4-4v9"
                                    />
                                </svg>
                                <a
                                    href="tel:+79042068089"
                                    class="text-[#40C4FF] underline hover:text-[#E0E0E0]"
                                    >+7 (904) 206-80-89</a
                                >
                            </div>
                            <a
                                href="https://taxi.yandex.ru"
                                target="_blank"
                                class="bg-[#FF4040] text-[#E0E0E0] px-4 py-2 rounded hover:bg-[#CC3333] transition"
                            >
                                Вызвать такси
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Секция "Комплектующие" -->
            <section class="mt-12 mb-16">
                <h2 class="text-2xl font-semibold text-center mb-6">
                    Комплектующие
                </h2>
                <div class="flex justify-center space-x-4 mb-6">
                    <button
                        v-for="zone in zones"
                        :key="zone.key"
                        @click="activeZone = zone.key"
                        :class="[
                            'px-4 py-2 rounded transition',
                            activeZone === zone.key
                                ? 'bg-[#FF4040] text-[#E0E0E0]'
                                : 'bg-[#1E1E1E] text-[#E0E0E0] hover:bg-[#2A2A2A] hover:text-[#E0E0E0]',
                        ]"
                    >
                        {{ zone.label }}
                    </button>
                </div>
                <div v-if="componentsByZone[activeZone]?.length">
                    <div class="grid gap-6 lg:grid-cols-3">
                        <div
                            v-for="component in componentsByZone[activeZone]"
                            :key="component.id"
                            class="rounded-lg p-6 shadow-lg"
                            style="
                                background: rgba(30, 30, 30, 0.7);
                                backdrop-filter: blur(10px);
                                -webkit-backdrop-filter: blur(10px);
                            "
                        >
                            <img
                                :src="component.image"
                                :alt="component.title"
                                class="w-full h-48 object-cover rounded-t-lg"
                            />
                            <div class="p-4">
                                <h3 class="text-lg font-semibold">
                                    {{ component.title }}
                                </h3>
                                <p class="mt-2 text-sm text-[#A0A0A0]">
                                    {{ component.description }}
                                </p>
                                <Link
                                    :href="
                                        route('components.show', component.id)
                                    "
                                    class="mt-4 inline-block bg-[#FF4040] text-[#E0E0E0] px-4 py-2 rounded hover:bg-[#CC3333] transition"
                                >
                                    Подробнее
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center text-[#A0A0A0]">
                    Нет комплектующих в этой зоне.
                </div>
            </section>

            <!-- Секция "Отзывы" -->
            <section
                class="bg-[#181818] text-[#E0E0E0] py-24 relative overflow-hidden"
            >
                <div
                    class="absolute top-0 left-0 w-1/2 h-4 bg-[repeating-linear-gradient(-45deg,#40C4FF_0_10px,#181818_10px_20px)] z-10"
                ></div>
                <div class="relative z-20 max-w-7xl mx-auto px-6 lg:px-12">
                    <div
                        class="flex flex-col lg:flex-row justify-between items-start gap-12"
                    >
                        <div>
                            <h2 class="text-5xl font-bold mb-6">Отзывы</h2>
                            <p class="text-2xl mb-8">Читать все отзывы</p>
                            <div class="flex flex-wrap gap-6">
                                <a
                                    href="https://yandex.ru/maps/org/drop/232821190503/?ll=50.825493%2C61.677873&z=17"
                                    target="_blank"
                                    class="bg-[#1E1E1E] text-[#E0E0E0] px-8 py-4 rounded-md text-xl font-semibold hover:bg-[#2A2A2A] transition shadow-md flex items-center gap-3"
                                >
                                    <img
                                        src="/images/social/yandex-logo.svg"
                                        alt="Яндекс"
                                        class="w-16 h-9"
                                    />
                                </a>
                                <a
                                    href="https://2gis.ru/syktyvkar/firm/70000001038697049"
                                    target="_blank"
                                    class="bg-[#1E1E1E] text-[#E0E0E0] px-8 py-4 rounded-md text-xl font-semibold hover:bg-[#2A2A2A] transition shadow-md flex items-center gap-3"
                                >
                                    <img
                                        src="/images/social/2gis-logo.svg"
                                        alt="2ГИС"
                                        class="w-16 h-9"
                                    />
                                </a>
                            </div>
                        </div>
                        <div
                            class="text-[#40C4FF] text-4xl font-bold self-center hidden lg:block"
                        >
                            ★★★★★
                        </div>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 right-0 w-1/2 h-4 bg-[repeating-linear-gradient(-45deg,#40C4FF_0_10px,#181818_10px_20px)] z-10"
                ></div>
            </section>
        </main>

        <footer class="bg-[#181818] text-[#E0E0E0] p-6 text-center">
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
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
    promotions: Array,
    cyberNews: Array,
    componentsByZone: Object,
});

function handleImageError() {
    document.getElementById("screenshot-container")?.classList.add("!hidden");
    document.getElementById("docs-card")?.classList.add("!row-span-1");
    document.getElementById("docs-card-content")?.classList.add("!flex-row");
    document.getElementById("background")?.classList.add("!hidden");
}

const currentYearRef = ref(null);
onMounted(() => {
    if (currentYearRef.value) {
        currentYearRef.value.textContent = new Date().getFullYear();
    }

    // Динамическая загрузка AOS
    const aosScript = document.createElement("script");
    aosScript.src = "https://unpkg.com/aos@2.3.1/dist/aos.js";
    aosScript.async = true;
    aosScript.onload = () => {
        window.AOS.init({
            duration: 800,
            once: true,
        });
    };
    document.body.appendChild(aosScript);
});

const games = ref([
    { name: "CS:GO", image: "/images/game/cs.png" },
    { name: "Dota 2", image: "/images/game/dota.png" },
    { name: "Valorant", image: "/images/game/valorant.png" },
    { name: "FIFA 23", image: "/images/game/fifa23.png" },
    { name: "GTA V", image: "/images/game/gta.png" },
]);
const currentGameIndex = ref(0);
const gamesPerSlide = 2;

function prevGame() {
    currentGameIndex.value = Math.max(currentGameIndex.value - 1, 0);
}

function nextGame() {
    const maxIndex = Math.ceil(games.value.length / gamesPerSlide) - 1;
    currentGameIndex.value = Math.min(currentGameIndex.value + 1, maxIndex);
}

const zones = [
    { key: "general", label: "Общий зал" },
    { key: "vip", label: "VIP" },
    { key: "bootcamp", label: "BOOTCAMP" },
    { key: "chill", label: "CHILL ZONE" },
];

const activeZone = ref("general");

function formatDate(dateString) {
    return dateString ? dateString : "Нет даты";
}

function truncateDescription(description) {
    if (!description) return "";
    const sentences = description.split(". ").filter((s) => s.trim());
    if (sentences.length <= 2) return description;
    return sentences.slice(0, 2).join(". ") + ".";
}
</script>
