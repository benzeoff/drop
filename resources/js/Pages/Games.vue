<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    canPlay: { type: Boolean, required: true },
    nextAttemptTime: { type: String, default: null },
    questions: { type: Array, default: () => [] },
    flash: { type: Object, default: () => ({}) },
});

const currentQuestionIndex = ref(0);
const answers = ref([]);
const feedback = ref(null);

const currentQuestion = computed(() => {
    if (!props.questions || props.questions.length === 0) {
        return { id: null, question: "", options: [] };
    }
    const question = props.questions[currentQuestionIndex.value] || {
        id: null,
        question: "",
        options: [],
    };
    // Убедимся, что options — это массив
    return {
        ...question,
        options: Array.isArray(question.options) ? question.options : [],
    };
});

const submitAnswer = (selectedOption) => {
    if (!currentQuestion.value.id) return;

    answers.value.push({
        question_id: currentQuestion.value.id,
        selected_option: selectedOption,
    });

    if (currentQuestionIndex.value === props.questions.length - 1) {
        const form = useForm({
            answers: answers.value,
        });
        form.post(route("games.quiz.submit"), {
            onSuccess: () => {
                currentQuestionIndex.value = 0;
                answers.value = [];
                feedback.value = null;
            },
        });
    } else {
        currentQuestionIndex.value++;
        feedback.value = "Правильно! Переходим к следующему вопросу.";
    }
};

const timeUntilNextAttempt = computed(() => {
    if (!props.nextAttemptTime) return "";
    const now = new Date();
    const next = new Date(props.nextAttemptTime);
    const diff = next - now;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    return `${hours} часов ${minutes} минут`;
});
</script>

<template>
    <Head title="Игры - Drop Cyber Lounge" />
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
                    <nav class="-mx-3 flex flex-1 justify-end space-x-2">
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
                            :href="route('games')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Игры
                        </Link>
                        <Link
                            :href="route('store')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Магазин
                        </Link>
                        <Link
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Профиль
                        </Link>
                    </nav>
                </header>

                <!-- Main Content -->
                <main class="mt-6">
                    <h1
                        class="text-center text-3xl font-semibold text-black dark:text-white mb-8"
                    >
                        Киберспортивная викторина
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

                    <!-- Quiz Content -->
                    <div
                        class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-6"
                    >
                        <div v-if="!canPlay">
                            <p class="text-center text-black dark:text-white">
                                Вы уже прошли викторину сегодня. Следующая
                                попытка будет доступна через:
                                {{ timeUntilNextAttempt }}.
                            </p>
                        </div>
                        <div v-else-if="questions.length === 0">
                            <p class="text-center text-black dark:text-white">
                                Вопросы не загружены. Пожалуйста, попробуйте
                                позже.
                            </p>
                        </div>
                        <div v-else>
                            <h2
                                class="text-xl font-semibold text-black dark:text-white mb-4"
                            >
                                Вопрос {{ currentQuestionIndex + 1 }} из
                                {{ questions.length }}
                            </h2>
                            <p class="text-black dark:text-white mb-4">
                                {{ currentQuestion.question }}
                            </p>
                            <div class="space-y-2">
                                <button
                                    v-for="(
                                        option, index
                                    ) in currentQuestion.options"
                                    :key="index"
                                    @click="submitAnswer(index)"
                                    class="w-full bg-gray-200 dark:bg-zinc-700 text-black dark:text-white px-4 py-2 rounded hover:bg-gray-300 dark:hover:bg-zinc-600 transition"
                                >
                                    {{ option }}
                                </button>
                            </div>
                            <p
                                v-if="feedback"
                                class="mt-4 text-green-600 dark:text-green-400"
                            >
                                {{ feedback }}
                            </p>
                        </div>
                    </div>
                </main>
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
