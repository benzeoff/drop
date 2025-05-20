<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    points: { type: Number, required: true },
    items: { type: Array, required: true },
    flash: { type: Object, default: () => ({}) },
});

const purchase = (item) => {
    const form = useForm({
        item_name: item.name,
        points: item.points,
    });
    form.post(route("store.purchase"), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Магазин - Drop Cyber Lounge" />
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
                    <div class="flex justify-between items-center mb-8">
                        <h1
                            class="text-center text-3xl font-semibold text-black dark:text-white"
                        >
                            Магазин
                        </h1>
                        <div class="text-black dark:text-white">
                            Баллы: <span class="font-bold">{{ points }}</span>
                        </div>
                    </div>

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

                    <!-- Store Items -->
                    <div
                        class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-6"
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                v-for="item in items"
                                :key="item.name"
                                class="border border-gray-300 dark:border-zinc-700 rounded-lg p-4"
                            >
                                <h2
                                    class="text-lg font-semibold text-black dark:text-white"
                                >
                                    {{ item.name }}
                                </h2>
                                <p class="text-black dark:text-white">
                                    Стоимость: {{ item.points }} баллов
                                </p>
                                <button
                                    @click="purchase(item)"
                                    class="mt-2 inline-block bg-[#FF2D20] text-white px-4 py-2 rounded hover:bg-[#CC1A1A] transition"
                                >
                                    Купить
                                </button>
                            </div>
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
