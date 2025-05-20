<template>
    <Head :title="promotion.title" />
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
                <h1 class="text-3xl font-semibold mb-4">
                    {{ promotion.title }}
                </h1>
                <img
                    :src="promotion.image"
                    :alt="promotion.title"
                    class="w-full h-64 object-cover rounded-lg mb-4"
                />
                <p class="text-gray-700">{{ promotion.description }}</p>
                <Link
                    :href="route('welcome')"
                    class="mt-6 inline-block bg-[#FF2D20] text-white px-4 py-2 rounded hover:bg-[#e6251e] transition"
                >
                    Назад
                </Link>
            </div>
        </main>

        <footer class="text-white p-6 text-center">
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
    promotion: Object,
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
