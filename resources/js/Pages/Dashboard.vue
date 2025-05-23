<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";

// Получаем данные из пропсов с проверкой типов
const props = defineProps({
    auth: {
        type: Object,
        default: () => ({ user: null }),
    },
    bookings: {
        type: Array,
        default: () => [],
    },
    teams: {
        type: Array,
        default: () => [],
    },
    tournaments: {
        type: Array,
        default: () => [],
    },
});

console.log("Dashboard props:", props); // Отладка (оставьте, если нужно)

// Реактивные данные для бронирований и команд
const bookings = ref(props.bookings);
const teams = ref(props.teams);
const selectedTeamId = ref(null);

onMounted(() => {
    bookings.value = props.bookings || [];
    teams.value = props.teams || [];
});

// Фильтрация активных и завершённых бронирований
const activeBookings = computed(() =>
    bookings.value.filter((b) => b?.status === "confirmed")
);
const completedBookings = computed(() =>
    bookings.value.filter(
        (b) => b?.status && ["completed", "cancelled"].includes(b.status)
    )
);

// Форма для продления бронирования
const extendForm = useForm({
    additional_duration: 30,
});

// Форма для подачи заявки
const applyForm = useForm({
    tournament_id: null,
    team_id: null,
});

// Проверяем, является ли пользователь капитаном
const isCaptain = computed(() => {
    const selectedTeam = teams.value.find(
        (team) => team?.id === selectedTeamId.value
    );
    return (
        selectedTeam &&
        props.auth?.user &&
        selectedTeam.captain_id &&
        selectedTeam.captain_id === props.auth.user.id
    );
});

