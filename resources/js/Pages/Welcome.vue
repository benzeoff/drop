<template>
    <Head title="Drop Cyber Lounge">
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
            <nav v-if="canLogin" class="ml-auto flex items-center space-x-4">
                <Link
                    :href="route('about')"
                    class="text-[#E0E0E0] hover:text-[#FF4040] px-3 py-2 rounded transition"
                >
                    О клубе
                </Link>
                <a
                    href="#promotions"
                    @click.prevent="scrollToPromotions"
                    class="text-[#E0E0E0] hover:text-[#FF4040] px-3 py-2 rounded transition"
                >
                    Акции
                </a>
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
                <a
                    href="tel:+79999999999"
                    class="text-[#E0E0E0] hover:text-[#FF4040] px-3 py-2 rounded transition flex items-center gap-2"
                >
                    <svg
                        width="30px"
                        height="30px"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M6.02427 10.2591L13.7368 17.9716C15.4072 19.642 18.1154 19.642 19.7858 17.9716C20.4539 17.3035 20.4539 16.2202 19.7858 15.552L17.6434 13.4096C17.1562 12.9224 16.3663 12.9224 15.8791 13.4096C15.3919 13.8968 14.602 13.8968 14.1148 13.4096L10.2334 9.52819C9.74619 9.041 9.74619 8.25109 10.2334 7.7639C10.7206 7.2767 10.7206 6.4868 10.2334 5.9996L8.44388 4.2101C7.77573 3.54195 6.69243 3.54194 6.02428 4.2101C4.35389 5.88049 4.35389 8.58872 6.02427 10.2591Z"
                            stroke="currentColor"
                            stroke-width="1.6"
                            class="my-path"
                        />
                    </svg>
                    +7 (999) 999-99-99
                </a>
                <Link
                    :href="route('booking')"
                    class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900"
                >
                    Забронировать
                </Link>
            </nav>
        </header>

        <main class="flex-grow p-6 lg:p-12">
            <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                <!-- Карточка "Магазин" -->
                <div
                    class="rounded-lg p-6 shadow-lg"
                    style="
                        background: rgba(30, 30, 30, 0.7);
                        backdrop-filter: blur(10px);
                        -webkit-backdrop-filter: blur(10px);
                    "
                >
                    <img
                        src="/images/store.png"
                        alt="Store"
                        class="w-full aspect-video object-contain"
                    />
                    <div class="p-4">
                        <h2 class="text-xl font-semibold">Магазин</h2>
                        <p class="mt-2 text-sm text-[#A0A0A0]">
                            Зарабатывай баллы в викторинах и обменивай их на
                            время игры или другие награды!
                        </p>
                        <Link
                            :href="route('store')"
                            class="mt-4 inline-block bg-[#FF4040] text-[#E0E0E0] px-4 py-2 rounded hover:bg-[#CC3333] transition"
                        >
                            Перейти в магазин
                        </Link>
                    </div>
                </div>
                <!-- Карточка "Игры" -->
                <div
                    class="rounded-lg p-6 shadow-lg"
                    style="
                        background: rgba(30, 30, 30, 0.7);
                        backdrop-filter: blur(10px);
                        -webkit-backdrop-filter: blur(10px);
                    "
                >
                    <img
                        src="/images/game.png"
                        alt="Games"
                        class="w-full aspect-video object-contain"
                    />
                    <div class="p-4">
                        <h2 class="text-xl font-semibold">Игры</h2>
                        <p class="mt-2 text-sm text-[#A0A0A0]">
                            Пройди киберспортивную викторину и получи баллы для
                            магазина!
                        </p>
                        <Link
                            :href="route('games')"
                            class="mt-4 inline-block bg-[#FF4040] text-[#E0E0E0] px-4 py-2 rounded hover:bg-[#CC3333] transition"
                        >
                            Играть
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Слайдер "Популярные игры" -->
            <section class="mt-12" data-aos="fade-up">
                <h2 class="text-2xl font-semibold text-center mb-6">
                    Популярные игры в клубе
                </h2>
                <div class="relative">
                    <div class="overflow-hidden">
                        <div
                            class="flex transition-transform duration-300 ease-in-out gap-2"
                            :style="{
                                transform: `translateX(-${
                                    currentGameIndex * (100 / 3)
                                }%)`,
                            }"
                        >
                            <div
                                v-for="(game, index) in games"
                                :key="index"
                                class="min-w-[33.333%] flex justify-center"
                            >
                                <div
                                    class="rounded-lg p-4 w-full max-w-md shadow-lg"
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
            <section
                id="promotions"
                class="bg-[#121212] py-12"
                data-aos="fade-up"
            >
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
                                <p class="text-[#A0A0A0] mb-4 text-sm">
                                    {{
                                        truncateDescription(
                                            promotion.description
                                        )
                                    }}
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

            <!-- Секция с картой -->
            <section class="bg-[#181818] py-12 px-4 text-[#E0E0E0]">
                <div class="max-w-7xl mx-auto">
                    <h2 class="text-3xl font-bold text-center mb-8">
                        DROP СЫКТЫВКАР
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
                            <!-- Новая иконка: MapPin -->
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
                                        d="M12 11a4 4 0 100-8 4 4 0 000 8z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 22s8-4.5 8-10a8 8 0 10-16 0c0 5.5 8 10 8 10z"
                                    />
                                </svg>
                                <a
                                    href="https://yandex.ru/maps"
                                    target="_blank"
                                    class="text-[#40C4FF] underline hover:text-[#E0E0E0]"
                                >
                                    Построить маршрут
                                </a>
                            </div>

                            <!-- Новая иконка: Phone -->
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
                                        d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 013.08 4.18 2 2 0 015 2h3a2 2 0 012 1.72 12.22 12.22 0 00.57 2.57 2 2 0 01-.45 2.11l-1.27 1.27a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.22 12.22 0 002.57.57A2 2 0 0122 16.92z"
                                    />
                                </svg>
                                <a
                                    href="tel:+79042068089"
                                    class="text-[#40C4FF] underline hover:text-[#E0E0E0]"
                                >
                                    +7 (999) 999-99-99
                                </a>
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
                                class="w-full aspect-video object-contain"
                            />
                            <div class="p-4">
                                <h3 class="text-lg font-semibold">
                                    {{ component.title }}
                                </h3>
                                <div
                                    class="mt-2 text-sm text-[#A0A0A0] space-y-2"
                                    v-html="component.description"
                                ></div>
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
                    class="absolute top-0 left-0 w-1/2 h-4 bg-[repeating-linear-gradient(-45deg,#A0A0A0_0_10px,#181818_10px_20px)] z-10"
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
                                    class="bg-[#E0E0E0] text-[#E0E0E0] px-8 py-4 rounded-md text-xl font-semibold hover:bg-[#2A2A2A] transition shadow-md flex items-center gap-3"
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
                                    class="bg-[#E0E0E0] text-[#E0E0E0] px-8 py-4 rounded-md text-xl font-semibold hover:bg-[#2A2A2A] transition shadow-md flex items-center gap-3"
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
                            class="text-[#A0A0A0] text-4xl font-bold self-center hidden lg:block"
                        >
                            ★★★★★
                        </div>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 right-0 w-1/2 h-4 bg-[repeating-linear-gradient(-45deg,#A0A0A0_0_10px,#181818_10px_20px)] z-10"
                ></div>
            </section>
        </main>

        <footer class="text-[#E0E0E0] p-6 text-center">
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
const gamesPerSlide = 3;

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
    if (sentences.length === 0) return "";
    return sentences[0] + ".";
}

function scrollToPromotions() {
    const promotionsSection = document.getElementById("promotions");
    if (promotionsSection) {
        promotionsSection.scrollIntoView({ behavior: "smooth" });
    }
}
</script>
