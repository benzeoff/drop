<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";

// Получаем данные из пропсов
const props = defineProps({
    auth: Object,
    bookings: Array,
});

// Реактивные данные для бронирований
const bookings = ref(props.bookings || []);

onMounted(() => {
    bookings.value = props.bookings || [];
});

// Фильтрация активных и завершённых бронирований
const activeBookings = computed(() =>
    bookings.value.filter((b) => b.status === "confirmed")
);
const completedBookings = computed(() =>
    bookings.value.filter((b) => ["completed", "cancelled"].includes(b.status))
);

// Форма для продления бронирования
const extendForm = useForm({
    additional_duration: 30,
});
</script>

<template>
    <Head title="Личный кабинет - Drop Cyber Lounge" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Личный кабинет
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Вы вошли в систему!</div>
                </div>

                <!-- Секция истории бронирований -->
                <div
                    class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            История бронирований
                        </h3>

                        <!-- Активные бронирования -->
                        <div v-if="activeBookings.length" class="mb-6">
                            <h4 class="text-md font-medium text-gray-700 mb-2">
                                Активные бронирования
                            </h4>
                            <div class="space-y-4">
                                <div
                                    v-for="booking in activeBookings"
                                    :key="booking.id"
                                    class="border-b border-gray-200 pb-4"
                                >
                                    <p class="text-sm text-gray-600">
                                        Ресурс: {{ booking.resource.name }} ({{
                                            booking.resource.category ||
                                            booking.resource.name
                                        }})
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Время:
                                        {{
                                            new Date(
                                                booking.start_time
                                            ).toLocaleString()
                                        }}
                                        -
                                        {{
                                            new Date(
                                                booking.end_time
                                            ).toLocaleString()
                                        }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Стоимость: {{ booking.price }} ₽
                                    </p>
                                    <div class="mt-2 flex gap-2">
                                        <div class="flex items-center gap-2">
                                            <select
                                                v-model="
                                                    extendForm.additional_duration
                                                "
                                                class="rounded-md border-gray-300 text-sm"
                                            >
                                                <option
                                                    v-for="duration in [
                                                        30, 60, 180,
                                                    ]"
                                                    :key="duration"
                                                    :value="duration"
                                                >
                                                    {{ duration }} минут
                                                </option>
                                            </select>
                                            <button
                                                @click="
                                                    extendForm.put(
                                                        route(
                                                            'booking.extend',
                                                            booking.id
                                                        )
                                                    )
                                                "
                                                class="rounded-md bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                                            >
                                                Продлить
                                            </button>
                                        </div>
                                        <button
                                            @click="
                                                $inertia.put(
                                                    route(
                                                        'booking.cancel',
                                                        booking.id
                                                    )
                                                )
                                            "
                                            class="rounded-md bg-red-500 px-3 py-1 text-white hover:bg-red-600"
                                        >
                                            Отменить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-600">
                            Нет активных бронирований.
                        </div>

                        <!-- Завершённые бронирования -->
                        <div v-if="completedBookings.length" class="mt-6">
                            <h4 class="text-md font-medium text-gray-700 mb-2">
                                Завершённые бронирования
                            </h4>
                            <div class="space-y-4">
                                <div
                                    v-for="booking in completedBookings"
                                    :key="booking.id"
                                    class="border-b border-gray-200 pb-4"
                                >
                                    <p class="text-sm text-gray-600">
                                        Ресурс: {{ booking.resource.name }} ({{
                                            booking.resource.category ||
                                            booking.resource.name
                                        }})
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Время:
                                        {{
                                            new Date(
                                                booking.start_time
                                            ).toLocaleString()
                                        }}
                                        -
                                        {{
                                            new Date(
                                                booking.end_time
                                            ).toLocaleString()
                                        }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Стоимость: {{ booking.price }} ₽
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Статус:
                                        {{
                                            booking.status === "completed"
                                                ? "Завершено"
                                                : "Отменено"
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-600">
                            Нет завершённых бронирований.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
