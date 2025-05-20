<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import { router } from "@inertiajs/vue3";

// Пропсы
defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    tournaments: { type: Object, required: true },
    games: { type: Array, required: true },
    flash: { type: Object, default: () => ({}) },
});

// Динамический год для футера
const currentYearRef = ref(null);
onMounted(() => {
    if (currentYearRef.value) {
        currentYearRef.value.textContent = new Date().getFullYear();
    }
});

// Фильтр по играм
const selectedGame = ref("all");

// Форма для регистрации
const registerForm = useForm({});

// Форма для генерации матчей
const generateMatchesForm = useForm({});

// Обработка регистрации
const register = (tournamentId) => {
    registerForm.post(route("tournaments.register", tournamentId), {
        preserveScroll: true,
        onSuccess: () => {
            registerForm.reset();
        },
    });
};

// Обработка генерации матчей
const generateMatches = (tournamentId) => {
    generateMatchesForm.post(
        route("tournaments.generate-matches", tournamentId),
        {
            preserveScroll: true,
        }
    );
};

// Обработка изменения фильтра
watch(selectedGame, (newGame) => {
    router.get(
        route("tournaments"),
        { game: newGame },
        { preserveState: true, preserveScroll: true }
    );
});

// Обработка пагинации
const goToPage = (page) => {
    router.get(
        page,
        { game: selectedGame.value },
        { preserveState: true, preserveScroll: true }
    );
};
</script>

<template>
    <Head title="Турниры - Drop Cyber Lounge" />
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
                        <div class="logo transition-all hover:scale-105">
                            <span class="drop-text"
                                >dr<span class="drop-o">o</span>p</span
                            >
                        </div>
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
                        Турниры
                    </h1>

                    <!-- Уведомления -->
                    <div
                        v-if="flash.success"
                        class="mb-4 bg-green-100 text-green-800 p-4 rounded-lg"
                    >
                        {{ flash.success }}
                    </div>
                    <div
                        v-if="flash.error"
                        class="mb-4 bg-red-100 text-red-800 p-4 rounded-lg"
                    >
                        {{ flash.error }}
                    </div>

                    <!-- Фильтр по играм -->
                    <div class="mb-6">
                        <label
                            for="game-filter"
                            class="text-black dark:text-white mr-2"
                            >Турнир по игре:</label
                        >
                        <select
                            id="game-filter"
                            v-model="selectedGame"
                            class="bg-white dark:bg-zinc-900 text-black dark:text-white border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2"
                        >
                            <option value="all">Все игры</option>
                            <option
                                v-for="game in games"
                                :key="game"
                                :value="game"
                            >
                                {{ game }}
                            </option>
                        </select>
                    </div>

                    <!-- Список турниров -->
                    <div
                        v-if="tournaments.data.length"
                        class="grid gap-6 lg:grid-cols-2"
                    >
                        <div
                            v-for="tournament in tournaments.data"
                            :key="tournament.id"
                            class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-6"
                        >
                            <h2
                                class="text-xl font-semibold text-black dark:text-white"
                            >
                                {{ tournament.name }}
                            </h2>
                            <p
                                class="text-sm text-gray-600 dark:text-gray-400 mt-2"
                            >
                                Игра: {{ tournament.game }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Дата:
                                {{
                                    new Date(tournament.date).toLocaleString(
                                        "ru-RU"
                                    )
                                }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Участники: {{ tournament.users_count }} /
                                {{ tournament.max_participants }}
                            </p>
                            <p
                                v-if="tournament.description"
                                class="text-sm text-gray-600 dark:text-gray-400 mt-2"
                            >
                                {{ tournament.description }}
                            </p>
                            <p
                                v-if="tournament.prize"
                                class="text-sm text-gray-600 dark:text-gray-400"
                            >
                                Призы: {{ tournament.prize }}
                            </p>
                            <button
                                v-if="
                                    $page.props.auth.user && !tournament.is_full
                                "
                                @click="register(tournament.id)"
                                :disabled="registerForm.processing"
                                class="mt-4 inline-block bg-[#FF2D20] text-white px-4 py-2 rounded hover:bg-[#CC1A1A] transition"
                            >
                                {{
                                    registerForm.processing
                                        ? "Загрузка..."
                                        : "Зарегистрироваться"
                                }}
                            </button>
                            <p
                                v-else-if="tournament.is_full"
                                class="mt-4 text-sm text-red-600 dark:text-red-400"
                            >
                                Турнир заполнен
                            </p>
                            <p
                                v-else
                                class="mt-4 text-sm text-gray-600 dark:text-gray-400"
                            >
                                <Link
                                    :href="route('login')"
                                    class="text-[#FF2D20] hover:underline"
                                >
                                    Войдите
                                </Link>
                                , чтобы зарегистрироваться.
                            </p>

                            <!-- Кнопка генерации матчей (для админов) -->
                            <button
                                v-if="
                                    $page.props.auth.user?.role === 'admin' &&
                                    tournament.users_count >= 2 &&
                                    !tournament.matches.length
                                "
                                @click="generateMatches(tournament.id)"
                                :disabled="generateMatchesForm.processing"
                                class="mt-4 inline-block bg-[#FF2D20] text-white px-4 py-2 rounded hover:bg-[#CC1A1A] transition"
                            >
                                {{
                                    generateMatchesForm.processing
                                        ? "Генерация..."
                                        : "Сгенерировать матчи"
                                }}
                            </button>

                            <!-- Список матчей -->
                            <div v-if="tournament.matches.length" class="mt-4">
                                <h3
                                    class="text-lg font-semibold text-black dark:text-white"
                                >
                                    Матчи
                                </h3>
                                <ul class="mt-2 space-y-2">
                                    <li
                                        v-for="match in tournament.matches"
                                        :key="match.id"
                                        class="text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        {{ match.player1?.name || "TBD" }} vs
                                        {{ match.player2?.name || "TBD" }} ({{
                                            match.status === "pending"
                                                ? "Ожидается"
                                                : `Счет: ${
                                                      match.player1_score || 0
                                                  } - ${
                                                      match.player2_score || 0
                                                  }`
                                        }})
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div
                        v-else
                        class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-6 text-center"
                    >
                        <p class="text-sm/relaxed text-black dark:text-white">
                            Список предстоящих турниров скоро будет доступен.
                            Следите за обновлениями!
                        </p>
                    </div>

                    <!-- Пагинация -->
                    <div
                        v-if="tournaments.data.length"
                        class="mt-6 flex justify-center"
                    >
                        <div class="flex space-x-2">
                            <button
                                v-for="link in tournaments.links"
                                :key="link.url"
                                @click="goToPage(link.url)"
                                :disabled="!link.url"
                                class="px-4 py-2 rounded-md"
                                :class="{
                                    'bg-[#FF2D20] text-white': link.active,
                                    'bg-white dark:bg-zinc-900 text-black dark:text-white':
                                        !link.active,
                                    'opacity-50 cursor-not-allowed': !link.url,
                                }"
                                v-html="link.label"
                            ></button>
                        </div>
                    </div>
                </main>

                <!-- Футер -->
                <footer class="py-16 text-sm text-black dark:text-white/70">
                    <div class="mt-8 text-center">
                        Компьютерный клуб © <span ref="currentYearRef"></span>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>

<style scoped>
.logo {
    font-family: "Drop", sans-serif;
    font-size: 64px;
    color: #e0e0e0;
    line-height: 96px;
    text-align: center;
}

.drop-text {
    display: inline-block;
}

.drop-o {
    color: #ff4040;
}
</style>
