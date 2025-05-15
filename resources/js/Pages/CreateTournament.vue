<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";

// Define props
const props = defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    flash: { type: Object, default: () => ({}) },
    users: { type: Array, default: () => [] },
    teams: { type: Array, default: () => [] },
});

// Fetch users and teams for selection
const fetchParticipants = () => {
    $inertia.get(
        route("tournaments.create"),
        {},
        {
            onSuccess: (page) => {
                form.users = page.props.users || [];
                form.teams = page.props.teams || [];
            },
        }
    );
};

// Initialize form
const form = useForm({
    name: "",
    game: "",
    date: "",
    description: "",
    prize: "",
    max_participants: 2,
    mode: "1v1",
    users: [],
    teams: [],
});

// Watch for mode changes to reset selections
watch(
    () => form.mode,
    (newMode) => {
        if (newMode === "1v1") {
            form.teams = [];
        } else {
            form.users = [];
        }
    }
);

// Fetch participants on mount
onMounted(() => {
    fetchParticipants();
    const currentYearRef = ref(null);
    if (currentYearRef.value) {
        currentYearRef.value.textContent = new Date().getFullYear();
    }
});

// Submit form
const submit = () => {
    form.post(route("tournaments.store"), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Создать турнир - Drop Cyber Lounge" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div
            class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <!-- Header -->
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

                <!-- Main Content -->
                <main class="mt-6">
                    <h1
                        class="text-center text-3xl font-semibold text-black dark:text-white mb-8"
                    >
                        Создать турнир
                    </h1>

                    <!-- Flash Messages -->
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

                    <!-- Form -->
                    <div
                        class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-6"
                    >
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label
                                    for="name"
                                    class="block text-black dark:text-white"
                                    >Название турнира</label
                                >
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-black dark:text-white"
                                    required
                                />
                                <p
                                    v-if="form.errors.name"
                                    class="text-red-600 text-sm mt-1"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>
                            <div>
                                <label
                                    for="game"
                                    class="block text-black dark:text-white"
                                    >Игра</label
                                >
                                <input
                                    id="game"
                                    v-model="form.game"
                                    type="text"
                                    class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-black dark:text-white"
                                    required
                                />
                                <p
                                    v-if="form.errors.game"
                                    class="text-red-600 text-sm mt-1"
                                >
                                    {{ form.errors.game }}
                                </p>
                            </div>
                            <div>
                                <label
                                    for="date"
                                    class="block text-black dark:text-white"
                                    >Дата и время</label
                                >
                                <input
                                    id="date"
                                    v-model="form.date"
                                    type="datetime-local"
                                    class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-black dark:text-white"
                                    required
                                />
                                <p
                                    v-if="form.errors.date"
                                    class="text-red-600 text-sm mt-1"
                                >
                                    {{ form.errors.date }}
                                </p>
                            </div>
                            <div>
                                <label
                                    for="description"
                                    class="block text-black dark:text-white"
                                    >Описание</label
                                >
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-black dark:text-white"
                                    rows="4"
                                ></textarea>
                                <p
                                    v-if="form.errors.description"
                                    class="text-red-600 text-sm mt-1"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>
                            <div>
                                <label
                                    for="prize"
                                    class="block text-black dark:text-white"
                                    >Призы</label
                                >
                                <input
                                    id="prize"
                                    v-model="form.prize"
                                    type="text"
                                    class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-black dark:text-white"
                                />
                                <p
                                    v-if="form.errors.prize"
                                    class="text-red-600 text-sm mt-1"
                                >
                                    {{ form.errors.prize }}
                                </p>
                            </div>
                            <div>
                                <label
                                    for="max_participants"
                                    class="block text-black dark:text-white"
                                    >Максимум участников</label
                                >
                                <input
                                    id="max_participants"
                                    v-model="form.max_participants"
                                    type="number"
                                    min="2"
                                    class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-black dark:text-white"
                                    required
                                />
                                <p
                                    v-if="form.errors.max_participants"
                                    class="text-red-600 text-sm mt-1"
                                >
                                    {{ form.errors.max_participants }}
                                </p>
                            </div>
                            <div>
                                <label
                                    for="mode"
                                    class="block text-black dark:text-white"
                                    >Режим</label
                                >
                                <select
                                    id="mode"
                                    v-model="form.mode"
                                    class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-black dark:text-white"
                                    required
                                >
                                    <option value="1v1">1 на 1</option>
                                    <option value="team">
                                        Команда против команды
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.mode"
                                    class="text-red-600 text-sm mt-1"
                                >
                                    {{ form.errors.mode }}
                                </p>
                            </div>
                            <div v-if="form.mode === '1v1'">
                                <label
                                    for="users"
                                    class="block text-black dark:text-white"
                                    >Участники</label
                                >
                                <select
                                    id="users"
                                    v-model="form.users"
                                    multiple
                                    class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-black dark:text-white"
                                >
                                    <option
                                        v-for="user in users"
                                        :key="user.id"
                                        :value="user.id"
                                    >
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.users"
                                    class="text-red-600 text-sm mt-1"
                                >
                                    {{ form.errors.users }}
                                </p>
                            </div>
                            <div v-if="form.mode === 'team'">
                                <label
                                    for="teams"
                                    class="block text-black dark:text-white"
                                    >Команды</label
                                >
                                <select
                                    id="teams"
                                    v-model="form.teams"
                                    multiple
                                    class="w-full bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-700 rounded-md px-3 py-2 text-black dark:text-white"
                                >
                                    <option
                                        v-for="team in teams"
                                        :key="team.id"
                                        :value="team.id"
                                    >
                                        {{ team.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.teams"
                                    class="text-red-600 text-sm mt-1"
                                >
                                    {{ form.errors.teams }}
                                </p>
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-block bg-[#FF2D20] text-white px-4 py-2 rounded hover:bg-[#CC1A1A] transition"
                            >
                                {{
                                    form.processing
                                        ? "Создание..."
                                        : "Создать турнир"
                                }}
                            </button>
                        </form>
                    </div>
                </main>

                <!-- Footer -->
                <footer class="py-16 text-sm text-black dark:text-white/70">
                    <div
                        class="flex flex-col lg:flex-row justify-between items-start gap-8"
                    >
                        <div class="flex-shrink-0">
                            <iframe
                                src="https://yandex.com/map-widget/v1/?um=constructor%3Ayour_map_id&z=15&ll=50.836496%2C61.667614&mode=placemark"
                                width="300"
                                height="200"
                                frameborder="0"
                                class="rounded-lg"
                            ></iframe>
                        </div>
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
                                    >+7 (904) 206-80-89</a
                                >
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