// Метод для подачи заявки
const submitApply = () => {
    if (!props.auth?.user) {
        console.error("User not authenticated");
        return;
    }
    if (!selectedTeamId.value) {
        console.error("No team selected");
        return;
    }
    applyForm.team_id = selectedTeamId.value;
    applyForm.post(route("dashboard.teams.apply", selectedTeamId.value), {
        onSuccess: () => {
            applyForm.reset();
            selectedTeamId.value = null;
        },
        onError: (errors) => {
            console.log(errors);
        },
    });
};
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
                                    :key="booking?.id || Math.random()"
                                    class="border-b border-gray-200 pb-4"
                                >
                                    <p class="text-sm text-gray-600">
                                        Ресурс:
                                        {{
                                            booking?.resource?.name ||
                                            "Не указан"
                                        }}
                                        ({{
                                            booking?.resource?.category ||
                                            booking?.resource?.name ||
                                            "Не указан"
                                        }})
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Время:
                                        {{
                                            booking?.start_time
                                                ? new Date(
                                                      booking.start_time
                                                  ).toLocaleString()
                                                : "Не указано"
                                        }}
                                        -
                                        {{
                                            booking?.end_time
                                                ? new Date(
                                                      booking.end_time
                                                  ).toLocaleString()
                                                : "Не указано"
                                        }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Стоимость: {{ booking?.price || 0 }} ₽
                                    </p>
                                    <div class="mt-2 flex gap-2">
                                        <div class="flex items-center gap-2">
                                            <select
                                                v-model="
                                                    extendForm.additional_duration
                                                "
                                                class="rounded-md border-gray-300 text-sm"
                                                :disabled="!booking?.id"
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
                                                :disabled="!booking?.id"
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
                                            :disabled="!booking?.id"
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
                                    :key="booking?.id || Math.random()"
                                    class="border-b border-gray-200 pb-4"
                                >
                                    <p class="text-sm text-gray-600">
                                        Ресурс:
                                        {{
                                            booking?.resource?.name ||
                                            "Не указан"
                                        }}
                                        ({{
                                            booking?.resource?.category ||
                                            booking?.resource?.name ||
                                            "Не указан"
                                        }})
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Время:
                                        {{
                                            booking?.start_time
                                                ? new Date(
                                                      booking.start_time
                                                  ).toLocaleString()
                                                : "Не указано"
                                        }}
                                        -
                                        {{
                                            booking?.end_time
                                                ? new Date(
                                                      booking.end_time
                                                  ).toLocaleString()
                                                : "Не указано"
                                        }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Стоимость: {{ booking?.price || 0 }} ₽
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Статус:
                                        {{
                                            booking?.status === "completed"
                                                ? "Завершено"
                                                : booking?.status ===
                                                  "cancelled"
                                                ? "Отменено"
                                                : "Не указан"
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

                <!-- Секция команд -->
                <div
                    class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Мои команды
                        </h3>
                        <div v-if="teams.length" class="space-y-4">
                            <div
                                v-for="team in teams"
                                :key="team?.id || Math.random()"
                                class="border-b border-gray-200 pb-4"
                            >
                                <h4 class="text-md font-medium text-gray-700">
                                    {{ team?.name || "Без названия" }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    Капитан:
                                    {{ team?.captain?.name || "Не указан" }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Участники:
                                    {{
                                        team?.users
                                            ?.map((u) => u?.name)
                                            .join(", ") || "Нет участников"
                                    }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Код приглашения:
                                    {{ team?.invite_code || "Не указан" }}
                                </p>
                                <div v-if="auth?.user" class="mt-2 flex gap-2">
                                    <button
                                        v-if="team?.captain_id === auth.user.id"
                                        @click="
                                            $inertia.delete(
                                                route(
                                                    'dashboard.teams.delete',
                                                    team.id
                                                )
                                            )
                                        "
                                        class="rounded-md bg-red-500 px-3 py-1 text-white hover:bg-red-600"
                                        :disabled="!team?.id"
                                    >
                                        Удалить команду
                                    </button>
                                    <button
                                        v-if="
                                            team?.id &&
                                            team.captain_id === auth.user.id
                                        "
                                        @click="
                                            $inertia.get(
                                                route(
                                                    'dashboard.teams.edit',
                                                    team.id
                                                )
                                            )
                                        "
                                        class="rounded-md bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                                        :disabled="!team?.id"
                                    >
                                        Редактировать
                                    </button>
                                    <button
                                        v-if="team?.captain_id !== auth.user.id"
                                        @click="
                                            $inertia.post(
                                                route(
                                                    'dashboard.teams.leave',
                                                    team.id
                                                )
                                            )
                                        "
                                        class="rounded-md bg-red-500 px-3 py-1 text-white hover:bg-red-600"
                                        :disabled="!team?.id"
                                    >
                                        Выйти из команды
                                    </button>
                                    <button
                                        v-if="team?.captain_id === auth.user.id"
                                        @click="selectedTeamId = team.id"
                                        class="rounded-md bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                                        :disabled="!team?.id"
                                    >
                                        Подать заявку
                                    </button>
                                </div>
                                <div v-else class="text-sm text-gray-600">
                                    Пожалуйста, войдите в систему, чтобы
                                    управлять командами.
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-600">
                            У вас нет команд.
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button
                                @click="
                                    $inertia.get(
                                        route('dashboard.teams.create')
                                    )
                                "
                                class="rounded-md bg-green-500 px-3 py-1 text-white hover:bg-green-600"
                            >
                                Создать команду
                            </button>
                            <button
                                @click="
                                    $inertia.get(
                                        route('dashboard.teams.join.form')
                                    )
                                "
                                class="rounded-md bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                            >
                                Присоединиться к команде
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Форма подачи заявки -->
                <div
                    v-if="selectedTeamId"
                    class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Подать заявку на турнир
                        </h3>
                        <form @submit.prevent="submitApply" class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-600"
                                    >Турнир</label
                                >
                                <select
                                    v-model="applyForm.tournament_id"
                                    required
                                    class="w-full p-2 border rounded"
                                >
                                    <option disabled value="">
                                        Выберите турнир
                                    </option>
                                    <option
                                        v-for="tournament in tournaments"
                                        :key="tournament?.id || Math.random()"
                                        :value="tournament?.id"
                                    >
                                        {{ tournament?.name || "Без названия" }}
                                    </option>
                                </select>
                            </div>
                            <button
                                type="submit"
                                :disabled="
                                    !isCaptain.value || !applyForm.tournament_id
                                "
                                class="rounded-md bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                            >
                                Подать заявку
                            </button>
                            <p
                                v-if="!isCaptain.value"
                                class="text-red-500 mt-2"
                            >
                                Только капитан может подать заявку.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
